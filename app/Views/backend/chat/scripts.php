<script>
    /**
     * WAYO Chat Module with Reverb & Pusher
     * Gestion du frontend chat pour School Management
     * 
     * Architecture: Frontend Only (School Management) -> Backend (Chat Service)
     * Auth: Cross-Auth (WAP Token -> Chat Service Token)
     */

    const ChatApp = (function() {
        // Configuration
        const config = {
            // URLs
            baseUrl: '<?php echo base_url(); ?>',
            siteUrl: '<?php echo rtrim(site_url(), "/"); ?>',
            chatServiceUrl: 'https://wayochat.wayo.ac/api/v1',
            chatServiceBaseUrl: 'https://wayochat.wayo.ac', // Updated to production
            authUrl: 'https://wayochat.wayo.ac/api/auth/cross-auth',
            
            // Auth Data
            wapToken: '<?php echo $wap_token; ?>',
            authToken: null, // Will be set after cross-auth
            appId: 'wayo',
            
            currentUser: {
                id: '<?php echo session()->get("user_id"); ?>', // WAP ID
                chat_id: null, // Chat Service ID (set after auth)
                name: '<?php echo session()->get("name"); ?>',
                email: '<?php echo session()->get("email"); ?>',
                avatar: '<?php 
                    $user_id = session()->get("user_id");
                    $avatar_path = "uploads/users/" . $user_id . ".jpg";
                    if (file_exists($avatar_path)) {
                        echo base_url($avatar_path) . "?v=" . filemtime($avatar_path);
                    } else {
                        echo base_url("uploads/users/placeholder.jpg");
                    }
                ?>'
            },
            
            // Reverb (Laravel Echo)
            reverb: {
                key: 'iuvcjjlml7xkwbdfaxo3',
                host: 'wayochat.wayo.ac',
                port: 443,
                scheme: 'https',
                forceTLS: true,
                encrypted: true,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
            },
            
            currentChatId: null,
            echo: null
        };

        // State
        let mediaRecorder = null;
        let audioChunks = [];
        let recordedAudioBlob = null;
        let isRecording = false;
        let recordingStartTime = null;
        let recordingInterval = null;
        let recordingAction = 'preview'; // 'preview' or 'cancel'
        let previewAudio = null;
        let previewUpdateInterval = null;
        let isEditing = false;
        let editingMessageId = null; // Track which message is being edited
        let selectedMessageId = null;
        let selectedImageFile = null;
        let activeChannel = null;
        let onlineUsers = new Set();
        let offlineTimeouts = new Map(); // Debounce map for graceful disconnect
        
        // Mobile Long Press State
        let longPressTimer;
        let touchStartX = 0;
        let touchStartY = 0;
        const LONG_PRESS_DURATION = 500; // ms

        // DOM Elements
        const dom = {
            chatBody: document.getElementById('chat-body'),
            messageInput: document.getElementById('message-input'),
            sendBtn: document.getElementById('btn-send'),
            recordBtn: document.getElementById('btn-record'),
            imageBtn: document.getElementById('btn-image'),
            imageInput: document.getElementById('image-input'),
            chatList: document.getElementById('chat-list'),
            typingIndicator: document.getElementById('typing-indicator'),
            searchInput: document.getElementById('user-search-input'),
            searchResults: document.getElementById('search-results'),
            // Previews & Mobile
            previewContainer: document.getElementById('image-preview-container'),
            previewWrapper: document.getElementById('preview-wrapper'),
            previewImage: document.getElementById('image-preview'),
            removePreviewBtn: document.getElementById('btn-remove-preview'),
            
            // Modern Recording UI
            standardInputUI: document.getElementById('standard-input-ui'),
            recordingInterface: document.getElementById('recording-interface'),
            recordingState: document.getElementById('recording-state'),
            previewState: document.getElementById('recording-preview-state'),
            recordingTimer: document.getElementById('recording-timer-modern'),
            
            cancelRecordBtn: document.getElementById('btn-cancel-record'),
            stopRecordBtn: document.getElementById('btn-stop-record'),
            
            discardRecordBtn: document.getElementById('btn-discard-record'),
            sendRecordBtnFinal: document.getElementById('btn-send-record-final'),
            previewPlayBtn: document.getElementById('btn-preview-play'),
            previewProgressBar: document.getElementById('preview-progress-bar'),
            previewProgressContainer: document.getElementById('preview-progress-container'),
            previewTimer: document.getElementById('preview-timer'),
            
            mobileContextOverlay: document.getElementById('mobile-context-overlay'),
            mobileCopy: document.getElementById('mobile-copy'),
            mobileEdit: document.getElementById('mobile-edit'),
            mobileDelete: document.getElementById('mobile-delete'),
            // Lightbox
            lightboxOverlay: document.getElementById('lightbox-overlay'),
            lightboxImage: document.getElementById('lightbox-image'),
            
            // Scroll Button
            scrollToBottomBtn: document.getElementById('scroll-to-bottom-btn')
        };
        
        let shouldSendRecording = false; // Flag for auto-send

        let typingTimeout = null;
        let lastTypingTime = 0;
        let currentPlayingAudioId = null; // Track currently playing audio

        // --- Core Functions ---

        function toggleAudio(msgId) {
            const audio = document.getElementById(`audio-${msgId}`);
            const btnIcon = document.getElementById(`audio-icon-${msgId}`);
            
            if (!audio) return;

            // If another audio is playing, stop it
            if (currentPlayingAudioId && currentPlayingAudioId !== msgId) {
                const prevAudio = document.getElementById(`audio-${currentPlayingAudioId}`);
                const prevIcon = document.getElementById(`audio-icon-${currentPlayingAudioId}`);
                if (prevAudio) {
                    prevAudio.pause();
                    prevAudio.currentTime = 0;
                }
                if (prevIcon) prevIcon.className = 'mdi mdi-play';
            }

            if (audio.paused) {
                audio.play();
                btnIcon.className = 'mdi mdi-pause';
                currentPlayingAudioId = msgId;
            } else {
                audio.pause();
                btnIcon.className = 'mdi mdi-play';
                currentPlayingAudioId = null;
            }
        }

        function onAudioLoaded(audio, msgId) {
            const timeDisplay = document.getElementById(`audio-time-${msgId}`);
            if (timeDisplay && audio.duration) {
                const minutes = Math.floor(audio.duration / 60);
                const seconds = Math.floor(audio.duration % 60);
                timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        }

        function onAudioTimeUpdate(msgId) {
            const audio = document.getElementById(`audio-${msgId}`);
            const progress = document.getElementById(`audio-progress-${msgId}`);
            const timeDisplay = document.getElementById(`audio-time-${msgId}`);
            
            if (audio && progress) {
                const percent = (audio.currentTime / audio.duration) * 100;
                progress.style.width = `${percent}%`;
                
                // Update time countdown or current time
                const timeLeft = audio.duration - audio.currentTime;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = Math.floor(timeLeft % 60);
                if (timeDisplay) {
                     timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                }
            }
        }

        function onAudioEnded(msgId) {
            const btnIcon = document.getElementById(`audio-icon-${msgId}`);
            const progress = document.getElementById(`audio-progress-${msgId}`);
            const audio = document.getElementById(`audio-${msgId}`);
            
            if (btnIcon) btnIcon.className = 'mdi mdi-play';
            if (progress) progress.style.width = '0%';
            
            // Reset time display
            if (audio) onAudioLoaded(audio, msgId);
            
            currentPlayingAudioId = null;
        }
        
        function seekAudio(e, msgId) {
            const audio = document.getElementById(`audio-${msgId}`);
            const track = e.currentTarget;
            if (audio && track) {
                const rect = track.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const percent = x / rect.width;
                audio.currentTime = percent * audio.duration;
            }
        }

        function init() {
            
            authenticate();
            bindEvents();
        }

        function authenticate() {
           
            if (!config.wapToken || String(config.wapToken).trim() === '') {
                console.error('Authentication failed: missing WAP token');
                alert('<?php echo get_phrase('chat_service_connection_error'); ?>');
                return;
            }
            fetch(config.authUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Application-ID': config.appId,
                    'Authorization': 'Bearer ' + config.wapToken
                },
                body: JSON.stringify({ wap_token: config.wapToken })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    
                    config.authToken = data.data.token;
                    config.currentUser.chat_id = data.data.user.id;
                    
                    initEcho();
                    loadConversations();
                } else {
                    console.error('Authentication failed:', data.message);
                    alert('<?php echo get_phrase('chat_service_connection_error'); ?>');
                }
            })
            .catch(err => {
                console.error('Auth Error:', err);
                alert('<?php echo get_phrase('chat_service_unreachable'); ?>');
            });
        }

        function getHeaders() {
            return {
                'Authorization': 'Bearer ' + config.authToken,
                'X-Application-ID': config.appId,
                'Accept': 'application/json'
            };
        }

        function initEcho() {
            if (window.Echo) {
                config.echo = new Echo({
                    broadcaster: 'reverb',
                    key: config.reverb.key,
                    wsHost: config.reverb.host,
                    wsPort: config.reverb.port,
                    wssPort: config.reverb.port,
                    forceTLS: (config.reverb.scheme === 'https'),
                    enabledTransports: ['ws', 'wss'],
                    authEndpoint: config.chatServiceUrl + '/broadcasting/auth',
                    auth: {
                        headers: {
                            Authorization: 'Bearer ' + config.authToken,
                            'X-Application-ID': config.appId
                        }
                    }
                });

                config.echo.connector.pusher.connection.bind('connected', () => {
                    
                });

                // Listen for global updates (new messages/conversations)
                // Channel format: private-user.{userId}.{appId}
                // Event: message.sent (defined in MessageSent::broadcastAs)
                config.echo.private(`user.${config.currentUser.chat_id}.${config.appId}`)
                    .listen('.message.sent', (e) => {
                         
                         loadConversations(); // Reload list to show new message/order
                         
                         // If we are not in this conversation, show notification/badge?
                         // Already handled by loadConversations (unread count)
                    })
                    .listen('.user.updated', (e) => {
                        
                        
                        // 1. Update Active Conversation Header
                        if (config.activePeerId && String(config.activePeerId) === String(e.id)) {
                             const headerName = document.getElementById('active-user-name');
                             const headerAvatar = document.getElementById('active-user-avatar');
                             if (headerName) headerName.textContent = e.name;
                             if (headerAvatar && e.avatar) headerAvatar.src = e.avatar;
                        }
                        
                        // 2. Update Sidebar List (Direct DOM)
                        const statusIndicators = document.querySelectorAll(`.status-indicator[data-user-id="${e.id}"]`);
                        statusIndicators.forEach(indicator => {
                            const item = indicator.closest('.chat-user-item');
                            if (item) {
                                const nameEl = item.querySelector('.user-name');
                                const avatarEl = item.querySelector('.user-avatar img');
                                if (nameEl) nameEl.textContent = e.name;
                                if (avatarEl && e.avatar) avatarEl.src = e.avatar;
                            }
                        });
                        
                        // 3. Update Search Results
                        const searchResults = document.querySelectorAll(`.search-result-card[data-user-id="${e.id}"]`);
                        searchResults.forEach(item => {
                             const nameEl = item.querySelector('.search-result-name');
                             const avatarEl = item.querySelector('.search-result-avatar');
                             if (nameEl) nameEl.textContent = e.name;
                             if (avatarEl && e.avatar) avatarEl.src = e.avatar;
                        });

                        // 4. Reload full list to ensure consistency (background)
                        loadConversations();
                    });
                    
                setupPresence();
            }
        }
        
        function setupPresence() {
            if (!config.echo) return;
            
            // Join Presence Channel
            // Echo automatically adds 'presence-' prefix to join() calls
            config.echo.join(`global.${config.appId}`)
                .here((users) => {
                    onlineUsers.clear();
                    users.forEach(user => {
                        // user.id is the chat-service ID
                        // We store as String to be safe
                        if (String(user.id) !== String(config.currentUser.chat_id)) {
                            onlineUsers.add(String(user.id));
                        }
                    });
                    updateAllStatuses();
                })
                .joining((user) => {
                    
                    // Clear any pending offline timeout for this user (Graceful Reconnect)
                    if (offlineTimeouts.has(String(user.id))) {
                        clearTimeout(offlineTimeouts.get(String(user.id)));
                        offlineTimeouts.delete(String(user.id));
                    }

                    onlineUsers.add(String(user.id));
                    updateUserStatus(user.id, true);
                })
                .leaving((user) => {                    
                    // Debounce offline status to prevent flickering on page reload
                    const timeoutId = setTimeout(() => {
                        onlineUsers.delete(String(user.id));
                        updateUserStatus(user.id, false);
                        offlineTimeouts.delete(String(user.id));
                    }, 4000); // 4 seconds grace period
                    
                    offlineTimeouts.set(String(user.id), timeoutId);
                })
                .error((error) => {
                    console.error('⚠️ Presence Channel Error:', error);
                    // Check authentication endpoint response if possible
                });
        }
        
        function updateAllStatuses() {
            // Helper to refresh all visible indicators based on onlineUsers set
            document.querySelectorAll('.status-indicator[data-user-id]').forEach(el => {
                const userId = String(el.dataset.userId);
                if (onlineUsers.has(userId)) {
                    el.classList.remove('status-offline');
                    el.classList.add('status-online');
                } else {
                    el.classList.remove('status-online');
                    el.classList.add('status-offline');
                }
            });
            
            // Update active header if applicable
            if (config.activePeerId) {
                const isOnline = onlineUsers.has(String(config.activePeerId));
                updateHeaderStatus(isOnline);
            }
        }
        
        function updateUserStatus(userId, isOnline) {
            // Use String for comparison
            const sUserId = String(userId);
            const indicators = document.querySelectorAll(`.status-indicator[data-user-id="${sUserId}"]`);
            indicators.forEach(el => {
                if (isOnline) {
                    el.classList.remove('status-offline');
                    el.classList.add('status-online');
                } else {
                    el.classList.remove('status-online');
                    el.classList.add('status-offline');
                }
            });

            // Update Header if active user
            if (config.activePeerId && String(config.activePeerId) === sUserId) {
                updateHeaderStatus(isOnline);
            }
        }

        function updateHeaderStatus(isOnline) {
            const indicator = document.getElementById('active-user-indicator');
            const statusText = document.getElementById('active-user-status');
            
            if (indicator) {
                indicator.className = `user-status ${isOnline ? 'status-online' : 'status-offline'}`;
            }
            
            if (statusText) {
                statusText.className = isOnline ? 'text-success' : 'text-muted';
                statusText.innerText = isOnline ? '<?php echo get_phrase('online'); ?>' : '<?php echo get_phrase('offline'); ?>';
            }
        }

        // --- Conversations ---

        function loadConversations() {
            fetch(`${config.chatServiceUrl}/conversations`, {
                headers: getHeaders()
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (Array.isArray(data.data)) {
                        let totalUnread = 0;
                        data.data.forEach(conv => {
                             if (conv.unread_count) {
                                 totalUnread += parseInt(conv.unread_count);
                             }
                        });
                        
                        // Sync with global notification system
                        localStorage.setItem('chat_ack_count', totalUnread);
                       
                    }
                    renderConversations(data.data);
                }
            })
            .catch(err => console.error('Load Conversations Error:', err));
        }

        function renderConversations(conversations) {
            dom.chatList.innerHTML = '';
            conversations.forEach(conv => {
                const isActive = config.currentChatId == conv.id ? 'active' : '';
                
                let lastMsg = 'Nouvelle conversation';
                if (conv.last_message) {
                    if (conv.last_message.type === 'image') {
                        // Use original filename if available, otherwise fallback to URL or default
                        let filename = 'Image';
                        
                        if (conv.last_message.file_name) {
                            filename = conv.last_message.file_name;
                        } else if (conv.last_message.file_url) {
                            try {
                                const urlParts = conv.last_message.file_url.split('/');
                                filename = urlParts[urlParts.length - 1];
                            } catch (e) {
                                filename = 'Image';
                            }
                        }
                        
                        lastMsg = `<i class="mdi mdi-image"></i> ${filename}`;
                    } else if (conv.last_message.type === 'audio') {
                         lastMsg = '<i class="mdi mdi-microphone"></i> Audio';
                    } else if (conv.last_message.type === 'file') {
                         lastMsg = '<i class="mdi mdi-file"></i> Fichier';
                    } else {
                        lastMsg = conv.last_message.content || '';
                    }
                }
                
                const time = conv.last_message ? formatDate(conv.last_message.created_at) : formatDate(conv.updated_at);
                
                // Presence Logic
                let otherUserId = null;
                if (conv.type === 'direct' && conv.participants) {
                    const other = conv.participants.find(p => String(p.user_id) !== String(config.currentUser.chat_id));
                    if (other) otherUserId = String(other.user_id);
                }
                
                const isOnline = otherUserId && onlineUsers.has(otherUserId);
                const statusClass = isOnline ? 'status-online' : 'status-offline';
                const statusHtml = otherUserId ? `<span class="status-indicator ${statusClass}" data-user-id="${otherUserId}"></span>` : '';
                
                const html = `
                    <div class="chat-user-item ${isActive}" id="conversation-item-${conv.id}" onclick="ChatApp.openConversation(${conv.id}, '${conv.display_name.replace(/'/g, "\\'")}', '${conv.display_avatar}', ${otherUserId})">
                        <div class="user-avatar">
                            <img src="${conv.display_avatar || config.baseUrl + 'uploads/users/placeholder.jpg'}" onerror="this.src='${config.baseUrl}uploads/users/placeholder.jpg'" alt="">
                            ${statusHtml}
                        </div>
                        <div class="user-info">
                            <div class="user-name">
                                ${conv.display_name}
                                <div class="sidebar-typing-indicator" id="sidebar-typing-${conv.id}" style="display:none;">
                                    <span></span><span></span><span></span>
                                </div>
                            </div>
                            <div class="user-last-msg">${lastMsg}</div>
                        </div>
                        <div class="user-meta">
                            <span>${time}</span>
                            ${(() => {
                                // Logic for Badge Display
                                // 1. Hide if current conversation
                                if (config.currentChatId == conv.id) return '';
                                
                                // 2. Hide if count is 0 or less
                                if (!conv.unread_count || conv.unread_count <= 0) return '';
                                
                                // 3. Format count
                                let content = conv.unread_count;
                                if (conv.unread_count > 9) {
                                    content = '9+';
                                }
                                
                                return `<span class="badge badge-primary">${content}</span>`;
                            })()}
                        </div>
                    </div>
                `;
                dom.chatList.insertAdjacentHTML('beforeend', html);
            });
        }

        function openConversation(conversationId, name, avatar, peerId = null) {
            config.activePeerId = peerId;

            // UI: Immediately hide badge for this conversation
            const clickedItem = document.getElementById(`conversation-item-${conversationId}`);
            if (clickedItem) {
                const badge = clickedItem.querySelector('.badge');
                if (badge) badge.style.display = 'none';
            }
            
            // Mark as read on server
            markAsRead(conversationId);

            // Remove previous listener if exists to prevent duplicates
            if (activeChannel) {
                // We need to store the channel name to leave it correctly
                // Or simply leave the channel we are about to join if we track it differently?
                // Better approach: track the current channel name or object.
                // Since activeChannel is the Pusher channel object, we can use it.
                // However, Echo.leave expects a channel name string.
                // Let's rely on config.currentChatId but we need the previous one.
                // Actually, the previous bug was leaving 'chat.ID' instead of 'conversation.ID.APPID'
            }
            
            if (config.currentChatId) {
                 config.echo.leave(`conversation.${config.currentChatId}.${config.appId}`);
            }

            config.currentChatId = conversationId;
            
            // UI Update
            document.querySelectorAll('.chat-user-item').forEach(el => el.classList.remove('active'));
            const activeItem = document.getElementById(`conversation-item-${conversationId}`);
            if (activeItem) activeItem.classList.add('active');

            // Header Update
            document.getElementById('active-user-name').innerText = name;
            const avatarImg = document.getElementById('active-user-avatar');
            avatarImg.src = avatar && avatar !== 'null' ? avatar : `${config.baseUrl}uploads/users/placeholder.jpg`;

            // Initial Status Update
            if (config.activePeerId) {
                const isOnline = onlineUsers.has(String(config.activePeerId));
                updateHeaderStatus(isOnline);
            } else {
                updateHeaderStatus(false);
            }

            // View
            document.getElementById('chat-placeholder').style.display = 'none';
            document.getElementById('chat-active-container').style.display = 'flex';
            
            // Mobile View Toggle
            document.querySelector('.chat-wrapper').classList.add('mobile-chat-active');

            loadMessages(conversationId);

            // Subscribe to channel
            // Channel convention in chat-service: `conversation.{conversationId}.{appId}`
            // Event: message.sent (defined in MessageSent::broadcastAs)
            const channelName = `conversation.${conversationId}.${config.appId}`;
            
            if (activeChannel) {
                // Fix: Leave the correct channel name (including appId)
                // Was previously leaving 'chat.{id}' which was incorrect
                config.echo.leave(activeChannel.name);
            }

            activeChannel = config.echo.private(channelName)
                .listen('.message.sent', (e) => {
                 
                    // Only append if it's not from me (or if I want to confirm receipt)
                    // Optimistic UI already added my message
                    if (e.sender.id != config.currentUser.chat_id) {
                         appendMessage(e.message, false);
                         // Mark as read immediately since we are active
                         markAsRead(conversationId);
                    } else {
                         // It's my message, maybe update status to 'sent' if we had such UI
                    }
                    scrollToBottom();
                    loadConversations(); // Refresh sidebar on new message
                })
                .listen('.message.deleted', (e) => {
                    
                    // Support both id formats just in case
                    const idToDelete = e.message_id || e.id;
                    if (idToDelete) {
                        const msgEl = document.getElementById(`msg-${idToDelete}`);
                        if (msgEl) {
                            msgEl.style.transition = 'opacity 0.3s ease';
                            msgEl.style.opacity = '0';
                            setTimeout(() => msgEl.remove(), 300);
                        }
                    }
                    loadConversations(); // Refresh sidebar on delete
                })
                .listen('.message.edited', (e) => {
                   
                    const msgEl = document.getElementById(`msg-${e.message.id}`);
                    if (msgEl) {
                        // Update content
                        msgEl.dataset.content = e.message.content;
                        const span = msgEl.querySelector('.message-content > span');
                        if (span) {
                            const linkedContent = escapeHtml(e.message.content).replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
                            span.innerHTML = linkedContent;
                        }
                        
                        // Add (modifié) indicator if missing
                        const metaDiv = msgEl.querySelector('.message-meta');
                        if (metaDiv) {
                            const timeSpan = metaDiv.querySelector('span');
                            // Check if indicator already exists to avoid duplicates
                            if (timeSpan && !timeSpan.innerHTML.includes('edited-indicator')) {
                                // Add indicator with correct styling
                                const indicatorHtml = ' <span class="edited-indicator" style="font-size: 0.75rem; color: #adb5bd;">(modifié)</span>';
                                timeSpan.insertAdjacentHTML('beforeend', indicatorHtml);
                            }
                        }
                    }
                    loadConversations(); // Refresh sidebar on edit
                })
                .listen('.message.read', (e) => {
                     // Update status to Read (Double Check White 100%)
                     // We expect e.message_ids or similar, or just a generic "all messages read" for this conversation
                     // Based on typical implementation, it might be per conversation.
                     
                     
                     // Assuming e contains user_id (who read) and maybe last_read_at
                     if (e.conversation_id == config.currentChatId) {
                         // Mark all my unread messages as read visually
                         document.querySelectorAll('.message-item.outgoing .message-status').forEach(el => {
                             // Update icon to double check
                             el.innerHTML = '<i class="mdi mdi-check-all"></i>';
                             // Update class to read
                             el.className = 'message-status status-read';
                         });
                     }
                })
                .listenForWhisper('typing', (e) => {
                    // Whisper event structure is determined by what we send
                    const conversationId = e.conversation_id || config.currentChatId;
                    
                    if (e.user_id != config.currentUser.chat_id) {
                        if (e.is_typing) {
                            showTypingIndicator(e.user_name, conversationId);
                        } else {
                            hideTypingIndicator(conversationId);
                        }
                    }
                });
        }

        function sendTypingEvent(isTyping) {
             if (!activeChannel) return;
             
             // Throttle: Don't send too often (e.g., every 2s)
             const now = Date.now();
             if (isTyping && now - lastTypingTime < 2000) {
                 return;
             }
             
             if (isTyping) lastTypingTime = now;

             // Send Whisper Event (Client-to-Client)
             // No server load, faster
             activeChannel.whisper('typing', {
                 user_id: config.currentUser.chat_id,
                 user_name: config.currentUser.name,
                 conversation_id: config.currentChatId,
                 is_typing: isTyping
             });
        }

        // --- Messages ---

        function loadMessages(conversationId) {
            dom.chatBody.innerHTML = '<div class="text-center mt-4"><div class="spinner-border text-primary" role="status"></div></div>';
            
            fetch(`${config.chatServiceUrl}/conversations/${conversationId}/messages`, {
                headers: getHeaders()
            })
            .then(res => res.json())
            .then(data => {
                dom.chatBody.innerHTML = '';
                if (data.success) {
                    // Handle Laravel Pagination structure (data.data.data vs data.data)
                    let messages = data.data.data ? data.data.data : data.data;
                    
                    // Ensure chronological order (Oldest -> Newest)
                    if (Array.isArray(messages)) {
                        messages.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                        
                        

                        messages.forEach(msg => {
                            let isMe = false;
                            
                            // Check 1: Chat Service ID (Most reliable if auth is done)
                            const msgUserId = msg.user_id || msg.sender_id;
                            if (config.currentUser.chat_id && msgUserId == config.currentUser.chat_id) {
                                isMe = true;
                            }
                            // Check 2: WAP ID (via user relation - fallback)
                            else if (msg.user && msg.user.user_id == config.currentUser.id) {
                                isMe = true;
                            }
                            // Check 3: Email (Final fallback)
                            else if (msg.user && config.currentUser.email && msg.user.email === config.currentUser.email) {
                                isMe = true;
                            }
                            
                            // Debug only if mismatch suspected
                            // console.log(`Msg ${msg.id} | IsMe: ${isMe} | MyChatID: ${config.currentUser.chat_id} | MsgUserID: ${msgUserId}`);

                            appendMessage(msg, isMe);
                        });

                        // Re-append Typing Indicator to ensure it exists in DOM after innerHTML wipe
                        if (dom.typingIndicator) {
                            dom.chatBody.appendChild(dom.typingIndicator);
                            dom.typingIndicator.style.display = 'none'; // Ensure hidden initially
                        }

                        scrollToBottom(true);
                    } else {
                         console.error('Invalid messages format:', messages);
                    }
                }
            });
        }

        function appendMessage(msg, isMe = false) {
            // Prevent duplicates
            const existingMsg = document.getElementById(`msg-${msg.id}`);
            if (existingMsg) {
                // If message exists (e.g., temp message), we might want to update it if confirmed
                if (existingMsg.id.startsWith('msg-temp-') && !msg.id.toString().startsWith('temp-')) {
                     existingMsg.id = `msg-${msg.id}`;
                     // Update content if needed (for edits)
                     const contentDiv = existingMsg.querySelector('.message-content span');
                }
                return;
            }

            const time = formatTime(msg.created_at);
            // Check if edited (flag from backend or timestamp comparison)
            const isEdited = msg.is_edited || (msg.updated_at && msg.created_at && new Date(msg.updated_at).getTime() > new Date(msg.created_at).getTime() + 1000);
            
            const div = document.createElement('div');
            
            // Check if it's an image-only message (no text content)
            const isImageOnly = msg.type === 'image' && (!msg.content || msg.content.trim() === '');
            const isAudio = msg.type === 'audio';
            
            // Align classes with CSS: message-item, outgoing/incoming
            div.className = `message-item ${isMe ? 'outgoing' : 'incoming'} ${isImageOnly ? 'image-message' : ''} ${isAudio ? 'audio-message' : ''}`;
            div.id = `msg-${msg.id}`;
            div.dataset.content = msg.content || ''; // Store content for easy access
            
            // Handle file content if present
            let contentHtml = '';
            if (msg.file_url) {
                // Ensure absolute URL
                let fileUrl = msg.file_url;
                if (fileUrl) {
                    if (fileUrl.startsWith('/')) {
                        fileUrl = config.chatServiceBaseUrl + fileUrl;
                    } else if (fileUrl.startsWith('http://localhost/storage')) {
                        // Fix Laravel default APP_URL issue
                        fileUrl = fileUrl.replace('http://localhost/storage', config.chatServiceBaseUrl + '/storage');
                    }
                }
                
                if (msg.type === 'image') {
                    contentHtml += `<div class="preview-wrapper mb-2"><img src="${fileUrl}" alt="Image" class="chat-image-thumbnail" onclick="ChatApp.openLightbox('${fileUrl}')"></div>`;
                } else if (msg.type === 'audio') {
                    contentHtml += `
                        <div class="custom-audio-player" id="player-container-${msg.id}">
                            <button class="audio-control-btn" onclick="ChatApp.toggleAudio('${msg.id}')">
                                <i class="mdi mdi-play" id="audio-icon-${msg.id}"></i>
                            </button>
                            <div class="audio-track" onclick="ChatApp.seekAudio(event, '${msg.id}')">
                                <div class="audio-progress" id="audio-progress-${msg.id}"></div>
                            </div>
                            <div class="audio-time" id="audio-time-${msg.id}">0:00</div>
                        </div>
                        <audio id="audio-${msg.id}" src="${fileUrl}" preload="metadata" 
                               onloadedmetadata="ChatApp.onAudioLoaded(this, '${msg.id}')" 
                               onended="ChatApp.onAudioEnded('${msg.id}')" 
                               ontimeupdate="ChatApp.onAudioTimeUpdate('${msg.id}')" 
                               style="display:none;"></audio>
                    `;
                } else {
                    contentHtml += `<div class="file-attachment"><i class="mdi mdi-file"></i> <a href="${fileUrl}" target="_blank">Fichier joint</a></div>`;
                }
            }
            
            if (msg.content) {
                // Auto-link URLs
                const linkedContent = escapeHtml(msg.content).replace(
                    /(https?:\/\/[^\s]+)/g, 
                    '<a href="$1" target="_blank">$1</a>'
                );
                contentHtml += `<span>${linkedContent}</span>`;
            }

            // Context Menu Actions HTML (Desktop 3-dots)
            // REMOVED Reply option as per user request
            // Split into Trigger and Menu for better positioning (Lateral)
            // Only show actions for MY messages as requested
            const triggerHtml = isMe ? `
                <div class="message-actions" onclick="event.stopPropagation(); ChatApp.toggleMessageMenu('${msg.id}', ${isMe})">
                    <div class="message-actions-btn">
                        <i class="mdi mdi-dots-vertical"></i>
                    </div>
                </div>
            ` : '';
            
            // Customize menu based on message type
            const isText = msg.type === 'text';
            
            const menuHtml = isMe ? `
                <div id="msg-menu-${msg.id}" class="message-context-menu">
                    ${isText ? `
                    <div class="context-menu-item" onclick="ChatApp.initEditMessage('${msg.id}')">
                        <i class="mdi mdi-pencil"></i> <?php echo get_phrase('edit'); ?>
                    </div>` : ''}
                    <div class="context-menu-item delete" onclick="ChatApp.deleteMessage('${msg.id}')">
                        <i class="mdi mdi-delete"></i> <?php echo get_phrase('delete'); ?>
                    </div>
                    ${isText ? `
                    <div class="context-menu-item" onclick="ChatApp.copyMessageContent('${msg.id}')">
                        <i class="mdi mdi-content-copy"></i> <?php echo get_phrase('copy'); ?>
                    </div>` : ''}
                </div>
            ` : '';

            // Determine Read Status (Simulated or Real)
            // If msg has read_at, it is read (Blue/Bright White Double Check)
            // If msg has delivered_at (or just is sent and not read), we might show Delivered (Grey Double Check)
            // If just sent (created_at), Single Check
            
            let statusIcon = '<i class="mdi mdi-check"></i>'; // Default Sent
            let statusClass = 'status-sent';
            
            if (msg.read_at) {
                statusIcon = '<i class="mdi mdi-check-all"></i>';
                statusClass = 'status-read';
            } else if (msg.delivered_at) { // Assuming backend might provide this or we simulate
                statusIcon = '<i class="mdi mdi-check-all"></i>';
                statusClass = 'status-delivered';
            }
            
            // Align inner structure with CSS: message-content, message-meta
            div.innerHTML = `
                <div class="message-content">
                    ${contentHtml}
                    <div class="message-meta" style="display: flex; align-items: center; gap: 5px;">
                        <span>${time}</span>
                        ${isEdited ? '<span class="edited-indicator" style="font-size: 0.75rem; color: #adb5bd;">(<?php echo get_phrase('edited'); ?>)</span>' : ''}
                        ${isMe ? `<span class="message-status ${statusClass}" style="margin-inline-start: auto;">${statusIcon}</span>` : ''}
                    </div>
                    ${triggerHtml}
                </div>
                ${menuHtml}
            `;
            
            // Add Long Press Events for Mobile (Touch)
            const msgContent = div.querySelector('.message-content');
            msgContent.addEventListener('touchstart', (e) => handleTouchStart(e, msg, isMe), {passive: true});
            msgContent.addEventListener('touchend', handleTouchEnd);
            msgContent.addEventListener('touchmove', handleTouchMove);
            
            // Add Long Press Events for Desktop/Simulator (Mouse)
            msgContent.addEventListener('mousedown', (e) => handleTouchStart(e, msg, isMe));
            msgContent.addEventListener('mouseup', handleTouchEnd);
            msgContent.addEventListener('mouseleave', handleTouchEnd);
            msgContent.addEventListener('mousemove', handleTouchMove);

            msgContent.addEventListener('contextmenu', (e) => {
                // Prevent default context menu on mobile long press if we handle it
                // Also prevent on desktop if we are testing mobile view
                if (window.innerWidth <= 768) e.preventDefault();
            });

            dom.chatBody.appendChild(div);
            
            // Ensure typing indicator stays at bottom
            if (dom.typingIndicator && dom.typingIndicator.parentNode === dom.chatBody) {
                dom.chatBody.appendChild(dom.typingIndicator);
            }

            scrollToBottom();
        }

        // --- Touch / Mobile Events ---

        function handleTouchStart(e, msg, isMe) {
            // Support both Touch and Mouse events
            if (e.type === 'touchstart') {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            } else {
                touchStartX = e.clientX;
                touchStartY = e.clientY;
            }
            
            longPressTimer = setTimeout(() => {
                openMobileMenu(msg, isMe);
            }, LONG_PRESS_DURATION);
        }

        function handleTouchEnd(e) {
            clearTimeout(longPressTimer);
        }

        function handleTouchMove(e) {
            // Cancel if moved significantly
            let x, y;
            if (e.type === 'touchmove') {
                x = e.touches[0].clientX;
                y = e.touches[0].clientY;
            } else {
                x = e.clientX;
                y = e.clientY;
            }

            if (Math.abs(x - touchStartX) > 10 || Math.abs(y - touchStartY) > 10) {
                clearTimeout(longPressTimer);
            }
        }

        function openMobileMenu(msg, isMe) {
            // Vibrate if supported
            if (navigator.vibrate) navigator.vibrate(50);
            
            selectedMessageId = msg.id;
            
            // Populate Mobile Menu
            const menu = document.querySelector('.mobile-context-menu');
            if (!menu) return; // Should exist in HTML or be created
            
            const isImageOnly = msg.type === 'image' && (!msg.content || msg.content.trim() === '');
            let menuHtml = '';
            
            // Modifier (Text Only & My Message)
            if (isMe && !isImageOnly) {
                menuHtml += `
                    <div class="mobile-menu-item" onclick="ChatApp.initEditMessage('${msg.id}'); ChatApp.closeMobileMenu()">
                        <i class="mdi mdi-pencil"></i> <?php echo get_phrase('edit'); ?>
                    </div>
                `;
            }

            // Copier (Text Only)
            if (!isImageOnly) {
                 menuHtml += `
                    <div class="mobile-menu-item" onclick="ChatApp.copyMessageContent('${msg.id}'); ChatApp.closeMobileMenu()">
                        <i class="mdi mdi-content-copy"></i> <?php echo get_phrase('copy'); ?>
                    </div>
                `;
            }

            // Supprimer (My Message)
            if (isMe) {
                 menuHtml += `
                    <div class="mobile-menu-item delete" onclick="ChatApp.deleteMessage('${msg.id}'); ChatApp.closeMobileMenu()">
                        <i class="mdi mdi-delete"></i> <?php echo get_phrase('delete'); ?>
                    </div>
                `;
            }
            
            menu.innerHTML = menuHtml;
            
            const overlay = document.querySelector('.mobile-context-overlay');
            if (overlay) {
                overlay.classList.add('active');
                // Ensure we can close it by clicking outside
                overlay.onclick = (e) => {
                    if (e.target === overlay) {
                        closeMobileMenu();
                    }
                };
            }
        }
        
        function closeMobileMenu() {
            const overlay = document.querySelector('.mobile-context-overlay');
            if (overlay) overlay.classList.remove('active');
        }

        // --- Action Handlers ---

        function toggleMessageMenu(msgId, isMe) {
            // Close all others
            document.querySelectorAll('.message-context-menu').forEach(el => {
                if (el.id !== `msg-menu-${msgId}`) el.classList.remove('show');
            });
            
            const menu = document.getElementById(`msg-menu-${msgId}`);
            if (menu) menu.classList.toggle('show');
        }

        function initEditMessage(msgId) {
            const msgEl = document.getElementById(`msg-${msgId}`);
            if (!msgEl) return;
            
            const content = msgEl.dataset.content;
            if (!content) return; // Can't edit images only for now
            
            dom.messageInput.value = content;
            dom.messageInput.focus();
            
            isEditing = true;
            editingMessageId = msgId;
            
            // Change Send Button to Update
            dom.sendBtn.innerHTML = '<i class="mdi mdi-check"></i>';
            
            // Show Cancel Button (create if not exists)
            let cancelBtn = document.getElementById('cancel-edit-btn');
            if (!cancelBtn) {
                cancelBtn = document.createElement('button');
                cancelBtn.id = 'cancel-edit-btn';
                cancelBtn.className = 'btn-cancel-edit'; 
                cancelBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
                cancelBtn.title = '<?php echo get_phrase('cancel_edit'); ?>';
                cancelBtn.onclick = cancelEdit;
                dom.sendBtn.parentNode.appendChild(cancelBtn);
            }
            cancelBtn.style.display = 'flex'; // Use flex to center icon
        }

        function cancelEdit() {
            isEditing = false;
            editingMessageId = null;
            dom.messageInput.value = '';
            dom.sendBtn.innerHTML = '<i class="mdi mdi-send"></i>';
            const cancelBtn = document.getElementById('cancel-edit-btn');
            if (cancelBtn) cancelBtn.style.display = 'none';
        }

        function deleteMessage(msgId) {            
            Swal.fire({
                title: '<?php echo get_phrase('are_you_sure'); ?>',
                text: "<?php echo get_phrase('action_irreversible'); ?>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<?php echo get_phrase('yes_delete'); ?>',
                cancelButtonText: '<?php echo get_phrase('cancel'); ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Optimistic UI
                    const msgEl = document.getElementById(`msg-${msgId}`);
                    if (msgEl) msgEl.style.opacity = '0.5';

                    fetch(`${config.chatServiceUrl}/conversations/${config.currentChatId}/messages/${msgId}`, {
                        method: 'DELETE',
                        headers: {
                            ...getHeaders(),
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (msgEl) msgEl.remove();
                            loadConversations(); // Update sidebar immediately
                            
                            Swal.fire(
                                '<?php echo get_phrase('deleted'); ?>',
                                '<?php echo get_phrase('message_deleted'); ?>',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                '<?php echo get_phrase('error'); ?>',
                                '<?php echo get_phrase('delete_error'); ?>' + (data.message || 'Inconnue'),
                                'error'
                            );
                            if (msgEl) msgEl.style.opacity = '1';
                        }
                    })
                    .catch(err => {
                        console.error('Delete error:', err);
                        Swal.fire(
                            '<?php echo get_phrase('error'); ?>',
                            '<?php echo get_phrase('delete_network_error'); ?>',
                            'error'
                        );
                        if (msgEl) msgEl.style.opacity = '1';
                    });
                }
            });
        }

        function replyToMessage(msgId) {
            // Function kept but unused to prevent breaking if referenced elsewhere
            console.warn('Reply feature disabled');
        }

        function copyMessageContent(msgId) {
            const msgEl = document.getElementById(`msg-${msgId}`);
            if (!msgEl) return;
            const content = msgEl.dataset.content;
            if (content) {
                navigator.clipboard.writeText(content).then(() => {
                    // Toast or feedback could be nice
                });
            }
        }

        // --- Media Handling (Image & Audio) ---

        function handleImageSelect(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate Size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire(
                    '<?php echo get_phrase('error'); ?>',
                    '<?php echo get_phrase('image_size_limit_5mb'); ?>',
                    'error'
                );
                e.target.value = '';
                return;
            }

            // Validate Type
            if (!file.type.startsWith('image/')) {
                Swal.fire(
                    '<?php echo get_phrase('error'); ?>',
                    '<?php echo get_phrase('invalid_image_selection'); ?>',
                    'error'
                );
                e.target.value = '';
                return;
            }

            selectedImageFile = file;
            
            // Preview
            const reader = new FileReader();
            reader.onload = (e) => {
                dom.previewImage.src = e.target.result;
                dom.previewImage.style.display = 'block';
                
                // Remove potential audio preview
                const audioPrev = dom.previewWrapper.querySelector('audio');
                if (audioPrev) audioPrev.remove();
                
                dom.previewContainer.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }

        function removePreview() {
            selectedImageFile = null;
            recordedAudioBlob = null;
            dom.imageInput.value = '';
            dom.previewContainer.style.display = 'none';
            dom.previewImage.src = '';
            dom.previewImage.style.display = 'none';
            
            // Remove audio element if exists
            const audioPrev = dom.previewWrapper.querySelector('audio');
            if (audioPrev) audioPrev.remove();
        }

        function startRecording() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('<?php echo get_phrase('audio_recording_not_supported'); ?>');
                return;
            }

            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.ondataavailable = handleDataAvailable;
                    mediaRecorder.onstop = handleStop;
                    mediaRecorder.start();
                    
                    isRecording = true;
                    recordingAction = 'preview'; // Default action
                    
                    // UI Toggle
                    if (dom.standardInputUI) dom.standardInputUI.style.display = 'none';
                    if (dom.recordingInterface) dom.recordingInterface.style.display = 'flex';
                    
                    // Show Recording State, Hide Preview State
                    if (dom.recordingState) dom.recordingState.style.display = 'flex';
                    if (dom.previewState) dom.previewState.style.display = 'none';
                    
                    // Start Timer
                    if (dom.recordingTimer) dom.recordingTimer.textContent = "00:00";
                    recordingStartTime = Date.now();
                    recordingInterval = setInterval(() => {
                        const elapsed = Date.now() - recordingStartTime;
                        const seconds = Math.floor((elapsed / 1000) % 60);
                        const minutes = Math.floor((elapsed / 1000 / 60));
                        if (dom.recordingTimer) {
                            dom.recordingTimer.textContent = 
                                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        }
                    }, 1000);
                    
                    audioChunks = [];
                })
                .catch(err => {
                    console.error('Error accessing microphone:', err);
                    alert('<?php echo get_phrase('microphone_access_denied'); ?>');
                });
        }

        function cancelRecording() {
            recordingAction = 'cancel';
            stopRecording();
        }
        
        function stopAndPreview() {
            recordingAction = 'preview';
            stopRecording();
        }

        function stopRecording() {
            if (mediaRecorder && isRecording) {
                mediaRecorder.stop();
                isRecording = false;
                
                // Stop Timer
                if (recordingInterval) clearInterval(recordingInterval);
                
                // Stop all tracks to release microphone
                mediaRecorder.stream.getTracks().forEach(track => track.stop());
            }
        }

        function handleDataAvailable(e) {
            if (e.data.size > 0) {
                audioChunks.push(e.data);
            }
        }

        function handleStop() {
            if (recordingAction === 'cancel') {
                audioChunks = []; // Discard
                recordedAudioBlob = null;
                // Reset UI
                if (dom.standardInputUI) dom.standardInputUI.style.display = 'flex';
                if (dom.recordingInterface) dom.recordingInterface.style.display = 'none';
                return;
            }

            // Preview Action
            const blob = new Blob(audioChunks, { type: 'audio/webm' });
            
            // Validate Size (10MB)
            if (blob.size > 10 * 1024 * 1024) {
                alert('<?php echo get_phrase('audio_size_limit_10mb'); ?>');
                audioChunks = [];
                recordedAudioBlob = null;
                 // Reset UI
                if (dom.standardInputUI) dom.standardInputUI.style.display = 'flex';
                if (dom.recordingInterface) dom.recordingInterface.style.display = 'none';
                return;
            }

            recordedAudioBlob = blob;
            
            // Show Preview UI
            if (dom.recordingState) dom.recordingState.style.display = 'none';
            if (dom.previewState) dom.previewState.style.display = 'flex';
            
            // Init Preview Audio
            if (previewAudio) {
                previewAudio.pause();
                previewAudio = null;
            }
            previewAudio = new Audio(URL.createObjectURL(blob));
            
            previewAudio.addEventListener('timeupdate', updatePreviewProgress);
            previewAudio.addEventListener('ended', () => {
                if (dom.previewPlayBtn) dom.previewPlayBtn.innerHTML = '<i class="mdi mdi-play" style="font-size: 24px;"></i>';
                if (dom.previewProgressBar) dom.previewProgressBar.style.width = '0%';
            });
            previewAudio.addEventListener('loadedmetadata', () => {
                 if (dom.previewTimer) {
                      const duration = previewAudio.duration;
                      const seconds = Math.floor(duration % 60);
                      const minutes = Math.floor(duration / 60);
                      dom.previewTimer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                 }
            });
        }
        
        function togglePreviewAudio() {
            if (!previewAudio) return;
            
            if (previewAudio.paused) {
                previewAudio.play();
                if (dom.previewPlayBtn) dom.previewPlayBtn.innerHTML = '<i class="mdi mdi-pause" style="font-size: 24px;"></i>';
            } else {
                previewAudio.pause();
                if (dom.previewPlayBtn) dom.previewPlayBtn.innerHTML = '<i class="mdi mdi-play" style="font-size: 24px;"></i>';
            }
        }
        
        function updatePreviewProgress() {
            if (!previewAudio || !dom.previewProgressBar) return;
            const percent = (previewAudio.currentTime / previewAudio.duration) * 100;
            dom.previewProgressBar.style.width = `${percent}%`;
            
            if (dom.previewTimer) {
                const remaining = previewAudio.duration - previewAudio.currentTime;
                 const seconds = Math.floor(remaining % 60);
                 const minutes = Math.floor(remaining / 60);
                 dom.previewTimer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }
        
        function seekPreviewAudio(e) {
            if (!previewAudio || !dom.previewProgressContainer) return;
            const rect = dom.previewProgressContainer.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const width = rect.width;
            const percent = x / width;
            previewAudio.currentTime = percent * previewAudio.duration;
        }
        
        function discardPreview() {
            if (previewAudio) {
                previewAudio.pause();
                previewAudio = null;
            }
            audioChunks = [];
            recordedAudioBlob = null;
            
            if (dom.standardInputUI) dom.standardInputUI.style.display = 'flex';
            if (dom.recordingInterface) dom.recordingInterface.style.display = 'none';
        }
        
        function sendFinalRecording() {
             if (previewAudio) {
                previewAudio.pause();
                previewAudio = null;
            }
            sendMessage();
            // Reset UI after send
             if (dom.standardInputUI) dom.standardInputUI.style.display = 'flex';
             if (dom.recordingInterface) dom.recordingInterface.style.display = 'none';
             recordedAudioBlob = null;
             audioChunks = [];
        }

        function sendMessage() {
            const content = dom.messageInput.value.trim();
            
            // Allow send if content OR file exists
            if (!content && !selectedImageFile && !recordedAudioBlob) return;

            dom.sendBtn.disabled = true;

            if (isEditing && editingMessageId) {
                // --- UPDATE Logic ---
                // Currently only supports content update
                fetch(`${config.chatServiceUrl}/conversations/${config.currentChatId}/messages/${editingMessageId}`, {
                    method: 'PUT',
                    headers: {
                        ...getHeaders(),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ content: content })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update UI locally
                        const msgEl = document.getElementById(`msg-${editingMessageId}`);
                        if (msgEl) {
                            msgEl.dataset.content = content;
                            const span = msgEl.querySelector('.message-content > span');
                            if (span) {
                                // Re-link URLs
                                const linkedContent = escapeHtml(content).replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
                                span.innerHTML = linkedContent;
                            }
                            
                            // Update meta for "modifié"
                            const metaDiv = msgEl.querySelector('.message-meta');
                            if (metaDiv) {
                                const timeSpan = metaDiv.querySelector('span');
                                if (timeSpan && !timeSpan.innerHTML.includes('edited-indicator')) {
                                    timeSpan.insertAdjacentHTML('beforeend', ' <span class="edited-indicator" style="font-size: 0.75rem; color: #adb5bd;">(<?php echo get_phrase('edited'); ?>)</span>');
                                }
                            }
                        }
                        loadConversations(); // Update sidebar immediately
                        cancelEdit();
                    }
                })
                .finally(() => {
                    dom.sendBtn.disabled = false;
                });
                return;
            }

            // --- CREATE Logic ---
            
            const formData = new FormData();
            formData.append('content', content);
            
            let $type = 'text';
            if (selectedImageFile) { $type = 'image';
                formData.append('file', selectedImageFile);
            } else if (recordedAudioBlob) { $type = 'audio';
                formData.append('file', recordedAudioBlob, 'audio.webm');
                // Optional: calculate duration if needed
            }
            formData.append('type', type);

            // Optimistic UI (Text only for now, skip for files to avoid complex blob handling)
            let tempId = null;
            if (content && !selectedImageFile && !recordedAudioBlob) {
                tempId = 'temp-' + Date.now();
                appendMessage({
                    id: tempId,
                    content: content,
                    type: 'text',
                    created_at: new Date().toISOString(),
                    user_id: config.currentUser.chat_id,
                    user: {
                        user_id: config.currentUser.id
                    }
                }, true);
                scrollToBottom();
                dom.messageInput.value = '';
            }

            const headers = {
                'Authorization': 'Bearer ' + config.authToken,
                'X-Application-ID': config.appId,
                'Accept': 'application/json'
                // Content-Type must NOT be set for FormData, browser sets it with boundary
            };

            // Fetch with FormData
            // Note: We cannot use JSON headers here
            fetch(`${config.chatServiceUrl}/conversations/${config.currentChatId}/messages`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + config.authToken,
                    'X-Application-ID': config.appId
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Replace temp message with real one
                    if (tempId) {
                        const tempMsgEl = document.getElementById(`msg-${tempId}`);
                        if (tempMsgEl) tempMsgEl.remove();
                    }
                    
                    // Clear inputs and preview
                    dom.messageInput.value = '';
                    removePreview();
                    
                    appendMessage(data.data, true);
                    scrollToBottom(); // Scroll for new file messages
                    loadConversations(); // Update sidebar
                } else {
                    console.error('Send failed', data);
                    alert('<?php echo get_phrase('error_sending_message_colon'); ?>' + (data.message || 'Inconnue'));
                    if (data.errors) console.error(data.errors);
                }
            })
            .catch(err => {
                console.error('Send Error:', err);
                alert('<?php echo get_phrase('network_error_sending'); ?>');
            })
            .finally(() => {
                dom.sendBtn.disabled = false;
                dom.messageInput.focus();
            });
        }

        // --- Search ---

        function handleSearch(e) {
            const term = e.target.value.trim();
            if (term.length < 2) {
                dom.searchResults.style.display = 'none';
                return;
            }

            // Show loading state
            dom.searchResults.innerHTML = `
                <div class="text-center p-3">
                    <div class="spinner-border text-primary spinner-border-sm" role="status">
                        <span class="sr-only"><?php echo get_phrase('loading_ellipsis'); ?></span>
                    </div>
                </div>
            `;
            dom.searchResults.style.display = 'block';

            fetch(`${config.chatServiceUrl}/users/search?term=${encodeURIComponent(term)}`, {
                headers: getHeaders()
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    renderSearchResults(data.data);
                } else {
                    dom.searchResults.innerHTML = `
                        <div class="p-3 text-center text-muted">
                            <i class="mdi mdi-account-off-outline mb-1" style="font-size: 20px;"></i>
                            <div style="font-size: 13px;"><?php echo get_phrase('no_user_found'); ?></div>
                        </div>
                    `;
                }
            })
            .catch(err => {
                dom.searchResults.innerHTML = '<div class="p-3 text-danger text-center"><?php echo get_phrase('search_error'); ?></div>';
            });
        }

        function renderSearchResults(users) {
            dom.searchResults.innerHTML = '';
            users.forEach(user => {
                // Determine status (mock logic or real if available)
                const isOnline = user.is_online || false; // Assume property exists or default to false
                const statusClass = isOnline ? 'online' : 'offline';
                const statusText = isOnline ? '<?php echo get_phrase('online'); ?>' : '<?php echo get_phrase('offline'); ?>';
                
                // Format date (e.g., joined date or placeholder)
                const dateText = user.created_at ? new Date(user.created_at).toLocaleDateString() : '';

                const html = `
                    <div class="search-result-card" data-user-id="${user.id}" onclick="ChatApp.createConversation(${user.id})">
                        <div class="search-result-avatar-wrapper">
                            <img src="${user.avatar || config.baseUrl + 'uploads/users/placeholder.jpg'}" class="search-result-avatar">
                        </div>
                        <div class="search-result-info">
                            <div class="search-result-name">${escapeHtml(user.name)}</div>
                            <div class="search-result-meta">
                                <span class="status-badge ${statusClass}">
                                    <span class="status-dot ${statusClass}"></span>
                                    ${statusText}
                                </span>
                                ${dateText ? `<span>• ${dateText}</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
                dom.searchResults.insertAdjacentHTML('beforeend', html);
            });
            dom.searchResults.style.display = 'block';
        }

        function createConversation(otherUserId) { // chat-service user ID
            // Close search
            dom.searchResults.style.display = 'none';
            dom.searchInput.value = '';

            // Create conversation via API
            fetch(`${config.chatServiceUrl}/conversations`, {
                method: 'POST',
                headers: {
                    ...getHeaders(),
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    type: 'direct',
                    participant_ids: [otherUserId] // chat-service user ID
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadConversations();
                    openConversation(data.data.id, data.data.display_name, data.data.display_avatar);
                }
            });
        }

        // --- Helpers ---
        function markAsRead(conversationId) {
            if (!conversationId) return;
            
            fetch(`${config.chatServiceUrl}/conversations/${conversationId}/read`, {
                method: 'POST',
                headers: {
                    ...getHeaders(),
                    'Content-Type': 'application/json'
                }
            })
            .catch(err => console.error('Mark Read Error:', err));
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);
            
            // Normalize dates to midnight for comparison
            const d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            const t = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const y = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate());
            
            // Helper for time
            const timeStr = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });

            // 1. Aujourd'hui
            if (d.getTime() === t.getTime()) {
                return timeStr;
            }

            // 2. Hier
            if (d.getTime() === y.getTime()) {
                return `<?php echo get_phrase('yesterday'); ?> ${timeStr}`;
            }

            // 3. Cette semaine (lundi à dimanche)
            const dayOfWeek = t.getDay() || 7; // 1 (Mon) - 7 (Sun)
            const startOfWeek = new Date(t);
            startOfWeek.setDate(t.getDate() - dayOfWeek + 1);
            
            if (d >= startOfWeek) {
                const days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
                return `${days[date.getDay()]} ${timeStr}`;
            }

            // 4. Mois en cours (et année en cours)
            if (date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear()) {
                 return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')} ${timeStr}`;
            }

            // 5. Plus ancien
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear().toString().slice(-2); // YY
            return `${day}/${month}/${year}`;
        }
        
        function formatTime(dateStr) {
             return new Date(dateStr).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
        }

        function toggleScrollButton() {
            if (!dom.chatBody || !dom.scrollToBottomBtn) return;
            
            const threshold = 100; // Show if more than 100px from bottom
            const position = dom.chatBody.scrollTop + dom.chatBody.clientHeight;
            const height = dom.chatBody.scrollHeight;
            
            if (height - position > threshold) {
                dom.scrollToBottomBtn.classList.add('visible');
            } else {
                dom.scrollToBottomBtn.classList.remove('visible');
            }
        }

        function scrollToBottom(instant = false) {
            if (dom.chatBody) {
                dom.chatBody.scrollTo({
                    top: dom.chatBody.scrollHeight,
                    behavior: instant ? 'auto' : 'smooth'
                });
                // Force hide when scrolling to bottom programmatically
                if (dom.scrollToBottomBtn) dom.scrollToBottomBtn.classList.remove('visible');
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function truncate(str, n) {
            return (str.length > n) ? str.substr(0, n-1) + '&hellip;' : str;
        }
        
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        function openLightbox(imageUrl) {
            if (dom.lightboxImage && dom.lightboxOverlay) {
                dom.lightboxImage.src = imageUrl;
                dom.lightboxOverlay.classList.add('active');
            }
        }

        function closeLightbox() {
            if (dom.lightboxOverlay) {
                dom.lightboxOverlay.classList.remove('active');
                setTimeout(() => {
                     if(dom.lightboxImage) dom.lightboxImage.src = '';
                }, 300); // Wait for transition
            }
        }

        function bindEvents() {
             dom.sendBtn.addEventListener('click', sendMessage);
             dom.messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') sendMessage();
             });
             dom.messageInput.addEventListener('input', () => {
                 sendTypingEvent(true);
                 
                 // Debounce stop typing
                 clearTimeout(typingTimeout);
                 typingTimeout = setTimeout(() => {
                     sendTypingEvent(false);
                 }, 3000);
             });
             dom.searchInput.addEventListener('input', debounce(handleSearch, 300));
             
             // Media Events
             if (dom.imageBtn) dom.imageBtn.addEventListener('click', () => dom.imageInput.click());
             if (dom.imageInput) dom.imageInput.addEventListener('change', handleImageSelect);
             if (dom.removePreviewBtn) dom.removePreviewBtn.addEventListener('click', removePreview);
             if (dom.recordBtn) dom.recordBtn.addEventListener('click', startRecording);
             
             // Modern Recording Events
             if (dom.cancelRecordBtn) dom.cancelRecordBtn.addEventListener('click', cancelRecording);
             if (dom.stopRecordBtn) dom.stopRecordBtn.addEventListener('click', stopAndPreview);
             
             if (dom.discardRecordBtn) dom.discardRecordBtn.addEventListener('click', discardPreview);
             if (dom.sendRecordBtnFinal) dom.sendRecordBtnFinal.addEventListener('click', sendFinalRecording);
             if (dom.previewPlayBtn) dom.previewPlayBtn.addEventListener('click', togglePreviewAudio);
             if (dom.previewProgressContainer) dom.previewProgressContainer.addEventListener('click', seekPreviewAudio);

             // Scroll Button Events
             if (dom.chatBody) dom.chatBody.addEventListener('scroll', debounce(toggleScrollButton, 100));
             if (dom.scrollToBottomBtn) dom.scrollToBottomBtn.addEventListener('click', () => scrollToBottom(false));

             // Close menus on click outside
             document.addEventListener('click', (e) => {
                 // Close desktop menu if click is NOT on trigger AND NOT on menu
                 if (!e.target.closest('.message-actions') && !e.target.closest('.message-context-menu')) {
                     document.querySelectorAll('.message-context-menu').forEach(el => el.classList.remove('show'));
                 }
                 if (!e.target.closest('.mobile-context-menu')) {
                     closeMobileMenu();
                 }
             });
             
             // Close mobile menu on overlay click
             const overlay = document.querySelector('.mobile-context-overlay');
             if (overlay) {
                 overlay.addEventListener('click', (e) => {
                     if (e.target === overlay) closeMobileMenu();
                 });
             }
        }
        
        function showTypingIndicator(name, conversationId = null) {
             // 1. Sidebar Indicator
             if (conversationId) {
                 const item = document.getElementById(`conversation-item-${conversationId}`);
                 if (item) {
                     const lastMsgEl = item.querySelector('.user-last-msg');
                     if (lastMsgEl) {
                         // Save original text if not already saved
                         if (!lastMsgEl.dataset.originalText) {
                             lastMsgEl.dataset.originalText = lastMsgEl.innerHTML;
                         }
                         lastMsgEl.innerHTML = '<span style="color: #6b7280; font-style: italic;"><?php echo get_phrase("is_typing"); ?>...</span>';
                     }
                 }
             }

             // 2. Active Conversation Indicators (Header & Body)
             // Only show if we are in the relevant conversation
             if (config.currentChatId && conversationId && String(config.currentChatId) !== String(conversationId)) {
                 return; // Typing in another conversation, only sidebar updates
             }

             // Header
             const headerIndicator = document.getElementById('header-typing-indicator');
             const statusText = document.getElementById('active-user-status');
             if (headerIndicator) {
                 headerIndicator.style.display = 'block';
                 if (statusText) statusText.style.display = 'none';
             }

             // Chat Body (Bubble)
             if(dom.typingIndicator) {
                 // Reset inner HTML if it was text-based previously
                 // dom.typingIndicator.innerHTML = ... (Already set in index.php)
                 dom.typingIndicator.style.display = 'flex';
                 scrollToBottom(); // Ensure visibility
             }
        }
        
        function hideTypingIndicator(conversationId = null) {
             // 1. Sidebar Indicator
             if (conversationId) {
                 const item = document.getElementById(`conversation-item-${conversationId}`);
                 if (item) {
                     const lastMsgEl = item.querySelector('.user-last-msg');
                     if (lastMsgEl && lastMsgEl.dataset.originalText) {
                         lastMsgEl.innerHTML = lastMsgEl.dataset.originalText;
                         delete lastMsgEl.dataset.originalText;
                     }
                 }
             }

             // 2. Active Conversation Indicators
             if (config.currentChatId && conversationId && String(config.currentChatId) !== String(conversationId)) {
                 return;
             }

             // Header
             const headerIndicator = document.getElementById('header-typing-indicator');
             const statusText = document.getElementById('active-user-status');
             if (headerIndicator) {
                 headerIndicator.style.display = 'none';
                 if (statusText) statusText.style.display = 'block';
             }

             // Chat Body
             if(dom.typingIndicator) dom.typingIndicator.style.display = 'none';
        }

        function backToConversations() {
            document.querySelector('.chat-wrapper').classList.remove('mobile-chat-active');
            // Optional: Reset active state in sidebar if desired, but keeping it selected is fine
        }

        // Public API
        return {
            init: init,
            openConversation: openConversation,
            backToConversations: backToConversations,
            createConversation: createConversation,
            toggleMessageMenu: toggleMessageMenu,
            initEditMessage: initEditMessage,
            deleteMessage: deleteMessage,
            replyToMessage: replyToMessage,
            copyMessageContent: copyMessageContent,
            closeMobileMenu: closeMobileMenu,
            openLightbox: openLightbox,
            closeLightbox: closeLightbox,
            toggleAudio: toggleAudio,
            onAudioLoaded: onAudioLoaded,
            onAudioEnded: onAudioEnded,
            onAudioTimeUpdate: onAudioTimeUpdate,
            seekAudio: seekAudio
        };
    })();

    document.addEventListener('DOMContentLoaded', ChatApp.init);
    
    // Ensure global access for inline event handlers
    window.ChatApp = ChatApp;
</script>
       
