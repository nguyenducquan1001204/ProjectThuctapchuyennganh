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

    public function index()
    {
        $user = Auth::user();
        
        $conversations = ChatConversation::where('userid', $user->userid)
            ->orderBy('updatedat', 'desc')
            ->get();
        
        $userAvatarUrl = $user->avatar 
            ? asset('storage/'.$user->avatar) 
            : asset('assets/img/cropped_circle_image.png');
        
        return view('admin.chat.index', compact('conversations', 'userAvatarUrl'));
    }

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

    public function getMessages($conversationId)
    {
        $user = Auth::user();
        
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
                $conversation = ChatConversation::where('conversationid', $conversationId)
                    ->where('userid', $user->userid)
                    ->firstOrFail();
                
                $conversation->updatedat = now();
                $conversation->save();
            }
            
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
            
            if (count($conversationHistory) > 20) {
                $conversationHistory = array_slice($conversationHistory, -20);
            }
            
            try {
                $context = $this->contextRetriever->getContext($userMessage);
            } catch (\Exception $e) {
                Log::error('Context Retrieval Error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $context = [];
            }
            
            try {
                $aiResponse = $this->deepSeekClient->chat($userMessage, $context, $conversationHistory);
            } catch (\Exception $e) {
                Log::error('DeepSeek API Error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
            
            ChatHistory::create([
                'userid' => $user->userid,
                'conversationid' => $conversationId,
                'role' => 'user',
                'message' => $userMessage,
                'createdat' => now(),
            ]);
            
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

    public function deleteConversation($conversationId)
    {
        $user = Auth::user();
        
        $conversation = ChatConversation::where('conversationid', $conversationId)
            ->where('userid', $user->userid)
            ->firstOrFail();
        
        ChatHistory::where('conversationid', $conversationId)->delete();
        
        $conversation->delete();
        
        return response()->json(['success' => true]);
    }

    public function deleteAllConversations()
    {
        $user = Auth::user();
        
        $conversationIds = ChatConversation::where('userid', $user->userid)
            ->pluck('conversationid');
        
        ChatHistory::whereIn('conversationid', $conversationIds)->delete();
        
        ChatConversation::where('userid', $user->userid)->delete();
        
        return response()->json(['success' => true]);
    }

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

    private function generateConversationTitle(string $message): string
    {
        $title = mb_substr(trim($message), 0, 50);
        if (mb_strlen($message) > 50) {
            $title .= '...';
        }
        return $title ?: 'Cuộc trò chuyện mới';
    }
}

