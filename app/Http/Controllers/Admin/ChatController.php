<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use App\Models\ChatConversation;
use App\Services\ChatBot\DeepSeekClient;
use App\Services\ChatBot\ContextRetriever;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    protected DeepSeekClient $deepSeekClient;
    protected ContextRetriever $contextRetriever;

    public function __construct(DeepSeekClient $deepSeekClient, ContextRetriever $contextRetriever)
    {
        $this->deepSeekClient = $deepSeekClient;
        $this->contextRetriever = $contextRetriever;
    }

    /**
     * Hiển thị giao diện chat
     */
    public function index()
    {
        $user = Auth::user();
        
        // Lấy danh sách cuộc trò chuyện của user
        $conversations = ChatConversation::where('userid', $user->userid)
            ->orderBy('updatedat', 'desc')
            ->get();
        
        // Lấy avatar URL của user
        $userAvatarUrl = $user->avatar 
            ? asset('storage/'.$user->avatar) 
            : asset('assets/img/cropped_circle_image.png');
        
        return view('admin.chat.index', compact('conversations', 'userAvatarUrl'));
    }

    /**
     * Lấy danh sách cuộc trò chuyện
     */
    public function getConversations()
    {
        $user = Auth::user();
        
        $conversations = ChatConversation::where('userid', $user->userid)
            ->orderBy('updatedat', 'desc')
            ->get()
            ->map(function ($conversation) {
                $lastMessage = $conversation->getLastMessage();
                return [
                    'conversationid' => $conversation->conversationid,
                    'title' => $conversation->title ?: 'Cuộc trò chuyện mới',
                    'last_message' => $lastMessage ? substr($lastMessage->message, 0, 50) : '',
                    'updatedat' => $conversation->updatedat->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                    'createdat' => $conversation->createdat->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                ];
            });
        
        return response()->json($conversations);
    }

    /**
     * Tạo cuộc trò chuyện mới
     */
    public function createConversation(Request $request)
    {
        $user = Auth::user();
        
        $conversationId = 'conv_' . time() . '_' . uniqid();
        
        $conversation = ChatConversation::create([
            'conversationid' => $conversationId,
            'userid' => $user->userid,
            'title' => 'Cuộc trò chuyện mới',
            'createdat' => now(),
            'updatedat' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'conversationid' => $conversationId,
            'conversation' => [
                'conversationid' => $conversation->conversationid,
                'title' => $conversation->title,
                'createdat' => $conversation->createdat->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Lấy lịch sử tin nhắn của một cuộc trò chuyện
     */
    public function getMessages($conversationId)
    {
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập
        $conversation = ChatConversation::where('conversationid', $conversationId)
            ->where('userid', $user->userid)
            ->firstOrFail();
        
            $messages = ChatHistory::where('conversationid', $conversationId)
                ->orderBy('createdat', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'role' => $message->role,
                        'content' => $message->message,
                        'timestamp' => $message->createdat->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                    ];
                });
        
        return response()->json([
            'conversation' => [
                'conversationid' => $conversation->conversationid,
                'title' => $conversation->title,
            ],
            'messages' => $messages
        ]);
    }

    /**
     * Xử lý tin nhắn từ người dùng và trả lời bằng AI
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversationid' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();
            $userMessage = $request->input('message');
            $conversationId = $request->input('conversationid');
            
            // Tạo conversation mới nếu chưa có
            if (!$conversationId) {
                $conversationId = 'conv_' . time() . '_' . uniqid();
                ChatConversation::create([
                    'conversationid' => $conversationId,
                    'userid' => $user->userid,
                    'title' => $this->generateConversationTitle($userMessage),
                    'createdat' => now(),
                    'updatedat' => now(),
                ]);
            } else {
                // Kiểm tra quyền truy cập
                $conversation = ChatConversation::where('conversationid', $conversationId)
                    ->where('userid', $user->userid)
                    ->firstOrFail();
                
                // Cập nhật thời gian
                $conversation->updatedat = now();
                $conversation->save();
            }
            
            // Lấy lịch sử cuộc trò chuyện từ database
            $conversationHistory = ChatHistory::where('conversationid', $conversationId)
                ->orderBy('createdat', 'asc')
                ->get()
                ->map(function ($message) {
                    return [
                        'role' => $message->role,
                        'content' => $message->message,
                    ];
                })
                ->toArray();
            
            // Giới hạn lịch sử để tránh quá dài (chỉ lấy 20 tin nhắn gần nhất)
            if (count($conversationHistory) > 20) {
                $conversationHistory = array_slice($conversationHistory, -20);
            }
            
            // Lấy context từ database dựa trên câu hỏi
            try {
                $context = $this->contextRetriever->getContext($userMessage);
            } catch (\Exception $e) {
                Log::error('Context Retrieval Error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Sử dụng context rỗng nếu có lỗi
                $context = [];
            }
            
            // Gửi tin nhắn đến DeepSeek
            try {
                $aiResponse = $this->deepSeekClient->chat($userMessage, $context, $conversationHistory);
            } catch (\Exception $e) {
                Log::error('DeepSeek API Error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
            // Lưu tin nhắn người dùng vào database
            ChatHistory::create([
                'userid' => $user->userid,
                'conversationid' => $conversationId,
                'role' => 'user',
                'message' => $userMessage,
                'createdat' => now(),
            ]);
            
            // Lưu phản hồi AI vào database
            ChatHistory::create([
                'userid' => $user->userid,
                'conversationid' => $conversationId,
                'role' => 'assistant',
                'message' => $aiResponse,
                'createdat' => now(),
            ]);
            
            $response = [
                'message' => $aiResponse,
                'timestamp' => now()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                'conversationid' => $conversationId,
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Chat Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_message' => $userMessage ?? 'N/A',
            ]);

            $errorMessage = 'Xin lỗi, đã có lỗi xảy ra khi xử lý câu hỏi của bạn. ';
            
            if (str_contains($e->getMessage(), 'API key')) {
                $errorMessage .= 'Vui lòng kiểm tra cấu hình API key trong file .env.';
            } elseif (str_contains($e->getMessage(), 'Connection') || str_contains($e->getMessage(), 'timeout')) {
                $errorMessage .= 'Không thể kết nối đến API. Vui lòng kiểm tra kết nối mạng và thử lại sau.';
            } elseif (str_contains($e->getMessage(), 'database') || str_contains($e->getMessage(), 'SQL')) {
                $errorMessage .= 'Có lỗi khi truy vấn database. Vui lòng thử lại sau.';
            } else {
                $errorMessage .= 'Vui lòng thử lại sau. Nếu lỗi vẫn tiếp tục, vui lòng liên hệ quản trị viên.';
            }

            return response()->json([
                'message' => $errorMessage,
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ], 500);
        }
    }

    /**
     * Xóa cuộc trò chuyện
     */
    public function deleteConversation($conversationId)
    {
        $user = Auth::user();
        
        $conversation = ChatConversation::where('conversationid', $conversationId)
            ->where('userid', $user->userid)
            ->firstOrFail();
        
        // Xóa tất cả tin nhắn (cascade sẽ tự động xóa)
        ChatHistory::where('conversationid', $conversationId)->delete();
        
        // Xóa conversation
        $conversation->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Xóa tất cả cuộc trò chuyện
     */
    public function deleteAllConversations()
    {
        $user = Auth::user();
        
        // Lấy tất cả conversation của user
        $conversationIds = ChatConversation::where('userid', $user->userid)
            ->pluck('conversationid');
        
        // Xóa tất cả tin nhắn
        ChatHistory::whereIn('conversationid', $conversationIds)->delete();
        
        // Xóa tất cả conversation
        ChatConversation::where('userid', $user->userid)->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Cập nhật tiêu đề cuộc trò chuyện
     */
    public function updateConversationTitle(Request $request, $conversationId)
    {
        $user = Auth::user();
        
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $conversation = ChatConversation::where('conversationid', $conversationId)
            ->where('userid', $user->userid)
            ->firstOrFail();
        
        $conversation->title = $request->input('title');
        $conversation->save();
        
        return response()->json(['success' => true]);
    }

    /**
     * Tạo tiêu đề tự động từ tin nhắn đầu tiên
     */
    private function generateConversationTitle(string $message): string
    {
        $title = mb_substr(trim($message), 0, 50);
        if (mb_strlen($message) > 50) {
            $title .= '...';
        }
        return $title ?: 'Cuộc trò chuyện mới';
    }
}

