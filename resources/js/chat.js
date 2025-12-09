// Chat functionality
document.addEventListener('DOMContentLoaded', function() {
    // Wait for axios to be available
    if (typeof window.axios === 'undefined') {
        console.error('❌ Axios not loaded! Retrying in 500ms...');
        setTimeout(() => {
            window.location.reload();
        }, 500);
        return;
    }
    
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    
    if (!chatMessages || !chatForm) {
        console.log('⚠️ Chat elements not found on this page');
        return;
    }
    
    const isAdmin = window.isAdmin || false;
    const userId = window.userId;
    
    console.log('💬 Chat initialized for user:', userId, 'isAdmin:', isAdmin);
    
    // Function to get initials from name
    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    }
    
    // Load messages
    function loadMessages(targetUserId = null) {
        const url = isAdmin && targetUserId 
            ? `/chat/messages/${targetUserId}` 
            : '/chat/messages';
         
        console.log('📥 Loading messages from:', url);
        
        axios.get(url)
            .then(response => {
                console.log('✅ Messages loaded:', response.data.length);
                displayMessages(response.data);
                scrollToBottom();
            })
            .catch(error => {
                console.error('❌ Error loading messages:', error);
            });
    }
    
    // Display messages
    function displayMessages(messages) {
        chatMessages.innerHTML = '';
        
        if (messages.length === 0) {
            chatMessages.innerHTML = `
                <div class="no-chat-selected">
                    <i class="fas fa-inbox no-chat-icon"></i>
                    <div class="no-chat-text">Belum ada pesan</div>
                    <div class="no-chat-subtext">Mulai percakapan dengan mengirim pesan</div>
                </div>
            `;
            return;
        }
        
        messages.forEach(message => {
            appendMessage(message);
        });
    }
    
    // Append single message
    function appendMessage(message, isPending = false) {
        // Cek duplikat berdasarkan message ID
        if (message.id && chatMessages.querySelector(`[data-message-id="${message.id}"]`)) {
            console.log('⏭️ Skipping duplicate message:', message.id);
            return;
        }
        
        const messageDiv = document.createElement('div');
        
        // Convert to string for UUID comparison
        const currentUserId = String(userId);
        const messageSenderId = String(message.sender_id);
        const isSent = messageSenderId === currentUserId;
        
        console.log('🔍 Message comparison:', {
            currentUserId,
            messageSenderId,
            isSent,
            isAdmin: window.isAdmin
        });
        
        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        if (message.id) {
            messageDiv.dataset.messageId = message.id;
        }
        if (message.tempId) {
            messageDiv.dataset.tempId = message.tempId;
        }
        
        const time = new Date(message.created_at).toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        const senderName = message.sender ? message.sender.name : 'Unknown';
        const initials = getInitials(senderName);
        
        // Icon status: clock untuk pending, check untuk terkirim
        const statusIcon = isPending ? 
            '<i class="fas fa-clock"></i>' : 
            '<i class="fas fa-check"></i>';
        
        messageDiv.innerHTML = `
            <div class="message-avatar">${initials}</div>
            <div class="message-bubble">
                <div class="message-content">
                    <div class="message-text">${escapeHtml(message.message)}</div>
                    <div class="message-info">
                        ${statusIcon}
                        ${time}
                    </div>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
        
        // Hilangkan notifikasi suara - tidak perlu lagi
        // if (!isSent && !isPending) {
        //     playNotificationSound();
        //     showNotification(senderName, message.message);
        // }
    }
    
    // Notification sound
    function playNotificationSound() {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSyCz/LZiTYIGGS57OehUBELTqfj8bllHAU2jdXvzH4yBSh+zPLaizsKFFix6OyrWBQKQ5zd8sFuJAUrhM/y2Ik3CBhiu+zom1ARC0ym4/G5ZBwGNovU78x+MwUoc8zy3Ik2CBVes+jqq1kUCj+Z3PLEcSQFK4PO8tmJNwgZYrnq5p1RDwtMpuPxuWQcBjaM1e/MfjMFJ3DN8tyKOwgUXLPn6qtZFAo/mdzyxHEkBSuDzvLZiTcIGWK56uadUQ8LTKbj8blkHAY2i9XvzH4zBSdwzfLcizsIFF2z5+qrWRQKPpTa8cJvIwQqf87y2oo7CBZguerpnlEPC0ym4/G5ZBsFNYnU8Mx+MwUncMzy34s3CRVdsefrq3oM');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Audio play failed:', e));
    }
    
    // Show browser notification
    function showNotification(sender, message) {
        if (!('Notification' in window)) return;
        
        if (Notification.permission === 'granted') {
            new Notification(sender, {
                body: message.substring(0, 100),
                icon: '/favicon.ico'
            });
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    new Notification(sender, {
                        body: message.substring(0, 100),
                        icon: '/favicon.ico'
                    });
                }
            });
        }
    }
    
    // Update unread badge untuk admin
    function updateUnreadBadge(senderId) {
        const badge = document.getElementById(`unread-${senderId}`);
        if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
            badge.style.display = 'inline-block';
        }
    }
    
    // Move user to top of list when new message arrives
    function moveUserToTop(userId) {
        const userList = document.getElementById('userList');
        if (!userList) return;
        
        // Find user item
        const userItem = userList.querySelector(`[data-user-id="${userId}"]`);
        if (!userItem) return;
        
        // If already at top, no need to move
        if (userList.firstElementChild === userItem) return;
        
        // Remove from current position and add to top with smooth animation
        userItem.style.transition = 'all 0.3s ease';
        userList.insertBefore(userItem, userList.firstElementChild);
        
        // Add highlight animation
        userItem.style.backgroundColor = '#f0f9ff';
        setTimeout(() => {
            userItem.style.backgroundColor = '';
        }, 1000);
    }
    
    // Clear unread badge
    function clearUnreadBadge(userId) {
        const badge = document.getElementById(`unread-${userId}`);
        if (badge) {
            badge.textContent = '0';
            badge.style.display = 'none';
        }
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Scroll to bottom
    function scrollToBottom() {
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    }
    
    // Send message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;
        
        const data = { message };
        
        if (isAdmin) {
            const receiverId = document.getElementById('receiverId').value;
            if (!receiverId) {
                alert('Pilih user terlebih dahulu');
                return;
            }
            data.receiver_id = receiverId;
        }
        
        sendButton.disabled = true;
        messageInput.value = '';
        
        axios.post('/chat/send', data)
            .then(response => {
                console.log('✅ Message sent successfully:', response.data.message);
                // Langsung append pesan yang sudah berhasil disimpan
                appendMessage(response.data.message, false);
                scrollToBottom();
                
                // Move current user to top if admin
                if (isAdmin && window.selectedUserId) {
                    moveUserToTop(window.selectedUserId);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Gagal mengirim pesan');
            })
            .finally(() => {
                sendButton.disabled = false;
                messageInput.focus();
            });
    });
    
    // Setup WebSocket listener with retry mechanism
    function setupWebSocketListener() {
        if (!window.Echo) {
            console.log('⏳ Waiting for Echo to initialize...');
            setTimeout(setupWebSocketListener, 100);
            return;
        }
        
        console.log('🔌 Setting up Echo listener for user:', userId);
        console.log('📡 Channel name:', `chat.${userId}`);
        
        const channel = `chat.${userId}`;
        
        const privateChannel = window.Echo.private(channel);
        
        // Listen to ALL events for debugging
        privateChannel.listenToAll((event, data) => {
            console.log('🎯 RAW EVENT RECEIVED:', event, data);
        });
        
        privateChannel
            .subscribed(() => {
                console.log('✅ Successfully subscribed to channel:', channel);
            })
            // Try both event names
            .listen('MessageSent', (e) => {
                console.log('📩 RAW MESSAGE RECEIVED (MessageSent):', e);
                processIncomingMessage(e);
            })
            .listen('.MessageSent', (e) => {
                console.log('📩 RAW MESSAGE RECEIVED (.MessageSent):', e);
                processIncomingMessage(e);
            })
            .error((error) => {
                console.error('❌ Echo subscription error:', error);
            });
        
        // Function to process incoming message
        function processIncomingMessage(e) {
            console.log('📩 Processing message:', e);
            console.log('📩 Message ID:', e.id);
            console.log('📩 Sender ID:', e.sender_id);
            console.log('📩 Receiver ID:', e.receiver_id);
            console.log('📩 Message:', e.message);
                
            // Convert to string for UUID comparison
            const currentUserId = String(userId);
            const eventSenderId = String(e.sender_id);
            
            console.log('🔍 Comparison - Current User:', currentUserId, 'Event Sender:', eventSenderId);
            
            // Jangan tampilkan pesan yang baru saja kita kirim sendiri
            if (eventSenderId === currentUserId) {
                console.log('⏭️ Skipping own message (already displayed when sent)');
                return;
            }
            
            console.log('✅ Message is from another user, processing...');
            
            // Only append if message is for current conversation
            if (isAdmin) {
                const selectedUserId = String(window.selectedUserId);
                
                console.log('👤 Admin mode - Selected user:', selectedUserId);
                
                // Move user to top of list
                moveUserToTop(e.sender_id);
                
                // Update unread badge jika user belum dipilih atau berbeda
                if (!window.selectedUserId || selectedUserId !== eventSenderId) {
                    updateUnreadBadge(e.sender_id);
                    console.log('📬 Updated unread badge for user:', e.sender_id);
                }
                
                // HANYA append message jika sedang melihat chat dari user tersebut
                if (window.selectedUserId && selectedUserId === eventSenderId) {
                    console.log('✅ Appending message for admin (current conversation)');
                    appendMessage(e, false);
                    scrollToBottom();
                    
                    // Mark as read karena sedang melihat chat ini
                    axios.post(`/chat/mark-read/${e.sender_id}`)
                        .then(() => console.log('✅ Marked as read'))
                        .catch(err => console.error('❌ Failed to mark as read:', err));
                } else {
                    console.log('⏭️ Message for different conversation, not appending');
                }
            } else {
                console.log('✅ Customer mode - Appending message from admin');
                appendMessage(e, false);
                scrollToBottom();
            }
        }
        
        console.log('🎧 Event listener registered for MessageSent');
    }
    // Start WebSocket listener
    setupWebSocketListener();
    
    // Admin specific: Handle user selection
    if (isAdmin) {
        const userItems = document.querySelectorAll('.user-item');
        const chatTitle = document.getElementById('chatTitle');
        const chatAvatar = document.getElementById('chatAvatar');
        const chatActions = document.getElementById('chatActions');
        const chatInputContainer = document.getElementById('chatInputContainer');
        const receiverIdInput = document.getElementById('receiverId');
        
        userItems.forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all
                userItems.forEach(u => u.classList.remove('active'));
                
                // Add active class to clicked
                this.classList.add('active');
                
                // Get user info
                const targetUserId = this.dataset.userId;
                const userName = this.dataset.userName;
                
                // Update global variable
                window.selectedUserId = targetUserId;
                
                // Clear unread badge
                clearUnreadBadge(targetUserId);
                
                // Update UI
                chatTitle.textContent = userName;
                chatAvatar.style.display = 'flex';
                chatAvatar.innerHTML = getInitials(userName);
                chatActions.style.display = 'flex';
                receiverIdInput.value = targetUserId;
                chatInputContainer.style.display = 'block';
                
                // Load messages for this user
                loadMessages(targetUserId);
                
                // Mark messages as read
                axios.post(`/chat/mark-read/${targetUserId}`)
                    .then(() => {
                        console.log('✅ Messages marked as read');
                    })
                    .catch(err => console.error('❌ Failed to mark as read:', err));
            });
        });
    } else {
        // User: Load messages immediately
        loadMessages();
    }
    
    // Search functionality for admin
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');
            
            userItems.forEach(item => {
                const userName = item.dataset.userName.toLowerCase();
                const userEmail = item.querySelector('.user-email')?.textContent.toLowerCase() || '';
                
                if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});

