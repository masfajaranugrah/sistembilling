@use('Illuminate\Support\Facades\Auth')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - User</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
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
            50% { opacity: 0.5; }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-45deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .chat-container {
            max-width: 900px;
            width: 100%;
            height: 90vh;
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 30px 90px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }
        
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 20px;
            position: relative;
            overflow: hidden;
        }
        
        .chat-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .header-info h1 {
            font-size: 18px;
            margin-bottom: 2px;
            font-weight: 600;
        }
        
        .user-status {
            font-size: 12px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .status-dot {
            width: 7px;
            height: 7px;
            background: #4ade80;
            border-radius: 50%;
            box-shadow: 0 0 10px #4ade80;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
            background: linear-gradient(to bottom, #f8fafc 0%, #f1f5f9 100%);
            position: relative;
        }
        
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }
        
        .chat-messages::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        .chat-messages::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }
        
        .message {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            animation: slideIn 0.3s ease-out;
        }
        
        .message.sent {
            justify-content: flex-end;
        }
        
        .message.received {
            justify-content: flex-start;
        }
        
        .message-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .message.sent .message-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            order: 2;
        }
        
        .message.received .message-avatar {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .message-bubble {
            max-width: 65%;
            position: relative;
        }
        
        .message-content {
            padding: 14px 18px;
            border-radius: 20px;
            word-wrap: break-word;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .message.sent .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }
        
        .message.received .message-content {
            background: white;
            color: #1e293b;
            border-bottom-left-radius: 5px;
        }
        
        .message-text {
            font-size: 15px;
            line-height: 1.5;
        }
        
        .message-info {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 6px;
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
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 60px;
        }
        
        .chat-input-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .input-wrapper {
            flex: 1;
            position: relative;
        }
        
        .chat-input {
            width: 100%;
            padding: 10px 45px 10px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 24px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        .chat-input:focus {
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .emoji-button {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.6;
            transition: all 0.3s ease;
        }
        
        .emoji-button:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.2);
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
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            flex-shrink: 0;
        }
        
        .send-button:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.6);
        }
        
        .send-button:active {
            transform: scale(0.95);
        }
        
        .send-button:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .send-button:disabled:hover {
            transform: none;
        }
        
        .typing-indicator {
            padding: 15px 30px;
            font-size: 13px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(102, 126, 234, 0.05);
        }
        
        .typing-dots {
            display: flex;
            gap: 4px;
        }
        
        .typing-dot {
            width: 6px;
            height: 6px;
            background: #667eea;
            border-radius: 50%;
            animation: pulse 1.4s ease-in-out infinite;
        }
        
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        .date-divider {
            text-align: center;
            margin: 30px 0;
            position: relative;
        }
        
        .date-divider::before,
        .date-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: linear-gradient(to right, transparent, #cbd5e1, transparent);
        }
        
        .date-divider::before {
            left: 0;
        }
        
        .date-divider::after {
            right: 0;
        }
        
        .date-text {
            display: inline-block;
            padding: 6px 16px;
            background: white;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div class="header-content">
                <div class="header-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="header-info">
                    <h1>Chat dengan Admin</h1>
                    <div class="user-status">
                        <span class="status-dot"></span>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will be loaded here -->
        </div>
        
        <div class="typing-indicator" id="typingIndicator" style="display: none;">
            <div class="typing-dots">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
            <span>Admin sedang mengetik...</span>
        </div>
        
        <div class="chat-input-container">
            <form class="chat-input-form" id="chatForm">
                @csrf
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

    <script>
        // Set global variables BEFORE loading scripts
        window.userId = "{{ Auth::id() }}";
        window.userName = "{{ Auth::user()->name }}";
        window.isAdmin = false;
        
        console.log('Chat User Initialized');
        console.log('User ID:', window.userId);
        console.log('User Name:', window.userName);
    </script>
    
    @vite(['resources/js/bootstrap.js', 'resources/js/echo.js', 'resources/js/chat.js'])
    <div class="bottom-nav">
    @include('content.apps.chat.user.bottom-navbar', ['active' => 'chat'])
</div>
</body>
</html>
