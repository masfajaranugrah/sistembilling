@extends('layouts/layoutMaster')

@section('title', 'Chat Admin')

@use('Illuminate\Support\Facades\Auth')

@section('vendor-style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('vendor-script')
@vite(['resources/js/bootstrap.js', 'resources/js/echo.js'])
@endsection

@section('page-style')
<style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .admin-chat-container {
            display: flex;
            width: 100%;
            height: calc(100vh - 120px);
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px auto;
            max-width: 1400px;
        }
        
        .users-sidebar {
            width: 380px;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            background: #f9fafb;
        }
        
        .sidebar-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 28px 24px;
        }
        
        .admin-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 12px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }
        
        .admin-info h2 {
            font-size: 20px;
            margin-bottom: 6px;
            font-weight: 600;
        }
        
        .admin-status {
            font-size: 13px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .search-box {
            padding: 16px;
            background: white;
        }
        
        .search-wrapper {
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .user-list {
            flex: 1;
            overflow-y: auto;
            padding: 8px;
        }
        
        .user-list::-webkit-scrollbar {
            width: 6px;
        }
        
        .user-list::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .user-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        .user-item {
            padding: 14px 12px;
            margin-bottom: 6px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
            border: 1px solid transparent;
        }
        
        .user-item:hover {
            background: #f3f4f6;
            border-color: #e5e7eb;
        }
        
        .user-item.active {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-color: #667eea;
        }
        
        .user-item-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .user-details {
            flex: 1;
            min-width: 0;
        }
        
        .user-name {
            font-weight: 600;
            margin-bottom: 3px;
            font-size: 15px;
            color: #111827;
        }
        
        .user-type {
            font-size: 12px;
            color: #6b7280;
        }
        
        .unread-badge {
            background: #ef4444;
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 10px;
            min-width: 20px;
            text-align: center;
        }
        
        .chat-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .chat-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .chat-subtitle {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .chat-actions {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: #f9fafb;
        }
        
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        
        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        .message {
            margin-bottom: 16px;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            animation: slideIn 0.2s ease;
        }
        
        .message.sent {
            justify-content: flex-end;
        }
        
        .message.received {
            justify-content: flex-start;
        }
        
        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }
        
        .message.sent .message-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            order: 2;
        }
        
        .message.received .message-avatar {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .message-bubble {
            max-width: 60%;
        }
        
        .message-content {
            padding: 12px 16px;
            border-radius: 16px;
            word-wrap: break-word;
        }
        
        .message.sent .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .message.received .message-content {
            background: white;
            color: #111827;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .message-text {
            font-size: 14px;
            line-height: 1.5;
        }
        
        .message-info {
            font-size: 11px;
            opacity: 0.75;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .message.sent .message-info {
            justify-content: flex-end;
        }
        .chat-input-container {
            padding: 12px 16px;
            background: white;
            border-top: 1px solid #e5e7eb;
            margin-bottom: 60px;
        }
        
        .chat-input-form {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }
        
        .input-wrapper {
            flex: 1;
            position: relative;
        }
        
        .chat-input {
            width: 100%;
            padding: 10px 45px 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
            background: #f9fafb;
            resize: none;
            max-height: 100px;
        }
        
        .chat-input:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .emoji-button {
            position: absolute;
            right: 14px;
            bottom: 14px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.5;
            transition: opacity 0.2s;
        }
        
        .emoji-button:hover {
            opacity: 1;
        }
        
        .send-button {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        
        .send-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .send-button:active {
            transform: scale(0.95);
        }
        
        .send-button:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }
        
        .no-chat-selected {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            gap: 16px;
            color: #6b7280;
        }
        
        .no-chat-icon {
            font-size: 64px;
            color: #d1d5db;
        }
                .no-chat-text {
            color: #64748b;
            font-size: 18px;
            font-weight: 500;
        }
        
        .no-chat-subtext {
            color: #94a3b8;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
<div class="admin-chat-container">
        <div class="users-sidebar">
            <div class="sidebar-header">
                <div class="admin-info">
                    <div class="admin-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h2>Admin Panel</h2>
                    <div class="admin-status">
                        <span class="status-dot"></span>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
            
            <div class="search-box">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Cari user...">
                </div>
            </div>
            
            <div class="user-list" id="userList">
                @foreach($users as $user)
                <div class="user-item" data-user-id="{{ $user['id'] }}" data-user-name="{{ $user['name'] }}">
                    <div class="user-item-content">
                        <div class="user-avatar">
                            {{ strtoupper(substr($user['name'], 0, 1)) }}
                        </div>
                        <div class="user-details">
                            <div class="user-name">{{ $user['name'] }}</div>
                            <div class="user-type">{{ ucfirst($user['type']) }}</div>
                        </div>
                        <span class="unread-badge" id="unread-{{ $user['id'] }}" style="display: none;">0</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="chat-section">
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="chat-avatar" id="chatAvatar" style="display: none;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h1 class="chat-title" id="chatTitle">Pilih user untuk memulai chat</h1>
                </div>
                <div class="chat-actions" id="chatActions" style="display: none;">
                    <button class="action-btn" title="Info">
                        <i class="fas fa-info-circle"></i>
                    </button>
                    <button class="action-btn" title="More">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <div class="no-chat-selected">
                    <i class="fas fa-comments no-chat-icon"></i>
                    <div class="no-chat-text">Selamat Datang, Admin!</div>
                    <div class="no-chat-subtext">Pilih user dari sidebar untuk memulai percakapan</div>
                </div>
            </div>
            
            <div class="chat-input-container" id="chatInputContainer" style="display: none;">
                <form class="chat-input-form" id="chatForm">
                    @csrf
                    <input type="hidden" id="receiverId" name="receiver_id">
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            class="chat-input" 
                            id="messageInput" 
                            placeholder="Tulis pesan Anda..." 
                            autocomplete="off"
                            required
                        >
                        <button type="button" class="emoji-button">😊</button>
                    </div>
                    <button type="submit" class="send-button" id="sendButton">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
    // Set global variables BEFORE loading chat.js
    window.userId = "{{ Auth::id() }}";
    window.userName = "{{ Auth::user()->name }}";
    window.isAdmin = true;
    window.selectedUserId = null;
    
    console.log('Chat Admin Initialized');
    console.log('User ID:', window.userId);
    console.log('User Name:', window.userName);
    console.log('Is Admin:', window.isAdmin);
</script>
@vite(['resources/js/chat.js'])
@endsection
