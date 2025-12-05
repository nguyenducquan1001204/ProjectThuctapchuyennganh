<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Hiển thị giao diện chat
     */
    public function index()
    {
        return view('admin.chat.index');
    }

    /**
     * Xử lý tin nhắn từ người dùng (sẽ tích hợp AI sau)
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userMessage = $request->input('message');
        
        // TODO: Tích hợp AI ở đây
        // Hiện tại chỉ trả về phản hồi mẫu
        $response = [
            'message' => 'Xin chào! Tôi là chatbot hỗ trợ. Hiện tại tôi đang được phát triển. Câu hỏi của bạn: "' . $userMessage . '"',
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];

        return response()->json($response);
    }
}

