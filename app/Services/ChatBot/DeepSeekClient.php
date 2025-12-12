<?php

namespace App\Services\ChatBot;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekClient
{
    private string $apiKey;
    private string $baseUrl = 'https://openrouter.ai/api/v1';
    private string $model = 'tngtech/deepseek-r1t2-chimera:free';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
    }

    /**
     * Gửi tin nhắn đến DeepSeek và nhận phản hồi
     *
     * @param string $message
     * @param array $context Thông tin context từ database
     * @param array $conversationHistory Lịch sử cuộc trò chuyện
     * @return string
     * @throws \Exception
     */
    public function chat(string $message, array $context = [], array $conversationHistory = []): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenRouter API key chưa được cấu hình. Vui lòng kiểm tra file .env');
        }

        // Xây dựng system prompt với context về database
        $systemPrompt = $this->buildSystemPrompt($context);

        // Xây dựng messages array
        $messages = [];
        
        // Thêm system message
        if (!empty($systemPrompt)) {
            $messages[] = [
                'role' => 'system',
                'content' => $systemPrompt
            ];
        }

        // Thêm lịch sử cuộc trò chuyện
        foreach ($conversationHistory as $history) {
            $messages[] = [
                'role' => $history['role'] ?? 'user',
                'content' => $history['content'] ?? ''
            ];
        }

        // Thêm tin nhắn hiện tại
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(30)->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 1500,
                'stream' => false,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['choices'][0]['message']['content'])) {
                    $content = trim($data['choices'][0]['message']['content']);
                    // Loại bỏ các ký tự markdown * và #
                    $content = $this->removeMarkdownChars($content);
                    return $content;
                }
                
                throw new \Exception('Không nhận được phản hồi từ API');
            } else {
                $error = $response->json();
                $errorMessage = $error['error']['message'] ?? 'Lỗi không xác định từ OpenRouter API';
                Log::error('OpenRouter API Error', [
                    'status' => $response->status(),
                    'error' => $error
                ]);
                throw new \Exception("Lỗi API: {$errorMessage}");
            }
        } catch (\Exception $e) {
            Log::error('DeepSeek Client Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Xây dựng system prompt với thông tin về database
     *
     * @param array $context
     * @return string
     */
    private function buildSystemPrompt(array $context): string
    {
        $prompt = "Bạn là một chatbot hỗ trợ chuyên về hệ thống quản lý lương cho giáo viên. ";
        $prompt .= "Bạn có thể trả lời BẤT CỨ câu hỏi nào liên quan đến cơ sở dữ liệu, cấu trúc bảng, relationships, dữ liệu, và cách query.\n\n";
        
        $prompt .= "=== THÔNG TIN VỀ HỆ THỐNG ===\n";
        $prompt .= "- Hệ thống quản lý lương cho giáo viên\n";
        $prompt .= "- Các vai trò: Admin (Quản trị viên), Accounting (Kế toán), Teacher (Giáo viên)\n\n";

        // Thêm schema database chi tiết (rút gọn)
        if (isset($context['database_schema']) && is_array($context['database_schema'])) {
            $prompt .= "=== CẤU TRÚC CƠ SỞ DỮ LIỆU ===\n";
            $prompt .= "Các bảng chính: " . implode(', ', array_keys($context['database_schema'])) . "\n";
            $prompt .= "Chi tiết từng bảng:\n";
            foreach ($context['database_schema'] as $tableName => $tableInfo) {
                $prompt .= "• {$tableName}: {$tableInfo['description']} (PK: {$tableInfo['primary_key']})\n";
                $columnNames = array_keys($tableInfo['columns']);
                $prompt .= "  Cột: " . implode(', ', array_slice($columnNames, 0, 10));
                if (count($columnNames) > 10) {
                    $prompt .= "... (+" . (count($columnNames) - 10) . " cột khác)";
                }
                $prompt .= "\n";
            }
            $prompt .= "\n";
        }

        // Thêm relationships (rút gọn)
        if (isset($context['database_relationships']) && is_array($context['database_relationships'])) {
            $prompt .= "=== QUAN HỆ GIỮA CÁC BẢNG ===\n";
            foreach ($context['database_relationships'] as $tableName => $relationships) {
                $relations = [];
                if (!empty($relationships['belongs_to'])) {
                    $relations[] = "belongs_to: " . implode(', ', array_keys($relationships['belongs_to']));
                }
                if (!empty($relationships['has_many'])) {
                    $relations[] = "has_many: " . implode(', ', array_keys($relationships['has_many']));
                }
                if (!empty($relations)) {
                    $prompt .= "• {$tableName}: " . implode(' | ', $relations) . "\n";
                }
            }
            $prompt .= "\n";
        }

        // Thêm thống kê
        if (isset($context['database_statistics']) && is_array($context['database_statistics'])) {
            $prompt .= "=== THỐNG KÊ DỮ LIỆU HIỆN TẠI ===\n";
            foreach ($context['database_statistics'] as $key => $value) {
                $prompt .= "- {$key}: {$value}\n";
            }
            $prompt .= "\n";
        }

        // Thêm context cụ thể từ câu hỏi
        $contextKeysToSkip = ['database_schema', 'database_relationships', 'database_statistics'];
        $hasSpecificContext = false;
        foreach ($context as $key => $value) {
            if (!in_array($key, $contextKeysToSkip) && !empty($value)) {
                if (!$hasSpecificContext) {
                    $prompt .= "=== THÔNG TIN LIÊN QUAN ĐẾN CÂU HỎI ===\n";
                    $hasSpecificContext = true;
                }
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }
                $prompt .= "{$key}: {$value}\n";
            }
        }
        if ($hasSpecificContext) {
            $prompt .= "\n";
        }

        $prompt .= "=== HƯỚNG DẪN TRẢ LỜI ===\n";
        $prompt .= "1. Khi người dùng YÊU CẦU DỮ LIỆU CỤ THỂ (ví dụ: 'danh sách giáo viên', 'tên giáo viên', 'thống kê'):\n";
        $prompt .= "   - CHỈ HIỂN THỊ KẾT QUẢ TRỰC TIẾP, KHÔNG hiển thị câu lệnh SQL hay code\n";
        $prompt .= "   - Trình bày kết quả rõ ràng, dễ đọc (danh sách, bảng, số liệu)\n";
        $prompt .= "   - Nếu có dữ liệu trong context, hãy sử dụng ngay\n\n";
        
        $prompt .= "2. Khi người dùng HỎI CÁCH LÀM (ví dụ: 'làm sao để', 'cách query', 'hướng dẫn'):\n";
        $prompt .= "   - Mới giải thích cách thực hiện\n";
        $prompt .= "   - Có thể đưa ra ví dụ SQL hoặc Eloquent ORM\n\n";
        
        $prompt .= "3. Bạn có thể trả lời về:\n";
        $prompt .= "   - Cấu trúc các bảng (tên bảng, các cột, kiểu dữ liệu)\n";
        $prompt .= "   - Quan hệ giữa các bảng (foreign keys, relationships)\n";
        $prompt .= "   - Thống kê và số liệu từ database\n";
        $prompt .= "   - Giải thích các trường dữ liệu và ý nghĩa của chúng\n\n";
        
        $prompt .= "4. Luôn trả lời:\n";
        $prompt .= "   - Chính xác dựa trên schema và relationships đã cung cấp\n";
        $prompt .= "   - Rõ ràng, dễ hiểu, ngắn gọn\n";
        $prompt .= "   - Bằng tiếng Việt\n";
        $prompt .= "   - KHÔNG hiển thị SQL/code khi người dùng chỉ yêu cầu dữ liệu\n\n";

        return $prompt;
    }

    /**
     * Loại bỏ các ký tự markdown (* và #) khỏi nội dung
     *
     * @param string $content
     * @return string
     */
    private function removeMarkdownChars(string $content): string
    {
        // Loại bỏ các dấu # ở đầu dòng (headings)
        $content = preg_replace('/^#+\s*/m', '', $content);
        
        // Loại bỏ các dấu * (bold, italic) nhưng giữ lại nội dung
        $content = preg_replace('/\*{1,3}([^*]+)\*{1,3}/', '$1', $content);
        
        // Loại bỏ các dấu * đơn lẻ không khớp
        $content = preg_replace('/\*+/', '', $content);
        
        // Loại bỏ các dấu # còn sót lại
        $content = str_replace('#', '', $content);
        
        // Làm sạch khoảng trắng thừa
        $content = preg_replace('/\n{3,}/', "\n\n", $content);
        $content = trim($content);
        
        return $content;
    }
}

