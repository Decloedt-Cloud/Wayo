<?php
// Script Global de Notification Chat
// Ce script gère l'affichage de la bulle de notification dans la sidebar
// Il est inclus dans le layout global (includes_bottom.php)

$user_id = $this->session->userdata('user_id');
$wap_token = '';

if ($user_id) {
    // Génération du Token WAP pour l'authentification cross-service
    if (file_exists(APPPATH . '/libraries/TokenHandler.php')) {
        require_once APPPATH . '/libraries/TokenHandler.php';
        $tokenHandler = new TokenHandler();
        $tokenData = ['user_id' => $user_id, 'issued_at' => time()];
        try {
            $wap_token = $tokenHandler->GenerateToken($tokenData);
        } catch (Exception $e) {
            log_message('error', 'GlobalChatNotification: Token generation failed - ' . $e->getMessage());
        }
    }
}
?>

<!-- Chargement conditionnel des bibliothèques Reverb (si pas sur la page chat) -->
<?php 
$is_chat_page = (strpos($_SERVER['REQUEST_URI'], 'app/chat') !== false || strpos($_SERVER['REQUEST_URI'], 'chat/index') !== false);
if (!$is_chat_page): 
?>
<script src="<?php echo base_url('assets/backend/js/pusher.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/backend/js/echo.iife.js'); ?>"></script>
<?php endif; ?>

<script>
(function() {
    // Namespace GlobalChatNotifier pour éviter les conflits
    const GlobalChatNotifier = {
        config: {
            chatServiceUrl: 'https://chat.wayo.site/api/v1',
            authUrl: 'https://chat.wayo.site/api/auth/cross-auth',
            wapToken: '<?php echo $wap_token; ?>',
            appId: 'wayo',
            authToken: null,
            userId: '<?php echo $user_id; ?>',
            chatId: null, // ID interne du Chat Service
            debug: true // Active les logs
        },
        
        bubble: null,
        interval: null,
        echo: null,
        currentTotalUnread: 0, // Stockage local du nombre de messages

        log: function(msg, data = null) {
            if (this.config.debug) {
                if (data) console.log(`[ChatNotify] ${msg}`, data);
                else console.log(`[ChatNotify] ${msg}`);
            }
        },

        init: function() {
            this.bubble = document.getElementById('chat-notification-bubble');
            if (!this.bubble) {
                this.log('Element #chat-notification-bubble not found in DOM.');
                return;
            }

            // Si on est sur la page de chat, on cache la bulle et on ne lance pas le polling
            const currentUrl = window.location.href.toLowerCase();
            if (currentUrl.includes('/app/chat') || currentUrl.includes('chat/index')) {
                this.log('Current page is Chat. Hiding bubble.');
                this.bubble.style.display = 'none';
                return;
            }

            // Gestion du clic sur le lien Chat (via le parent direct de la bulle)
            const parentLink = this.bubble.closest('a');
            const handleChatClick = () => {
                this.log('Chat link clicked. Hiding bubble and acknowledging messages.');
                this.bubble.style.display = 'none';
                
                // On acquitte tous les messages actuellement connus
                localStorage.setItem('chat_ack_count', this.currentTotalUnread);
                
                // Optionnel : empêcher le clignotement au rechargement via sessionStorage
                sessionStorage.setItem('chat_bubble_hidden', 'true');
            };

            if (parentLink) {
                this.log('Click listener attached to parent link', parentLink);
                parentLink.addEventListener('click', handleChatClick);
            } else {
                // Fallback sur sélecteur générique si le parent n'est pas un lien
                const chatLink = document.querySelector('a[href*="app/chat"]');
                if (chatLink) {
                    chatLink.addEventListener('click', handleChatClick);
                }
            }
            
            // Vérifier si on doit masquer temporairement (juste après le clic)
            if (sessionStorage.getItem('chat_bubble_hidden') === 'true') {
                 // On vérifie si on est toujours en navigation vers le chat
                 // Si on est revenu sur une autre page, on peut vouloir réactiver
                 // Mais ici on laisse le checkUnread décider plus tard si c'est pertinent
                 // Pour l'instant on nettoie le flag si on n'est PAS sur le chat (pour permettre la réapparition)
                 if (!currentUrl.includes('/app/chat') && !currentUrl.includes('chat/index')) {
                     sessionStorage.removeItem('chat_bubble_hidden');
                 }
            }

            if (this.config.wapToken && this.config.userId) {
                this.authenticateAndCheck();
                // Garder le polling en backup (toutes les 60s au lieu de 30s)
                this.interval = setInterval(() => this.checkUnread(), 60000);
            } else {
                this.log('Missing WAP Token or User ID.', { token: !!this.config.wapToken, userId: this.config.userId });
            }
        },

        authenticateAndCheck: function() {
            this.log('Authenticating...');
            // Authentification Cross-Auth
            fetch(this.config.authUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Application-ID': this.config.appId,
                    'Authorization': 'Bearer ' + this.config.wapToken
                },
                body: JSON.stringify({ wap_token: this.config.wapToken })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.log('Auth success. Token received.');
                    this.config.authToken = data.data.token;
                    this.config.chatId = data.data.user.id; // Récupération de l'ID Chat
                    
                    // 1. Vérification initiale immédiate
                    this.checkUnread();
                    
                    // 2. Initialisation WebSockets pour temps réel
                    this.initReverb();
                } else {
                    this.log('Auth failed.', data);
                }
            })
            .catch(err => {
                this.log('Auth error.', err);
            });
        },

        initReverb: function() {
            if (typeof Echo === 'undefined') {
                this.log('Echo library not loaded.');
                return;
            }

            this.log('Initializing Reverb...');
            try {
                this.echo = new Echo({
                    broadcaster: 'reverb',
                    key: 'iuvcjjlml7xkwbdfaxo3',
                    wsHost: 'chat.wayo.site',
                    wsPort: 443,
                    wssPort: 443,
                    forceTLS: true,
                    enabledTransports: ['ws', 'wss'],
                    authEndpoint: this.config.chatServiceUrl + '/broadcasting/auth',
                    auth: {
                        headers: {
                            Authorization: 'Bearer ' + this.config.authToken,
                            'X-Application-ID': this.config.appId
                        }
                    }
                });

                const channelName = `user.${this.config.chatId}.${this.config.appId}`;
                this.log(`Subscribing to channel: ${channelName}`);

                this.echo.private(channelName)
                    .listen('.message.sent', (e) => {
                        this.log('⚡ Real-time message received!', e);
                        // On incrémente arbitrairement pour forcer l'affichage (le vrai compte sera mis à jour au prochain poll)
                        this.currentTotalUnread++; 
                        // Affichage immédiat de la bulle car c'est un nouveau message
                        this.showBubble();
                    });

                // Join Global Presence Channel to mark user as Online
                const presenceChannel = `global.${this.config.appId}`;
                this.log(`Joining presence channel: ${presenceChannel}`);
                
                this.echo.join(presenceChannel)
                    .here((users) => {
                        this.log(`✅ Presence Joined. ${users.length} users online.`);
                    })
                    .error((error) => {
                        this.log('⚠️ Presence Channel Error:', error);
                    });

            } catch (e) {
                this.log('Error initializing Reverb:', e);
            }
        },

        showBubble: function() {
            if (this.bubble) {
                this.bubble.style.display = 'inline-block';
                // Petit effet "pop" pour attirer l'attention (optionnel, géré par CSS animation)
            }
        },

        checkUnread: function() {
            // Sécurité supplémentaire : si on est sur la page chat, on n'affiche rien
            const currentUrl = window.location.href.toLowerCase();
            if (currentUrl.includes('/app/chat') || currentUrl.includes('chat/index')) {
                return;
            }

            if (!this.config.authToken) return;

            // this.log('Checking unread messages...');
            fetch(`${this.config.chatServiceUrl}/conversations`, {
                headers: {
                    'Authorization': 'Bearer ' + this.config.authToken,
                    'X-Application-ID': this.config.appId,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    let totalUnread = 0;
                    
                    // Calcul du total réel des messages non lus
                    for (let i = 0; i < data.data.length; i++) {
                        if (data.data[i].unread_count) {
                            totalUnread += parseInt(data.data[i].unread_count);
                        }
                    }

                    this.currentTotalUnread = totalUnread;
                    const acknowledgedCount = parseInt(localStorage.getItem('chat_ack_count') || '0');

                    // this.log(`Unread status: Total=${totalUnread}, Ack=${acknowledgedCount}`);

                    // Si le nombre total de messages a diminué (lecture ailleurs), on met à jour l'acquittement
                    // pour éviter de bloquer les futures notifs si on retombe à 0 puis remonte
                    if (totalUnread < acknowledgedCount) {
                        localStorage.setItem('chat_ack_count', totalUnread);
                    }

                    // On affiche la bulle SEULEMENT s'il y a plus de messages que ce qu'on a déjà "vu"
                    if (totalUnread > 0 && totalUnread > acknowledgedCount) {
                        this.showBubble();
                    } else {
                        this.bubble.style.display = 'none';
                    }
                }
            })
            .catch(err => {
                this.log('Fetch conversations error', err);
            });
        }
    };

    // Démarrage au chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => GlobalChatNotifier.init());
    } else {
        GlobalChatNotifier.init();
    }
})();
</script>
