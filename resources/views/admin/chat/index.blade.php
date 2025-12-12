@extends('layouts.admin')

@section('title', 'Chatbot Hỗ trợ')

@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Chatbot Hỗ trợ</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active">Chatbot Hỗ trợ</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<section class="chat-section">
    <div class="container-fluid pt-0 pb-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card chat-main-card" id="chat3">
                    <div class="card-body chat-card-body">
                        <div class="row g-0">
                            <!-- Lịch sử chat - Cột trái -->
                            <div class="col-md-4 col-lg-3 col-xl-3 chat-sidebar">
                                <div class="p-3 h-100 d-flex flex-column">
                                    <!-- Sidebar Header -->
                                    <div class="chat-sidebar-header mb-3">
                                        <h6 class="mb-0">Lịch sử trò chuyện</h6>
                                        <div class="chat-header-actions">
                                            <a href="#" class="btn btn-primary btn-sm rounded-pill text-white" 
                                                id="newConversationBtn" 
                                                title="Tạo cuộc trò chuyện mới">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm rounded-pill text-white" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#delete_all_conversations"
                                                id="clearChatBtn" 
                                                title="Xóa toàn bộ lịch sử trò chuyện">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Search -->
                                    <div class="chat-search-container mb-3">
                                        <div class="input-group rounded">
                                            <span class="input-group-text border-0 chat-search-icon">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="search" class="form-control rounded chat-search-input" id="searchConversations" 
                                                placeholder="Tìm kiếm cuộc trò chuyện..." aria-label="Search" />
                                        </div>
                                    </div>

                                    <!-- Danh sách lịch sử chat -->
                                    <div class="chat-history-container flex-grow-1" id="historyScrollContainer">
                                        <ul class="list-unstyled mb-0" id="conversationList">
                                            <!-- Danh sách sẽ được load từ database -->
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Chat Area - Cột phải -->
                            <div class="col-md-8 col-lg-9 col-xl-9 chat-main-area">
                                <!-- Chat Header -->
                                <div class="chat-header-area">
                                    <div class="chat-header-user">
                                        <div class="chat-header-avatar-wrapper">
                                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava6-bg.webp"
                                                alt="avatar" class="chat-header-avatar">
                                            <span class="badge-dot badge-dot-online"></span>
                                        </div>
                                        <div class="chat-header-info">
                                            <h5 class="chat-header-name">Chatbot Hỗ trợ</h5>
                                            <p class="chat-header-status">Đang hoạt động</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chat Messages -->
                                <div class="chat-messages-container" id="chatMessages">
                                    <div class="chat-welcome">
                                        <div class="welcome-icon-wrapper">
                                            <i class="fas fa-comments welcome-icon"></i>
                                        </div>
                                        <h4 class="welcome-title">Xin chào! 👋</h4>
                                        <p class="welcome-text">Tôi là chatbot hỗ trợ. Tôi có thể giúp bạn:</p>
                                        <ul class="welcome-list">
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Tìm hiểu về cơ sở dữ liệu</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Trả lời câu hỏi về hệ thống</span>
                                            </li>
                                            <li>
                                                <i class="fas fa-check-circle"></i>
                                                <span>Hướng dẫn sử dụng các chức năng</span>
                                            </li>
                                        </ul>
                                        <p class="welcome-footer">Hãy đặt câu hỏi để bắt đầu!</p>
                                    </div>
                                </div>

                                <!-- Chat Input -->
                                <div class="chat-input-area">
                                    <div class="chat-input-wrapper">
                                        <div class="chat-input-avatar">
                                            <img src="{{ $userAvatarUrl }}"
                                                alt="avatar" class="input-avatar">
                                        </div>
                                        <form id="chatForm" class="chat-form">
                                            @csrf
                                            <div class="chat-input-container">
                                                <textarea 
                                                    id="messageInput"
                                                    class="form-control chat-input" 
                                                    placeholder="Nhập câu hỏi của bạn..."
                                                    autocomplete="off"
                                                    maxlength="2000"
                                                    rows="1"
                                                ></textarea>
                                                <button type="submit" class="btn btn-send" id="sendBtn" title="Gửi tin nhắn">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Conversation Modal -->
<div class="modal fade chat-modal modal-delete" id="delete_conversation" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteConversationForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa cuộc trò chuyện <strong id="delete_conversation_name"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteConversationBtn">Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete All Conversations Modal -->
<div class="modal fade chat-modal modal-delete" id="delete_all_conversations" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteAllConversationsForm" method="post">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa toàn bộ lịch sử trò chuyện?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteAllBtn">Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Chat Section Background */
    .chat-section {
        background: transparent;
        min-height: calc(100vh - 200px);
        padding: 0;
        margin-top: -20px;
    }

    /* Main Card */
    .chat-main-card {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        background: white;
    }

    .chat-card-body {
        padding: 0 !important;
    }

    /* Sidebar Styles */
    .chat-sidebar {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        border-right: 1px solid #e5e7eb;
        height: 650px;
    }

    /* Sidebar Header */
    .chat-sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .chat-sidebar-header h6 {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
        margin: 0;
    }

    .chat-header-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .chat-sidebar-header .btn-link {
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .chat-sidebar-header .btn-link:hover {
        transform: scale(1.1);
        color: #dc3545 !important;
    }

    /* Search Container */
    .chat-search-container {
        position: relative;
    }

    .chat-search-input {
        border: 2px solid #e5e7eb;
        border-radius: 12px !important;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        transition: all 0.3s ease;
    }

    .chat-search-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .chat-search-icon {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        background: transparent;
        border: none;
        z-index: 10;
        color: #94a3b8;
    }

    .input-group-text.chat-search-icon {
        border: none;
        background: transparent;
    }

    /* History Container */
    .chat-history-container {
        position: relative;
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        scroll-behavior: smooth;
    }

    .chat-history-container::-webkit-scrollbar {
        width: 6px;
    }

    .chat-history-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-history-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .chat-history-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Conversation Item */
    .conversation-item {
        position: relative;
        margin-bottom: 4px;
        border-radius: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .conversation-item:hover {
        background: linear-gradient(90deg, #f0f4ff 0%, #e0e7ff 100%);
        transform: translateX(4px);
    }

    .conversation-item:hover .conversation-delete-btn {
        opacity: 1;
        visibility: visible;
    }

    .conversation-item.active {
        background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .conversation-item.active .conversation-link {
        color: white;
    }

    .conversation-item.active .conversation-name,
    .conversation-item.active .conversation-preview,
    .conversation-item.active .conversation-time {
        color: white !important;
    }

    .conversation-link {
        display: flex;
        align-items: center;
        padding: 12px 40px 12px 16px;
        text-decoration: none;
        color: inherit;
        gap: 12px;
    }

    .conversation-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .conversation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .badge-dot {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
        z-index: 1;
    }

    .badge-dot-online {
        background: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
    }

    .conversation-info {
        flex: 1;
        min-width: 0;
    }

    .conversation-name {
        font-weight: 600;
        font-size: 0.95rem;
        margin: 0;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conversation-preview {
        font-size: 0.85rem;
        color: #64748b;
        margin: 4px 0 0 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conversation-meta {
        flex-shrink: 0;
        text-align: right;
    }

    .conversation-time {
        font-size: 0.75rem;
        color: #94a3b8;
        white-space: nowrap;
    }

    /* Conversation Delete Button */
    .conversation-delete-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 10;
        text-decoration: none;
    }

    .conversation-item:hover .conversation-delete-btn {
        opacity: 1;
        visibility: visible;
    }

    /* Chat Main Area */
    .chat-main-area {
        background: #ffffff;
        display: flex;
        flex-direction: column;
        height: 650px;
    }

    /* Chat Header */
    .chat-header-area {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-header-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chat-header-avatar-wrapper {
        position: relative;
    }

    .chat-header-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .chat-header-info {
        color: white;
    }

    .chat-header-name {
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0;
        color: white;
    }

    .chat-header-status {
        font-size: 0.85rem;
        margin: 4px 0 0 0;
        color: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .chat-header-action {
        color: rgba(255, 255, 255, 0.9) !important;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 8px 12px;
        transition: all 0.3s ease;
    }

    .chat-header-action:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white !important;
        transform: scale(1.05);
    }

    /* Messages Container */
    .chat-messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 50%);
        scroll-behavior: smooth;
    }

    .chat-messages-container::-webkit-scrollbar {
        width: 8px;
    }

    .chat-messages-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .chat-messages-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Welcome Message */
    .chat-welcome {
        text-align: center;
        padding: 60px 40px;
        max-width: 500px;
        margin: 0 auto;
    }

    .welcome-icon-wrapper {
        margin-bottom: 24px;
    }

    .welcome-icon {
        font-size: 5rem;
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .welcome-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
    }

    .welcome-text {
        font-size: 1rem;
        color: #64748b;
        margin-bottom: 24px;
    }

    .welcome-list {
        list-style: none;
        padding: 0;
        margin: 0 0 24px 0;
        text-align: left;
    }

    .welcome-list li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        font-size: 0.95rem;
        color: #475569;
    }

    .welcome-list li i {
        color: #10b981;
        font-size: 1.1rem;
    }

    .welcome-footer {
        font-size: 0.95rem;
        color: #64748b;
        margin: 0;
        font-weight: 500;
    }

    /* Message Bubbles */
    .message-row {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        margin-bottom: 16px;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-user {
        justify-content: flex-end;
    }

    .message-bot {
        justify-content: flex-start;
    }

    .message-bubble {
        padding: 12px 16px;
        border-radius: 18px;
        max-width: 80%;
        word-wrap: break-word;
        line-height: 1.5;
        font-size: 0.95rem;
    }

    .message-user .message-bubble {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: white;
        border-radius: 18px 18px 4px 18px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        margin-left: auto;
    }

    .message-bot .message-bubble {
        background: white;
        color: #1e293b;
        border-radius: 18px 18px 18px 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    .message-time {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 6px;
        padding: 0 4px;
    }

    .message-user .message-time {
        text-align: right;
    }

    .message-bot .message-time {
        text-align: left;
    }

    /* Typing Indicator */
    .typing-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 12px 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #94a3b8;
        animation: typing 1.4s infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.7;
        }
        30% {
            transform: translateY(-8px);
            opacity: 1;
        }
    }

    /* Input Area */
    .chat-input-area {
        padding: 20px 16px;
        background: white;
        border-top: 1px solid #e5e7eb;
    }

    .chat-input-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    .input-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        flex-shrink: 0;
        border: 2px solid #e5e7eb;
    }

    .chat-form {
        flex: 1;
        min-width: 0;
        display: flex;
        width: 100%;
    }

    .chat-input-container {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: flex-end;
        background: #f8fafc;
        border: 2px solid #e5e7eb;
        border-radius: 24px;
        padding: 8px 20px;
        transition: all 0.3s ease;
        width: 100%;
        max-width: 100%;
    }

    .chat-input-container:focus-within {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        background: white;
    }

    .chat-input {
        border: none;
        background: transparent;
        flex: 1;
        padding: 6px 0;
        margin-right: 8px;
        font-size: 0.95rem;
        outline: none;
        resize: none;
        overflow-y: auto;
        min-height: 0 !important;
        max-height: 35px;
        line-height: 1.5;
        font-family: inherit;
        width: 100%;
    }

    textarea.form-control.chat-input {
        min-height: 0 !important;
    }

    .chat-input::placeholder {
        color: #94a3b8;
    }

    .btn-send {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    .btn-send:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }

    .btn-send:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .btn-send.loading i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* Form Control Override */
    #chat3 .form-control {
        border-color: transparent;
    }

    #chat3 .form-control:focus {
        border-color: transparent;
        box-shadow: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chat-main-area,
        .chat-history-container {
            height: 400px !important;
        }

        .chat-welcome {
            padding: 40px 20px;
        }

        .welcome-icon {
            font-size: 3.5rem;
        }

        .welcome-title {
            font-size: 1.5rem;
        }
    }

    /* Chat Modal Styles */
    .chat-modal .modal-content {
        border: none;
        border-radius: .9rem;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
    }

    .chat-modal .modal-header {
        background: linear-gradient(135deg, #2563eb, #1e40af);
        border-bottom: none;
        color: #fff;
    }

    .chat-modal .modal-title {
        font-weight: 600;
        letter-spacing: 0.01rem;
        color: #fff;
    }

    .chat-modal .modal-body {
        background: #f8fafc;
        padding: 1.75rem;
    }

    .chat-modal .modal-footer {
        background: #f1f5f9;
        border-top: none;
    }

    /* Chat Modal Delete Styles */
    .chat-modal.modal-delete .modal-header {
        background: linear-gradient(135deg, #f87171, #dc2626);
    }

    .chat-modal.modal-delete .modal-footer {
        background: #fef2f2;
    }

    .chat-modal .btn {
        border-radius: 999px;
        padding-inline: 1.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const chatMessages = $('#chatMessages');
        const chatForm = $('#chatForm');
        const messageInput = $('#messageInput');
        const sendBtn = $('#sendBtn');
        const clearChatBtn = $('#clearChatBtn');
        const conversationList = $('#conversationList');
        let currentConversationId = null;
        let messageHistory = [];

        // Load danh sách conversations từ database
        function loadConversations() {
            $.ajax({
                url: '{{ route("admin.chat.conversations") }}',
                method: 'GET',
                success: function(conversations) {
                    conversationList.empty();
                    
                    if (conversations.length === 0) {
                        // Nếu chưa có conversation nào, hiển thị welcome
                        showWelcome();
                    } else {
                        // Render danh sách conversations (mới nhất ở trên)
                        // Controller đã trả về theo thứ tự desc, nên append từng cái sẽ có mới nhất ở trên
                        conversations.forEach(function(conv) {
                            addConversationToList(conv, false);
                        });
                    }
                },
                error: function() {
                    console.error('Lỗi khi load danh sách conversations');
                }
            });
        }

        // Avatar của user
        const userAvatarUrl = '{{ $userAvatarUrl }}';
        const botAvatarUrl = 'https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava6-bg.webp';

        // Thêm conversation vào danh sách
        function addConversationToList(conversation, setActive = false) {
            const isActive = setActive || (currentConversationId === conversation.conversationid);
            const activeClass = isActive ? 'active' : '';
            
            // Parse timestamp từ server (format: Y-m-d H:i:s đã ở timezone VN)
            // Thêm timezone vào string để JavaScript parse đúng
            const updatedDate = new Date(conversation.updatedat.replace(' ', 'T') + '+07:00');
            const time = updatedDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Ho_Chi_Minh' });
            const date = updatedDate.toLocaleDateString('vi-VN', { day: '2-digit', month: 'short', timeZone: 'Asia/Ho_Chi_Minh' });
            
            const conversationHtml = `
                <li class="conversation-item ${activeClass}" data-conversation-id="${conversation.conversationid}">
                    <a href="#!" class="conversation-link">
                        <div class="conversation-avatar-wrapper">
                            <img src="${userAvatarUrl}"
                                alt="avatar" class="conversation-avatar">
                            <span class="badge-dot badge-dot-online"></span>
                        </div>
                        <div class="conversation-info">
                            <p class="conversation-name">${escapeHtml(conversation.title)}</p>
                            <p class="conversation-preview">${escapeHtml(conversation.last_message || 'Chưa có tin nhắn')}</p>
                        </div>
                        <div class="conversation-meta">
                            <span class="conversation-time">${time}</span>
                        </div>
                    </a>
                    <a href="#" class="btn btn-danger btn-sm rounded-pill text-white conversation-delete-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#delete_conversation"
                        data-conversation-id="${conversation.conversationid}"
                        title="Xóa cuộc trò chuyện này">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </li>
            `;
            
            // Sử dụng append để thêm vào cuối danh sách (mới nhất ở trên do controller đã sort desc)
            conversationList.append(conversationHtml);
            
            if (setActive) {
                currentConversationId = conversation.conversationid;
            }
        }

        // Escape HTML để tránh XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Auto scroll to bottom
        function scrollToBottom() {
            chatMessages.scrollTop(chatMessages[0].scrollHeight);
        }

        // Add message to chat
        function addMessage(message, isUser = true, timestamp = null) {
            $('.chat-welcome').remove();

            const avatarSrc = isUser 
                ? userAvatarUrl
                : botAvatarUrl;
            
            // Parse timestamp từ server hoặc dùng thời gian hiện tại
            let messageDate;
            if (timestamp) {
                // Timestamp từ server (format: Y-m-d H:i:s đã ở timezone VN)
                // Thêm timezone vào string để JavaScript parse đúng
                messageDate = new Date(timestamp.replace(' ', 'T') + '+07:00');
            } else {
                // Thời gian hiện tại của browser
                messageDate = new Date();
            }
            const time = messageDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Ho_Chi_Minh' });
            const date = messageDate.toLocaleDateString('vi-VN', { day: '2-digit', month: 'short', timeZone: 'Asia/Ho_Chi_Minh' });

            const messageClass = isUser ? 'message-user' : 'message-bot';

            const messageHtml = `
                <div class="d-flex flex-row ${isUser ? 'justify-content-end' : 'justify-content-start'} ${messageClass} message-row">
                    ${!isUser ? `<img src="${avatarSrc}" alt="avatar" style="width: 45px; height: 45px; border-radius: 50%;" class="me-2">` : ''}
                    <div>
                        <div class="message-bubble">${escapeHtml(message).replace(/\n/g, '<br>')}</div>
                        <div class="message-time">${time} | ${date}</div>
                    </div>
                    ${isUser ? `<img src="${avatarSrc}" alt="avatar" style="width: 45px; height: 45px; border-radius: 50%;" class="ms-2">` : ''}
                </div>
            `;

            chatMessages.append(messageHtml);
            scrollToBottom();

            messageHistory.push({
                message: message,
                isUser: isUser,
                timestamp: messageDate
            });
        }

        // Reload conversations list
        function reloadConversations() {
            loadConversations();
        }

        // Show typing indicator
        function showTyping() {
            const typingHtml = `
                <div class="d-flex flex-row justify-content-start mb-3" id="typingIndicator">
                    <img src="${botAvatarUrl}" 
                        alt="avatar" style="width: 45px; height: 45px; border-radius: 50%;" class="me-2">
                    <div class="typing-indicator">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            `;
            chatMessages.append(typingHtml);
            scrollToBottom();
        }

        // Hide typing indicator
        function hideTyping() {
            $('#typingIndicator').remove();
        }

        // Hiển thị welcome screen
        function showWelcome() {
            chatMessages.html(`
                <div class="chat-welcome">
                    <div class="welcome-icon-wrapper">
                        <i class="fas fa-comments welcome-icon"></i>
                    </div>
                    <h4 class="welcome-title">Xin chào! 👋</h4>
                    <p class="welcome-text">Tôi là chatbot hỗ trợ. Tôi có thể giúp bạn:</p>
                    <ul class="welcome-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Tìm hiểu về cơ sở dữ liệu</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Trả lời câu hỏi về hệ thống</span>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span>Hướng dẫn sử dụng các chức năng</span>
                        </li>
                    </ul>
                    <p class="welcome-footer">Hãy đặt câu hỏi để bắt đầu!</p>
                </div>
            `);
        }

        // Load conversation từ database
        function loadConversation(conversationId) {
            if (!conversationId) {
                showWelcome();
                return;
            }

            $.ajax({
                url: `{{ url('admin/chat/conversation') }}/${conversationId}/messages`,
                method: 'GET',
                success: function(data) {
                    chatMessages.empty();
                    messageHistory = [];
                    
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(function(msg) {
                            addMessage(msg.content, msg.role === 'user', msg.timestamp);
                        });
                    } else {
                        showWelcome();
                    }
                    
                    // Cập nhật active state
                    conversationList.find('.conversation-item').removeClass('active');
                    conversationList.find(`[data-conversation-id="${conversationId}"]`).addClass('active');
                },
                error: function() {
                    console.error('Lỗi khi load conversation');
                    showWelcome();
                }
            });
        }

        // Handle form submit
        chatForm.on('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.val().trim();
            if (!message) return;

            addMessage(message, true);
            messageInput.val('');
            messageInput.css('height', 'auto');
            
            messageInput.prop('disabled', true);
            sendBtn.prop('disabled', true).addClass('loading');

            showTyping();

            $.ajax({
                url: '{{ route("admin.chat.send") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    message: message,
                    conversationid: currentConversationId
                },
                success: function(response) {
                    hideTyping();
                    addMessage(response.message, false);
                    
                    // Cập nhật conversation ID nếu là conversation mới
                    if (response.conversationid && !currentConversationId) {
                        currentConversationId = response.conversationid;
                        // Reload danh sách conversations
                        reloadConversations();
                    }
                },
                error: function(xhr) {
                    hideTyping();
                    let errorMessage = 'Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    addMessage(errorMessage, false);
                },
                complete: function() {
                    messageInput.prop('disabled', false).focus();
                    sendBtn.prop('disabled', false).removeClass('loading');
                }
            });
        });

        // Auto resize textarea
        function autoResizeTextarea() {
            const textarea = messageInput[0];
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 35) + 'px';
        }

        messageInput.on('input', function() {
            autoResizeTextarea();
        });

        // Handle Enter key
        messageInput.on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.submit();
            } else if (e.key === 'Enter' && e.shiftKey) {
                // Cho phép xuống dòng với Shift+Enter
                setTimeout(function() {
                    autoResizeTextarea();
                }, 0);
            }
        });

        // New conversation button handler
        $('#newConversationBtn').on('click', function(e) {
            e.preventDefault();
            
            // Reset conversation hiện tại
            currentConversationId = null;
            messageHistory = [];
            showWelcome();
            
            // Remove active state
            conversationList.find('.conversation-item').removeClass('active');
            
            // Focus input
            messageInput.focus();
        });

        // Delete single conversation - Set data when button clicked
        conversationList.on('click', '.conversation-delete-btn', function(e) {
            e.stopPropagation(); // Ngăn event bubbling
            
            const conversationId = $(this).data('conversation-id');
            const conversationItem = $(this).closest('.conversation-item');
            const conversationName = conversationItem.find('.conversation-name').text() || 'này';
            
            // Set data for modal
            $('#delete_conversation_name').text(conversationName);
            $('#confirmDeleteConversationBtn').data('conversation-id', conversationId);
            $('#confirmDeleteConversationBtn').data('conversation-item', conversationItem);
        });

        // Confirm delete single conversation
        $('#confirmDeleteConversationBtn').on('click', function() {
            const conversationId = $(this).data('conversation-id');
            const conversationItem = $(this).data('conversation-item');
            
            // Close modal
            $('#delete_conversation').modal('hide');
            
            // Xóa conversation từ database
            $.ajax({
                url: `{{ url('admin/chat/conversation') }}/${conversationId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    conversationItem.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Nếu conversation đang active bị xóa, reset về welcome
                        if (currentConversationId === conversationId) {
                            currentConversationId = null;
                            messageHistory = [];
                            showWelcome();
                        }
                    });
                },
                error: function() {
                    alert('Có lỗi xảy ra khi xóa cuộc trò chuyện');
                }
            });
        });

        // Confirm delete all conversations
        $('#confirmDeleteAllBtn').on('click', function() {
            // Close modal
            $('#delete_all_conversations').modal('hide');
            
            // Xóa tất cả conversations từ database
            $.ajax({
                url: '{{ route("admin.chat.conversations.deleteAll") }}',
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    // Xóa tất cả conversation items
                    conversationList.find('.conversation-item').fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Reset conversation hiện tại
                    currentConversationId = null;
                    messageHistory = [];
                    showWelcome();
                },
                error: function() {
                    alert('Có lỗi xảy ra khi xóa tất cả cuộc trò chuyện');
                }
            });
        });

        // Conversation click handler
        conversationList.on('click', '.conversation-item', function(e) {
            // Không xử lý nếu click vào nút xóa
            if ($(e.target).closest('.conversation-delete-btn').length) {
                return;
            }
            
            const conversationId = $(this).data('conversation-id');
            
            conversationList.find('.conversation-item').removeClass('active');
            $(this).addClass('active');
            
            currentConversationId = conversationId;
            loadConversation(conversationId);
        });
        
        // Load conversations khi trang được load
        loadConversations();

        // Search conversations
        $('#searchConversations').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            conversationList.find('.conversation-item').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(searchTerm));
            });
        });

        // Focus input on load
        messageInput.focus();
    });
</script>
@endpush
