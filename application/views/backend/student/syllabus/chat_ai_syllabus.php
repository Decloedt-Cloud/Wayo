<style>
    .chat-container {
        max-width: 900px;
        margin: 0 auto;
        height: 80vh;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .chat-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .messages-container {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f8fafc;
    }

    .message {
        margin-bottom: 25px;
        display: flex;
        gap: 12px;
    }

    .message.user {
        flex-direction: row-reverse;
    }

    .message.assistant {
        flex-direction: row;
        align-items: flex-start;
    }

    .message-content {
        max-width: 100%;
        padding: 14px 18px;
        border-radius: 18px;
        line-height: 1.6;
    }

    .quiz-question {
        font-weight: 600;
        margin: 20px 0 10px 0;
        color: #1e293b;
    }

    .quiz-response {
        font-weight: 500;
        margin: 15px 0 25px 20px;
        color: #334155;
    }

    .message-wrapper {
        display: flex;
        flex-direction: column;
        max-width: 70%;
    }

    .message-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        font-size: 12px;
    }

    .message.user .message-header {
        justify-content: flex-end;
    }

    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
    }

    .profile-pic.user {
        background: linear-gradient(135deg, #f47a1f, #fbb040);
        border-color: #FFFFFF;
    }

    .profile-pic.assistant {
        background: #FFFFFF;
        border-color: #f47a1f;
    }

    .message.user .message-content {
        background: linear-gradient(90deg, #f47a1f 0%, #fbb040 100%);
        color: white;
        border-bottom-right-radius: 6px;
        margin-right: 0;
    }

    .message.assistant .message-content {
        background: white;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 6px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .message-time {
        color: #94a3b8;
        font-size: 11px;
        margin-left: 8px;
    }

    .message.user .message-time {
        margin-right: 8px;
        margin-left: 0;
    }

    .input-section {
        padding: 20px;
        background: white;
        border-top: 1px solid #e2e8f0;
    }

    .question-input {
        width: 100%;
        min-height: 60px;
        max-height: 150px;
        padding: 16px 60px 16px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 0 0 12px 12px;
        font-size: 16px;
        resize: none;
        outline: none;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
        border-top: none;
    }

    .question-input:focus {
        border-color: #fbb040;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .send-btn-integrated {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        padding: 10px 11px;
        background: linear-gradient(90deg, #f47a1f 0%, #fbb040 100%);
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .send-btn-integrated:hover:not(:disabled) {
        transform: translateY(-50%) scale(1.1);
    }

    .send-btn-integrated:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: translateY(-50%);
    }

    .document-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        background: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 12px 12px 0 0;
        font-size: 14px;
    }

    .document-info-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .document-icon {
        color: #64748b;
        font-size: 18px;
    }

    .document-name {
        color: #475569;
        font-weight: 500;
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .document-details {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .document-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 12px;
        color: #475569;
    }

    .document-stat-pill {
        background: #e2e8f0;
        color: #0f172a;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 500;
    }

    .profile-pic-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .typing-message .typing-indicator {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        min-height: 20px;
    }

    .typing-dots {
        display: flex;
        gap: 4px;
    }

    .typing-dots span {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #94a3b8;
        animation: typing 1.4s infinite ease-in-out;
    }

    .typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%,
        60%,
        100% {
            transform: translateY(0);
            opacity: 0.4;
        }
        30% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }

    /* Copy Button Styles */
    .message-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
    }

    .copy-btn {
        background: rgba(100, 116, 139, 0.1);
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        cursor: pointer;
        color: #64748b;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s ease;
    }

    .copy-btn:hover {
        background: rgba(100, 116, 139, 0.2);
        color: #475569;
    }

    .copy-btn.copied {
        background: rgba(34, 197, 94, 0.15);
        color: #16a34a;
    }

    .message-text {
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    /* Send Button States */
    .send-btn-integrated.sending {
        opacity: 0.7;
        cursor: not-allowed;
        pointer-events: none;
    }

    .send-btn-integrated.sending i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Input Focus Animation */
    .question-input.pulse-attention {
        animation: pulseInput 1.5s ease-in-out 3;
        border-color: #f47a1f !important;
    }

    @keyframes pulseInput {
        0% {
            box-shadow: 0 0 0 0 rgba(244, 122, 31, 0.4);
            border-color: #f47a1f;
        }
        50% {
            box-shadow: 0 0 0 8px rgba(244, 122, 31, 0);
            border-color: #fbb040;
        }
        100% {
            box-shadow: 0 0 0 0 rgba(244, 122, 31, 0);
            border-color: #f47a1f;
        }
    }

    /* Welcome Message Styles */
    .welcome-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 40px 20px;
        text-align: center;
    }

    .welcome-title {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .welcome-subtitle {
        font-size: 15px;
        color: #64748b;
        margin-bottom: 30px;
        max-width: 400px;
    }

    .welcome-examples-title {
        font-size: 13px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .welcome-examples {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
        max-width: 450px;
    }

    .example-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: left;
        font-size: 14px;
        color: #334155;
    }

    .example-btn:hover {
        border-color: #f47a1f;
        background: rgba(244, 122, 31, 0.03);
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(244, 122, 31, 0.1);
    }

    .example-btn i {
        font-size: 18px;
        color: #f47a1f;
        width: 24px;
        text-align: center;
    }

    .example-btn span {
        flex: 1;
    }

    .example-btn .arrow {
        color: #cbd5e1;
        font-size: 12px;
        transition: all 0.2s ease;
    }

    .example-btn:hover .arrow {
        color: #f47a1f;
        transform: translateX(3px);
    }

    /* ============================================
       RESPONSIVE STYLES - MOBILE & TABLET
       ============================================ */

    @media (max-width: 992px) {
        .chat-container {
            max-width: 100%;
            margin: 0;
            border-radius: 0;
            height: calc(100vh - 60px);
        }

        .message-wrapper {
            max-width: 80%;
        }

        .welcome-examples {
            max-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .chat-container {
            height: calc(100vh - 56px);
            border-radius: 0;
            box-shadow: none;
        }

        .messages-container {
            padding: 15px;
        }

        .message {
            gap: 8px;
            margin-bottom: 18px;
        }

        .message-wrapper {
            max-width: 85%;
        }

        .profile-pic {
            width: 36px;
            height: 36px;
            font-size: 12px;
        }

        .message-content {
            padding: 12px 14px;
            font-size: 14px;
        }

        .input-section {
            padding: 12px 15px;
        }

        .document-info {
            padding: 10px 15px;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .document-info-content {
            width: 100%;
        }

        .document-name {
            max-width: 200px;
            font-size: 13px;
        }

        .question-input {
            min-height: 50px;
            padding: 14px 55px 14px 15px;
            font-size: 15px;
        }

        .send-btn-integrated {
            right: 8px;
            min-width: 38px;
            height: 38px;
            padding: 8px 10px;
        }

        .welcome-container {
            padding: 25px 15px;
        }

        .welcome-title {
            font-size: 20px;
        }

        .welcome-subtitle {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .welcome-examples-title {
            font-size: 12px;
        }

        .welcome-examples {
            gap: 8px;
        }

        .example-btn {
            padding: 12px 14px;
            font-size: 13px;
            gap: 10px;
        }

        .example-btn i {
            font-size: 16px;
        }

        .copy-btn {
            padding: 5px 10px;
            font-size: 11px;
        }

        .typing-dots span {
            width: 6px;
            height: 6px;
        }
    }

    @media (max-width: 480px) {
        .chat-container {
            height: 100vh;
            height: 100dvh;
        }

        .messages-container {
            padding: 12px 10px;
        }

        .message {
            gap: 6px;
            margin-bottom: 15px;
        }

        .message-wrapper {
            max-width: 88%;
        }

        .profile-pic {
            width: 32px;
            height: 32px;
            font-size: 11px;
        }

        .message-content {
            padding: 10px 12px;
            font-size: 13px;
            border-radius: 14px;
        }

        .message.user .message-content {
            border-bottom-right-radius: 4px;
        }

        .message.assistant .message-content {
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 10px;
        }

        .input-section {
            padding: 10px 12px;
            padding-bottom: max(10px, env(safe-area-inset-bottom));
        }

        .document-info {
            padding: 8px 12px;
            border-radius: 10px 10px 0 0;
        }

        .document-name {
            max-width: 150px;
            font-size: 12px;
        }

        .question-input {
            min-height: 46px;
            padding: 12px 50px 12px 12px;
            font-size: 14px;
            border-radius: 0 0 10px 10px;
        }

        .send-btn-integrated {
            right: 6px;
            min-width: 36px;
            height: 36px;
            padding: 8px;
            font-size: 13px;
        }

        .welcome-container {
            padding: 20px 12px;
        }

        .welcome-title {
            font-size: 18px;
        }

        .welcome-subtitle {
            font-size: 13px;
            margin-bottom: 18px;
            padding: 0 5px;
        }

        .welcome-examples-title {
            font-size: 11px;
            margin-bottom: 12px;
        }

        .welcome-examples {
            gap: 6px;
        }

        .example-btn {
            padding: 10px 12px;
            font-size: 12px;
            border-radius: 10px;
        }

        .example-btn i {
            font-size: 14px;
            width: 20px;
        }

        .example-btn .arrow {
            display: none;
        }

        .quiz-question {
            font-size: 13px;
            margin: 15px 0 8px 0;
        }

        .quiz-response {
            font-size: 13px;
            margin: 12px 0 20px 15px;
        }

        .message-actions {
            margin-top: 8px;
            padding-top: 8px;
        }

        .copy-btn {
            padding: 4px 8px;
            font-size: 10px;
        }

        .typing-indicator {
            padding: 10px 14px !important;
        }

        .typing-dots span {
            width: 5px;
            height: 5px;
        }

        .document-stats {
            gap: 6px;
        }

        .document-stat-pill {
            padding: 3px 8px;
            font-size: 10px;
        }
    }

    @media (max-width: 360px) {
        .welcome-title {
            font-size: 16px;
        }

        .welcome-subtitle {
            font-size: 12px;
        }

        .example-btn {
            padding: 9px 10px;
            font-size: 11px;
        }

        .document-name {
            max-width: 120px;
        }

        .message-wrapper {
            max-width: 90%;
        }

        .message-content {
            font-size: 12px;
        }
    }

    @media (max-height: 500px) and (orientation: landscape) {
        .chat-container {
            height: 100vh;
        }

        .welcome-container {
            padding: 15px 10px;
        }

        .welcome-examples {
            flex-direction: row;
            flex-wrap: wrap;
        }

        .example-btn {
            flex: 1 1 calc(50% - 4px);
            min-width: 150px;
        }
    }

    @supports (padding: max(0px)) {
        .input-section {
            padding-left: max(15px, env(safe-area-inset-left));
            padding-right: max(15px, env(safe-area-inset-right));
            padding-bottom: max(12px, env(safe-area-inset-bottom));
        }

        .chat-container {
            padding-top: env(safe-area-inset-top);
        }
    }

    @media (hover: none) and (pointer: coarse) {
        .send-btn-integrated:hover:not(:disabled) {
            transform: translateY(-50%);
        }

        .example-btn:hover {
            transform: none;
            border-color: #e2e8f0;
            background: white;
        }

        .example-btn:active {
            border-color: #f47a1f;
            background: rgba(244, 122, 31, 0.05);
        }

        .copy-btn:active {
            background: rgba(100, 116, 139, 0.2);
        }
    }
</style>

<div class="chat-container">
    <!-- Chat Section - Affichée directement car le document est déjà chargé -->
    <div id="query-section" class="chat-section">
        <div class="messages-container" id="messagesContainer">
            <!-- Welcome Message -->
            <div class="welcome-container" id="welcomeMessage">
                <h2 class="welcome-title">👋 <?php echo get_phrase("Welcome_to_Wayo_AI"); ?></h2>
                <p class="welcome-subtitle"><?php echo get_phrase("Your_document_has_been_loaded._Ask_me_anything_about_its_content!"); ?></p>
                
                <div class="welcome-examples-title">💡 <?php echo get_phrase("Try_asking"); ?></div>
                <div class="welcome-examples">
                    <button type="button" class="example-btn" data-question="<?php echo get_phrase("Summarize_this_document_in_key_points"); ?>">
                        <i class="fas fa-list-ul"></i>
                        <span><?php echo get_phrase("Summarize_this_document_in_key_points"); ?></span>
                        <i class="fas fa-chevron-right arrow"></i>
                    </button>
                    <button type="button" class="example-btn" data-question="<?php echo get_phrase("What_are_the_main_topics_covered?"); ?>">
                        <i class="fas fa-book-open"></i>
                        <span><?php echo get_phrase("What_are_the_main_topics_covered?"); ?></span>
                        <i class="fas fa-chevron-right arrow"></i>
                    </button>
                    <button type="button" class="example-btn" data-question="<?php echo get_phrase("Create_a_10_question_quiz_on_this_content"); ?>">
                        <i class="fas fa-question-circle"></i>
                        <span><?php echo get_phrase("Create_a_10_question_quiz_on_this_content"); ?></span>
                        <i class="fas fa-chevron-right arrow"></i>
                    </button>
                    <button type="button" class="example-btn" data-question="<?php echo get_phrase("Explain_this_document_simply"); ?>">
                        <i class="fas fa-lightbulb"></i>
                        <span><?php echo get_phrase("Explain_this_document_simply"); ?></span>
                        <i class="fas fa-chevron-right arrow"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="input-section">
            <div id="documentInfo" class="document-info">
                <div class="document-details">
                    <div class="document-info-content">
                        <div class="document-icon">
                            <?php 
                            $file_ext = isset($file_type) ? $file_type : strtolower(pathinfo($syllabus['file'], PATHINFO_EXTENSION));
                            switch($file_ext) {
                                case 'pdf':
                                    echo '<i class="fas fa-file-pdf" style="color: #ef4444;"></i>';
                                    break;
                                case 'docx':
                                case 'doc':
                                    echo '<i class="fas fa-file-word" style="color: #2563eb;"></i>';
                                    break;
                                case 'txt':
                                    echo '<i class="fas fa-file-alt" style="color: #64748b;"></i>';
                                    break;
                                default:
                                    echo '<i class="fas fa-file"></i>';
                            }
                            ?>
                        </div>
                        <div class="document-name"><?php echo $syllabus['title']; ?></div>
                    </div>
                    <div class="document-stats">
                        <span class="document-stat-pill"><?php echo $page_count; ?> <?php echo get_phrase("pages"); ?></span>
                        <span class="document-stat-pill"><?php echo number_format($text_length); ?> <?php echo get_phrase("characters"); ?></span>
                    </div>
                </div>
            </div>
            <div class="input-wrapper">
                <input id="question" class="question-input" placeholder="<?php echo get_phrase("Ask_your_question_about_the_document's_content..."); ?>">
                <button type="button" class="send-btn-integrated" id="submitQuestion"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/backend/js/sweetalert.js"></script>
<script>
    let csrfToken = {
        name: '<?php echo $this->security->get_csrf_token_name(); ?>',
        hash: '<?php echo $this->security->get_csrf_hash(); ?>'
    };

    $(document).ready(function() {
        const numberFormatter = new Intl.NumberFormat();

        const refreshCsrfToken = (csrf) => {
            if (csrf && csrf.csrfName && csrf.csrfHash) {
                csrfToken = {
                    name: csrf.csrfName,
                    hash: csrf.csrfHash
                };
            }
        };

        const withCsrfData = (payload = {}) => {
            payload[csrfToken.name] = csrfToken.hash;
            return payload;
        };

        // Focus sur le champ de saisie au chargement
        const $questionInput = $('#question');
        $questionInput.addClass('pulse-attention');
        $questionInput.focus();
        
        $questionInput.one('input focus', function() {
            $(this).removeClass('pulse-attention');
        });
        setTimeout(() => {
            $questionInput.removeClass('pulse-attention');
        }, 4500);

        // Handler pour les boutons d'exemples
        $(document).on('click', '.example-btn', function() {
            const question = $(this).data('question');
            $('#question').val(question);
            $('#submitQuestion').click();
        });

        $('#submitQuestion').on('click', function() {
            const question = $('#question').val().trim();
            if (!question) return;

            // Cacher le message d'accueil
            $('#welcomeMessage').fadeOut(300);

            const $sendBtn = $(this);
            const $questionInput = $('#question');
            
            // Désactiver le bouton et l'input pendant l'envoi
            $sendBtn.addClass('sending').prop('disabled', true);
            $sendBtn.html('<i class="fas fa-spinner"></i>');
            $questionInput.prop('disabled', true);

            addMessage(question, 'user');
            $questionInput.val('');
            addMessage('', 'assistant', true);

            $.ajax({
                url: '<?php echo site_url("student/query_document"); ?>',
                type: 'POST',
                data: withCsrfData({
                    question: question
                }),
                dataType: 'json',
                success: function(response) {
                    $('.typing-message').remove();
                    refreshCsrfToken(response.csrf);
                    if (response.status === 'success') {
                        addMessage(response.answer, 'assistant');
                    } else {
                        addMessage('Error: ' + response.message, 'assistant');
                    }
                },
                error: function(xhr) {
                    $('.typing-message').remove();
                    if (xhr.responseJSON && xhr.responseJSON.csrf) {
                        refreshCsrfToken(xhr.responseJSON.csrf);
                    }
                    addMessage('<?php echo addslashes(get_phrase("Error_sending_request")); ?>', 'assistant');
                },
                complete: function() {
                    // Réactiver le bouton et l'input après la réponse
                    $sendBtn.removeClass('sending').prop('disabled', false);
                    $sendBtn.html('<i class="fas fa-paper-plane"></i>');
                    $questionInput.prop('disabled', false).focus();
                }
            });
        });

        function formatTime() {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            return hours + ':' + minutes;
        }

        function formatQuizResponse(content) {
            if (content.includes('<QUESTION>')) {
                return content;
            }

            var formattedContent = content.replace(/(\d+)\s+([^0-9].*?\?)(?=\s*\d+\s+|$)/g, function(match, num, question) {
                return '<div class="quiz-question">Question ' + num + ':</div>\n' + question.trim();
            });

            formattedContent = formattedContent.replace(/(\d+)\s+([^\d].*?)(?=\s*\d+\s+|$)/g, function(match, num, response) {
                return '<div class="quiz-response">Réponse ' + num + ':</div>\n' + response.trim();
            });

            formattedContent = formattedContent.replace(/(Question \d+:.+?)(?=Réponse \d+:|$)/g, function(match) {
                return match + '\n\n';
            });

            return formattedContent;
        }

        function addMessage(content, sender, isTyping = false) {
            const time = formatTime();

            if (sender === 'assistant' && isTyping) {
                var messageHtml = `
                    <div class="message assistant typing-message">
                        <div class="profile-pic assistant">
                            <img src="<?php echo base_url('uploads/system/logo/logo_wayo.png'); ?>" alt="Assistant" class="profile-pic-image">
                        </div>
                        <div class="message-wrapper">
                            <div class="message-header">
                                <span class="message-time">${time}</span>
                            </div>
                            <div class="message-content typing-indicator">
                                <div class="typing-dots">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#messagesContainer').append(messageHtml);
                $('#messagesContainer').scrollTop($('#messagesContainer')[0].scrollHeight);
                return;
            }

            var displayContent = content;
            if (sender === 'assistant' && !isTyping) {
                displayContent = formatQuizResponse(content);
            }

            var profileImage = sender === 'user' ?
                '<img src="<?php echo $this->user_model->get_user_image($this->session->userdata('user_id')); ?>" alt="User" class="profile-pic-image">' :
                '<img src="<?php echo base_url('uploads/system/logo/logo_wayo.png'); ?>" alt="Assistant" class="profile-pic-image">';

            var copyButton = sender === 'assistant' ? 
                `<div class="message-actions">
                    <button type="button" class="copy-btn" onclick="copyMessage(this)" data-content="${content.replace(/"/g, '&quot;').replace(/'/g, '&#39;')}">
                        <i class="fas fa-copy"></i> <?php echo get_phrase("Copy"); ?>
                    </button>
                </div>` : '';

            var messageHtml = `
                <div class="message ${sender}">
                    <div class="profile-pic ${sender}">
                        ${profileImage}
                    </div>
                    <div class="message-wrapper">
                        <div class="message-header">
                            <span class="message-time">${time}</span>
                        </div>
                        <div class="message-content">
                            <div class="message-text">${displayContent}</div>
                            ${copyButton}
                        </div>
                    </div>
                </div>
            `;

            $('#messagesContainer').append(messageHtml);
            $('#messagesContainer').scrollTop($('#messagesContainer')[0].scrollHeight);
        }

        // Fonction pour copier le message
        window.copyMessage = function(btn) {
            const content = $(btn).data('content');
            navigator.clipboard.writeText(content).then(function() {
                const $btn = $(btn);
                const originalHtml = $btn.html();
                $btn.addClass('copied');
                $btn.html('<i class="fas fa-check"></i> <?php echo get_phrase("Copied"); ?>');
                setTimeout(function() {
                    $btn.removeClass('copied');
                    $btn.html(originalHtml);
                }, 2000);
            }).catch(function() {
                const textArea = document.createElement('textarea');
                textArea.value = content;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                const $btn = $(btn);
                const originalHtml = $btn.html();
                $btn.addClass('copied');
                $btn.html('<i class="fas fa-check"></i> <?php echo get_phrase("Copied"); ?>');
                setTimeout(function() {
                    $btn.removeClass('copied');
                    $btn.html(originalHtml);
                }, 2000);
            });
        }

        // Envoyer avec Entrée
        $('#question').on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#submitQuestion').click();
            }
        });
    });
</script>
