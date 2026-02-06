<?php include 'styles.php'; ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/backend/css/font-awesome.min.css">
<!-- SweetAlert -->
<script src="<?php echo base_url(); ?>assets/backend/js/sweetalert.min.js"></script>
    
<div class="row ">
    <div class="col-xl-12">
        <div class="d-flex align-items-center mb-3 chat-page-header">
            <div class="title-icon-box">
                <i class="fas fa-comments fa-fw fa-fw"></i>
            </div>
            <h4 class="page-title ms-2 my-2"><?php echo get_phrase('chat'); ?></h4>
        </div>
    </div>
</div>

<div class="chat-wrapper">
    <!-- PANNEAU GAUCHE : Liste des conversations (30%) -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <h5 class="d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 20px; height: 20px; color :#6366f1; font-size: 1.125rem;"">
                    <path fill-rule="evenodd" d="M3 6a3 3 0 0 1 3-3h2.25a3 3 0 0 1 3 3v2.25a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm9.75 0a3 3 0 0 1 3-3H18a3 3 0 0 1 3 3v2.25a3 3 0 0 1-3 3h-2.25a3 3 0 0 1-3-3V6ZM3 15.75a3 3 0 0 1 3-3h2.25a3 3 0 0 1 3 3V18a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-2.25Zm9.75 0a3 3 0 0 1 3-3H18a3 3 0 0 1 3 3V18a3 3 0 0 1-3 3h-2.25a3 3 0 0 1-3-3v-2.25Z" clip-rule="evenodd" />
                </svg>
                <?php echo get_phrase('conversations'); ?>
            </h5>
            <div class="sidebar-separator"></div>
            <div class="chat-search-box">
                <input type="text" id="user-search-input" placeholder="<?php echo get_phrase('search_user_placeholder'); ?>">
                <div id="search-results" class="dropdown-menu" style="display:none; width: 100%; border:none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-top:5px;"></div>
            </div>
        </div>
        
     <div class="chat-list-container" id="chat-list">
    <div class="text-center p-4">
        <div class="d-flex flex-column align-items-center">
            <div class="spinner-border text-primary mb-2" style="width: 2rem; height: 2rem;" role="status">
                
            </div>
            <span><?php echo get_phrase('loading'); ?></span>
        </div>
    </div>
</div>
    </div>

    <!-- PANNEAU DROIT : Conversation active (70%) -->
    <div class="chat-main">
        <!-- Placeholder si aucune conversation sélectionnée -->
        <div id="chat-placeholder" class="empty-chat-placeholder">
            <div class="empty-chat-icon">
                <i class="mdi mdi-chat-processing-outline" style="font-size: 50px; color: #ffffff;"></i>
            </div>
            <h5 class="mt-3" style="font-weight:600; color:#374151;"><?php echo get_phrase('select_conversation'); ?></h5>
            <p style="font-size:14px; max-width:300px; margin: 10px auto 0;"><?php echo get_phrase('select_conversation_desc'); ?></p>
        </div>

        <!-- Contenu conversation (caché par défaut) -->
        <div id="chat-active-container" style="display:none; height:100%; flex-direction:column; position: relative;">
            <!-- Header Conversation -->
            <div class="chat-header">
                <div class="user-info-header">
                    <div class="user-avatar" style="margin-right: 12px;">
                        <img id="active-user-avatar" src="" alt="" style="width:40px; height:40px;">
                        <span id="active-user-indicator" class="user-status status-offline" style="width:10px; height:10px; border-width:2px;"></span>
                    </div>
                    <div class="header-details">
                        <div class="d-flex align-items-center">
                            <h4 id="active-user-name" class="mb-0">User Name</h4>
                        </div>
                        <p id="active-user-status" class="text-muted mb-0" style="margin-top: 2px;"><?php echo get_phrase('online'); ?></p>
                        <p id="header-typing-indicator" class="text-muted mb-0" style="display:none; margin-top: 2px; font-style: italic; color: #6b7280;"><?php echo get_phrase('is_typing'); ?>...</p>
                    </div>
                </div>
               
            </div>

            <!-- Zone des messages -->
            <div class="chat-body" id="chat-body">
                <!-- Messages loaded here -->
                
                <!-- Typing indicator -->
                <div id="typing-indicator" class="message-item incoming" style="display:none; margin-bottom: 12px;">
                    <div class="message-content typing-bubble">
                        <div class="typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll to Bottom Button -->
            <div id="scroll-to-bottom-btn" class="scroll-to-bottom-btn">
                <i class="mdi mdi-arrow-down"></i>
            </div>

            <!-- Zone de saisie -->
            <div class="chat-footer">
                <!-- Image Preview -->
                <div id="image-preview-container" class="image-preview-container" style="display:none;">
                    <div class="preview-wrapper" id="preview-wrapper">
                        <img id="image-preview" src="" alt="Preview">
                        <div id="compression-status" class="compression-status" style="display:none;">
                            <i class="mdi mdi-loading mdi-spin"></i>
                            <span><?php echo get_phrase('optimizing_image'); ?></span>
                            <small><?php echo get_phrase('preparing_sending'); ?></small>
                        </div>
                        <div id="compression-info" class="compression-info" style="display:none;"></div>
                        <button type="button" class="btn-remove-preview" id="btn-remove-preview">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                </div>

                <!-- Recording Indicator (Hidden by default, used for logic if needed or removed entirely if logic updated) -->
                <!-- Old indicator removed/commented out for modern UI -->

                <div class="chat-input-group">
                    <!-- Standard Input Interface -->
                    <div id="standard-input-ui" style="display: flex; flex: 1; align-items: center; width: 100%;">
                        <button type="button" class="btn-icon" id="btn-image" title="<?php echo get_phrase('send_image'); ?>">
                            <i class="fas fa-cloud-upload-alt gradient-icon"></i>
                        </button>
                        <input type="file" id="image-input" style="display:none" accept="image/*">
    
                        <button type="button" class="btn-icon" id="btn-record" title="<?php echo get_phrase('record_audio'); ?>">
                            <i class="fa-solid fa-microphone gradient-icon"></i>
                        </button>
    
                        <input type="text" class="chat-input" id="message-input" placeholder="<?php echo get_phrase('type_message_placeholder'); ?>" autocomplete="off">
    
                        <button type="button" class="btn-send" id="btn-send">
                            <i class="mdi mdi-send"></i>
                        </button>
                    </div>

                    <!-- Modern Recording Interface -->
                    <div id="recording-interface" class="recording-interface" style="display: none;">
                        <!-- State 1: Active Recording -->
                        <div id="recording-state" class="recording-state" style="display: flex; width: 100%; align-items: center;">
                            <button type="button" class="btn-icon btn-cancel-record" id="btn-cancel-record" title="<?php echo get_phrase('cancel'); ?>">
                                <i class="mdi mdi-delete-outline" style="color: #ef4444; font-size: 22px;"></i>
                            </button>
                            
                            <div class="recording-visualizer">
                                <div class="recording-status">
                                    <div class="recording-dot-active"></div>
                                    <span id="recording-timer-modern">00:00</span>
                                </div>
                                <div class="audio-wave">
                                    <span></span><span></span><span></span><span></span><span></span>
                                </div>
                            </div>

                            <button type="button" class="btn-send-record" id="btn-stop-record" title="<?php echo get_phrase('stop_and_listen'); ?>" style="background: #ef4444;">
                                <i class="mdi mdi-stop"></i>
                            </button>
                        </div>

                        <!-- State 2: Preview Recording -->
                        <div id="recording-preview-state" class="recording-preview-state" style="display: none; width: 100%; align-items: center; justify-content: space-between;">
                             <button type="button" class="btn-icon btn-cancel-record" id="btn-discard-record" title="<?php echo get_phrase('delete'); ?>">
                                <i class="mdi mdi-delete-outline" style="color: #ef4444; font-size: 22px;"></i>
                            </button>
                            
                            <div class="preview-player" style="flex: 1; display: flex; align-items: center; margin: 0 8px; background: rgba(109, 115, 243, 0.1); padding: 6px 12px; border-radius: 25px;">
                                 <button type="button" id="btn-preview-play" style="border:none; background:none; cursor:pointer; color: #6D73F3; display: flex; align-items: center; justify-content: center; padding: 0; margin-right: 10px;">
                                     <i class="mdi mdi-play" style="font-size: 24px;"></i>
                                 </button>
                                 <div id="preview-progress-container" style="flex:1; height: 4px; background: rgba(109, 115, 243, 0.2); border-radius: 2px; position: relative; cursor: pointer;">
                                     <div id="preview-progress-bar" style="width: 0%; height: 100%; background: #6D73F3; border-radius: 2px; transition: width 0.1s linear;"></div>
                                 </div>
                                 <span id="preview-timer" style="font-size: 12px; margin-left: 10px; color: #6D73F3; font-weight: 500; font-variant-numeric: tabular-nums;">00:00</span>
                            </div>

                            <button type="button" class="btn-send-record" id="btn-send-record-final" title="<?php echo get_phrase('send'); ?>">
                                <i class="mdi mdi-send"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Context Menu Overlay -->
<div id="mobile-context-overlay" class="mobile-context-overlay">
    <div class="mobile-context-menu">
        <div class="mobile-menu-item" id="mobile-copy">
            <i class="mdi mdi-content-copy"></i> <?php echo get_phrase('copy'); ?>
        </div>
        <div class="mobile-menu-item" id="mobile-info" style="display:none">
            <i class="mdi mdi-information-outline"></i> <?php echo get_phrase('message_info'); ?>
        </div>
        <div class="mobile-menu-item" id="mobile-edit">
            <i class="mdi mdi-pencil"></i> <?php echo get_phrase('edit'); ?>
        </div>
        <div class="mobile-menu-item delete" id="mobile-delete">
            <i class="mdi mdi-delete"></i> <?php echo get_phrase('delete'); ?>
        </div>
    </div>
</div>

<!-- Lightbox Overlay -->
<div id="lightbox-overlay" class="lightbox-overlay" onclick="ChatApp.closeLightbox()">
    <span class="lightbox-close" onclick="ChatApp.closeLightbox()">&times;</span>
    <img class="lightbox-image" id="lightbox-image" src="" alt="Full size image">
</div>

<!-- Bibliothèques Real-time -->
<script src="<?php echo base_url('assets/backend/js/pusher.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/backend/js/echo.iife.min.js'); ?>"></script>

<script>
    var CURRENT_USER_ID = <?php echo $current_user_id; ?>;
    var BASE_URL = '<?php echo base_url(); ?>';
    var API_URL = '<?php echo base_url("app/chat"); ?>';
</script>

<?php include 'scripts.php'; ?>
