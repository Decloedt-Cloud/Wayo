<style>
    /* Import Google Font for a modern look */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap');

    :root {
        --primary-color: #6D73F3; /* Custom Purple/Blue */
        --primary-gradient: linear-gradient(135deg, #6D73F3 0%, #5b62d6 100%);
        --bg-color: #f3f4f6;
        --sidebar-width: 320px;
        --chat-bg: #ffffff;
        --message-out-bg: #6D73F3;
        --message-in-bg: #f3f4f6;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --border-color: #e5e7eb;
    }

    /* Reset & Base */
    .chat-wrapper * {
        font-family: 'Inter', sans-serif;
        box-sizing: border-box;
    }

    /* --- PAGE HEADER MODERN --- */
    .page-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.875rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .title-icon-box {
        width: 48px;
        height: 48px;
        background-color: #6D73F3;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        box-shadow: 0 4px 10px rgba(109, 115, 243, 0.3);
    }
    
    .chat-page-header {
        margin-left: 20px;
    }

    .chat-wrapper {
        display: flex;
        height: calc(100vh - 200px); /* Hauteur dynamique pour laisser de l'espace au footer */
        min-height: 500px;
        background-color: #ffffff;
        border-radius: 20px;/* Radius plus standard comme les autres cards */
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04); /* Shadow standard du thème */
        border: 1px solid #e2e8f0;
        margin-top: 0px; /* Ajout d'espace au-dessus comme demandé */
        margin-bottom: 30px; /* Espace en bas */
        margin-left: 20px; /* Espace à gauche pour sidebar */
        
    }

    /* --- SIDEBAR --- */
    .chat-sidebar {
        width: 30%;
        min-width: 280px;
        background-color: #fff;
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        z-index: 2;
    }

    .chat-sidebar-header {
        padding: 24px 20px 10px;
        background: #fff;
    }

    .chat-sidebar-header h5 {
        font-family: 'Outfit', sans-serif;
    font-size: 1.0625rem;
    font-weight: 600;
        color: #1e293b;
        margin: 0 0 12px 0;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sidebar-separator {
        height: 1px;
        background-color: #e5e7eb;
        margin-bottom: 16px;
    }

    .chat-search-box {
        position: relative;
    }

    .chat-search-box input {
        width: 100%;
        padding: 12px 16px 12px 40px;
        border-radius: 12px;
        border: 1px solid transparent;
        background-color: #f3f4f6;
        font-size: 14px;
        transition: all 0.2s ease;
        color: var(--text-primary);
    }

    .chat-search-box input:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(109, 115, 243, 0.1); /* #6D73F3 with opacity */
        outline: none;
    }

    .chat-search-box::before {
        content: "\F0349"; /* MDI Magnify */
        font-family: "Material Design Icons";
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        font-size: 18px;
        pointer-events: none;
    }

    /* --- SEARCH RESULTS --- */
    #search-results {
        max-height: 350px;
        overflow-y: auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
        margin-top: 8px;
        /* Custom Scrollbar */
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    #search-results::-webkit-scrollbar {
        width: 6px;
    }

    #search-results::-webkit-scrollbar-track {
        background: transparent;
    }

    #search-results::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }

    .search-result-card {
        padding: 12px 16px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        animation: fadeIn 0.3s ease-out;
    }

    .search-result-card:last-child {
        border-bottom: none;
    }

    .search-result-card:hover {
        background-color: #f9fafb;
    }

    .search-result-avatar-wrapper {
        position: relative;
        margin-right: 12px;
    }

    .search-result-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .search-result-info {
        flex: 1;
    }

    .search-result-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .search-result-meta {
        font-size: 12px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.online {
        background-color: #d1fae5;
        color: #059669;
    }

    .status-badge.offline {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    /* --- PRESENCE INDICATOR --- */
    .status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #ffffff;
        z-index: 5;
        box-shadow: 0 0 0 1px rgba(0,0,0,0.05);
    }
    
    .status-online, .user-status.status-online {
        background-color: #10B981 !important;
    }
    
    .status-offline, .user-status.status-offline {
        background-color: #6B7280 !important;
    }
    
    .chat-user-item .user-avatar {
        position: relative; /* Ensure parent is relative for absolute positioning of indicator */
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* --- CHAT LIST --- */
    .chat-list-container {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    .chat-user-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f0f2f5;
        transition: all 0.2s;
        position: relative;
    }

    .chat-user-item:hover {
        background-color: #f5f6f6;
    }

    .chat-user-item.active {
        background-color: #e3f2fd;
        border-left: 3px solid var(--primary-color);
    }

    .chat-user-item .user-avatar {
        margin-right: 15px;
        flex-shrink: 0;
        width: 48px;
        height: 48px;
    }

    .chat-user-item .user-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .chat-user-item .user-info {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .chat-user-item .user-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 15px;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-user-item .user-last-msg {
        font-size: 13px;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }

    .chat-user-item .user-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        margin-left: 10px;
        min-width: 60px;
    }

    .chat-user-item .user-meta span:first-child {
        font-size: 11px;
        color: #9ca3af;
        margin-bottom: 6px;
    }

    .chat-user-item .badge {
        background-color: var(--primary-color);
        color: white;
        font-size: 10px;
        padding: 0 6px;
        border-radius: 10px;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    
    /* --- CHAT MAIN --- */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: var(--chat-bg);
        position: relative;
    }

    .empty-chat-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        color: var(--text-secondary);
        padding: 20px;
    }

    .empty-chat-icon {
        width: 100px;
        height: 100px;
        background: #6D73F3; /* #6D73F3 */
        box-shadow: 0 4px 12px rgba(109, 115, 243, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
  

    /* --- CHAT HEADER --- */
    .chat-header {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff;
    }

    .user-info-header {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        position: relative;
    }
    
    .user-avatar img {
        border-radius: 50%;
        object-fit: cover;
    }

    .user-status {
        position: absolute;
        bottom: 0;
        right: 0;
        border-radius: 50%;
        /* background-color: #10b981; REMOVED DEFAULT COLOR */
        border: 2px solid #fff;
    }

    .header-details h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .header-details p {
        margin: 0;
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* --- CHAT BODY --- */
    .chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        background-color: #c7d8f969; /* Modern neutral background */
        display: flex;
        flex-direction: column;
        gap: 12px; /* Spacing between messages */
    }

    /* Container for the message item */
    .message-item {
        display: flex;
        flex-direction: column;
        max-width: 75%; /* Slightly wider for better readability */
        animation: fadeIn 0.3s ease;
        margin-bottom: 4px;
        position: relative; /* Needed for absolute positioning of context menu */
    }

    /* Incoming Messages (Left) - Received */
    .message-item.incoming {
        align-self: flex-start;
    }

    .message-item.incoming .message-content {
        background-color: #ffffff;
        color: #1f2937; /* Dark Gray */
        border: 1px solid #e5e7eb; /* Light border */
        border-radius: 18px 18px 18px 4px; /* Sharp bottom-left */
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    /* Outgoing Messages (Right) - Sent */
    .message-item.outgoing {
        align-self: flex-end;
        align-items: flex-end;
    }

    .message-item.outgoing .message-content {
        background-color: var(--message-out-bg); /* #6D73F3 */
        color: white;
        border: 1px solid var(--message-out-bg);
        border-radius: 18px 18px 4px 18px; /* Sharp bottom-right */
        box-shadow: 0 4px 6px -1px rgba(109, 115, 243, 0.2), 0 2px 4px -1px rgba(109, 115, 243, 0.1); /* Purple-ish shadow */
        position: relative;
    }

    .message-content {
        padding: 10px 28px 10px 14px; /* Added right padding for 3-dots icon */
        font-size: 14.5px;
        line-height: 1.5;
        position: relative;
        word-wrap: break-word;
        min-width: 60px;
    }

    .message-meta {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 4px;
        font-size: 11px;
    }

    .message-item.incoming .message-meta {
        color: #6b7280; /* Gray for incoming */
    }
    
    .message-item.outgoing .message-meta {
        color: rgba(255, 255, 255, 0.9); /* White-ish for outgoing */
    }

    .message-meta span {
        margin-right: 4px;
    }

    .message-meta i {
        font-size: 14px;
    }
    
    .message-item.outgoing .message-content a {
        color: #e0f2fe;
        text-decoration: underline;
    }

    .message-item.incoming .message-content a {
        color: #2563eb;
        text-decoration: underline;
    }

    /* Removed triangle tails to rely on border-radius shape as requested */
    .message-item.incoming .message-content::before,
    .message-item.outgoing .message-content::before {
        display: none;
    }

    /* --- AUDIO MESSAGE STYLING --- */
    .message-item.audio-message .message-content {
        padding: 10px 14px;
        min-width: 240px;
    }

    .custom-audio-player {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        margin-bottom: 4px; /* Space for meta */
    }

    .audio-control-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    
    .audio-control-btn i {
        font-size: 20px;
    }

    .audio-track {
        flex: 1;
        height: 4px;
        border-radius: 2px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .audio-progress {
        height: 100%;
        width: 0%;
        border-radius: 2px;
        transition: width 0.1s linear;
    }

    .audio-time {
        font-size: 11px;
        font-variant-numeric: tabular-nums;
        font-weight: 500;
        min-width: 35px;
        text-align: right;
    }

    /* Incoming Audio Styling */
    .message-item.incoming .audio-control-btn {
        background: #f3f4f6;
        color: #4b5563;
    }
    .message-item.incoming .audio-control-btn:hover {
        background: #e5e7eb;
    }
    .message-item.incoming .audio-track {
        background: #e5e7eb;
    }
    .message-item.incoming .audio-progress {
        background: #4b5563;
    }
    .message-item.incoming .audio-time {
        color: #6b7280;
    }

    /* Outgoing Audio Styling */
    .message-item.outgoing .audio-control-btn {
        background: rgba(255,255,255,0.2);
        color: #fff;
    }
    .message-item.outgoing .audio-control-btn:hover {
        background: rgba(255,255,255,0.3);
    }
    .message-item.outgoing .audio-track {
        background: rgba(255,255,255,0.3);
    }
    .message-item.outgoing .audio-progress {
        background: #ffffff;
    }
    .message-item.outgoing .audio-time {
        color: rgba(255,255,255,0.9);
    }

    /* --- CHAT FOOTER --- */
    .chat-footer {
        padding: 16px 24px;
        background: #fff;
        border-top: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .chat-input-group {
        display: flex;
        align-items: center;
        background-color: #f3f4f6;
        border-radius: 24px;
        padding: 6px 8px;
        transition: box-shadow 0.2s;
        border: 1px solid transparent;
        width: 100%;
    }

    .chat-input-group:focus-within {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(109, 115, 243, 0.1); /* #6D73F3 */
    }

    .chat-input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 10px 12px;
        font-size: 14px;
        color: var(--text-primary);
        outline: none;
    }

    .chat-input::placeholder {
        color: #9ca3af;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: transparent;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 20px;
    }

    .btn-icon:hover {
        background-color: #e5e7eb;
        color: var(--text-primary);
    }

    .btn-send {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin-left: 8px;
        box-shadow: 0 2px 5px rgba(109, 115, 243, 0.3); /* #6D73F3 */
        transition: transform 0.1s;
    }

    .btn-send:hover {
        transform: scale(1.05);
    }

    .btn-send:active {
        transform: scale(0.95);
    }
    
    .btn-send i {
        font-size: 18px;
        margin-left: 2px; /* Visual centering adjustment for send icon */
    }
    
    .image-preview-container {
        padding: 10px;
        border-top: 1px solid var(--border-color);
    }
    
    .preview-wrapper {
        position: relative;
        display: inline-block;
    }

    .btn-remove-preview {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 24px;
        height: 24px;
        background-color: #ef4444;
        color: white;
        border: 2px solid #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        z-index: 10;
        transition: transform 0.2s, background-color 0.2s;
    }

    .btn-remove-preview:hover {
        background-color: #dc2626;
        transform: scale(1.1);
    }

    .btn-remove-preview i {
        font-size: 14px;
        line-height: 1;
    }
    
    .image-preview-container .preview-wrapper img {
        max-height: 100px;
        border-radius: 8px;
    }

    /* --- MESSAGE ACTIONS (DESKTOP) --- */
    .message-actions {
        position: absolute;
        top: 6px; /* Discreet position inside bubble */
        right: 8px;
        opacity: 0;
        transition: opacity 0.2s;
        cursor: pointer;
        z-index: 10;
    }

    .message-item:hover .message-actions {
        opacity: 1;
    }

    /* --- CHAT IMAGES & LIGHTBOX --- */
    .chat-image-thumbnail {
        max-width: 450px; /* Increased from 300px */
        max-height: 400px; /* Increased from 200px */
        min-width: 150px;
        width: auto;
        height: auto;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s, filter 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        display: block; /* Avoid ghost spacing */
    }

    @media (max-width: 768px) {
        .chat-image-thumbnail {
            max-width: 100%;
            max-height: 300px;
        }
    }

    .chat-image-thumbnail:hover {
        transform: scale(1.02);
        filter: brightness(0.95);
    }

    /* Lightbox Styles */
    .lightbox-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 9999; /* Très haut pour passer au-dessus de tout */
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
    }

    .lightbox-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .lightbox-image {
        max-width: 95%;
        max-height: 90vh;
        border-radius: 4px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.5);
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .lightbox-overlay.active .lightbox-image {
        transform: scale(1);
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 35px;
        cursor: pointer;
        z-index: 10000;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transition: background 0.2s;
    }

    .lightbox-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .chat-image-thumbnail {
            max-width: 240px; /* Plus petit sur mobile */
            max-height: 180px;
        }
        
        .lightbox-close {
            top: 15px;
            right: 15px;
        }
    }

    /* Toujours visible sur mobile si le menu est actif, mais géré par long-press */
    
    .message-actions-btn {
        width: 20px; /* Smaller button */
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px; /* Smaller icon */
    }

    .message-item.outgoing .message-actions-btn {
        color: rgba(255, 255, 255, 0.8);
        background: transparent;
    }
    
    .message-item.incoming .message-actions-btn {
        color: #9ca3af;
        background: transparent;
    }

    /* Dropdown Menu (Desktop) */
    .message-context-menu {
        position: absolute;
        top: 0;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        min-width: 160px;
        z-index: 100;
        display: none;
        overflow: hidden;
        animation: fadeIn 0.15s ease-out;
        border: 1px solid #e5e7eb;
    }
    
    /* Outgoing context menu - Positioned to the LEFT */
    .message-item.outgoing .message-context-menu {
        right: 100%;
        margin-right: 12px;
        color: var(--text-primary);
        text-align: left;
    }

    /* Incoming context menu - Positioned to the RIGHT */
    .message-item.incoming .message-context-menu {
        left: 100%;
        margin-left: 12px;
    }

    .message-context-menu.show {
        display: block;
    }

    .context-menu-item {
        padding: 10px 16px;
        font-size: 14px;
        color: #374151;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: background 0.1s;
    }

    .context-menu-item:hover {
        background-color: #f3f4f6;
    }

    .context-menu-item i {
        margin-right: 12px;
        font-size: 18px;
        color: #6b7280;
        width: 20px;
        text-align: center;
    }

    .context-menu-item.delete {
        color: #ef4444;
    }
    .context-menu-item.delete i {
        color: #ef4444;
    }

    /* --- GRADIENT ICONS --- */
    .gradient-icon {
        background: linear-gradient(135deg, #6D73F3 0%, #5b62d6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 14px; /* Reduced size */
    }

    /* --- MOBILE CONTEXT MENU (Centered) --- */
    /* --- STATUS ICONS --- */
    .message-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px; /* Icon size */
        margin-left: 4px;
        vertical-align: middle;
    }

    /* Sent: Single Check (White 100%) */
    .status-sent i {
        color: rgba(255, 255, 255, 1);
    }

    /* Delivered: Double Check (Grey/White 70%) */
    .status-delivered i {
        color: rgba(255, 255, 255, 0.7);
    }

    /* Read: Double Check (Bright White 100%) - As requested, but often blue is used. 
       User said: "blanc 100% brillant" for Read.
       Wait, Sent is also "blanc 100%". 
       Let's check user request again: 
       "Envoyé (une seule check ✓) : (blanc 100%)"
       "Livré (deux checks gris ✓✓) : (blanc 70%)"
       "Lu (deux checks bleus ✓✓) : (blanc 100% brillant)" -> This is contradictory in prompt. 
       Prompt said: "Lu (deux checks bleus ✓✓)" AND "Afficher ✓✓ (blanc 100% brillant)".
       Given standard WhatsApp, Blue is distinctive. 
       However, on a Purple background (#6D73F3), Blue might clash or be invisible.
       Bright White is good. Maybe add a text-shadow or just pure white?
       Let's use a distinct color if possible, like a light Cyan or maintain White but with opacity distinction.
       Actually, if Sent is White 100%, and Read is White 100%, they look same (except 1 vs 2 ticks).
       Delivered is 2 ticks (70%).
       So:
       1 Tick White = Sent.
       2 Ticks Grey(70%) = Delivered.
       2 Ticks White(100%) = Read.
       This makes sense.
    */
    .status-read i {
        color: #ffffff; /* Blanc 100% */
        text-shadow: 0 0 2px rgba(255,255,255,0.5); /* Brillant effect */
    }

    .mobile-context-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent dark */
        z-index: 9999;
        justify-content: center;
        align-items: center; /* Centered as requested */
        backdrop-filter: blur(2px);
    }

    .mobile-context-overlay.active {
        display: flex;
        animation: fadeIn 0.2s ease-out;
    }

    .mobile-context-menu {
        background-color: #fff;
        width: 85%;
        max-width: 320px;
        border-radius: 12px;
        padding: 8px 0;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        transform: scale(0.95);
        opacity: 0;
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .mobile-context-overlay.active .mobile-context-menu {
        transform: scale(1);
        opacity: 1;
    }

    .mobile-menu-item {
        padding: 14px 24px;
        display: flex;
        align-items: center;
        font-size: 16px;
        color: var(--text-primary);
        cursor: pointer;
        transition: background-color 0.1s;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .mobile-menu-item:last-child {
        border-bottom: none;
    }

    .mobile-menu-item:active {
        background-color: #f3f4f6;
    }

    .mobile-menu-item i {
        margin-right: 16px;
        font-size: 22px;
        color: #6b7280;
    }

    .mobile-menu-item.delete {
        color: #ef4444;
    }

    .mobile-menu-item.delete i {
        color: #ef4444;
    }

    /* --- IMAGE ONLY MESSAGES --- */
    /* Remove bubble styling for image-only messages */
    .message-item.image-message .message-content {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        position: relative; /* For absolute positioning of meta */
    }

    /* Remove preview wrapper margin in this context */
    .message-item.image-message .preview-wrapper {
        margin-bottom: 0 !important;
    }

    /* Style the meta (time) to overlay on the image */
    .message-item.image-message .message-meta {
        position: absolute;
        bottom: 6px;
        right: 6px;
        background: rgba(0, 0, 0, 0.6);
        color: rgba(255, 255, 255, 0.9) !important;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        backdrop-filter: blur(2px);
        margin-top: 0;
        pointer-events: none; /* Let clicks pass through to image */
    }
    
    /* Ensure checkmark is white in meta */
    .message-item.image-message .message-meta i {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    /* Adjust context menu trigger position/style */
    .message-item.image-message .message-actions {
        top: 6px;
        right: 6px;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
    }

    .message-item.image-message .message-actions-btn {
        color: white !important;
        width: 100%;
        height: 100%;
    }
    
    /* Hide tail/triangle if any remained (already hidden in main css but good to be safe) */
    .message-item.image-message .message-content::before {
        display: none !important;
    }

    /* Recording Pulse Animation */
    .recording-pulse {
        animation: pulse-recording 1.5s infinite;
    }

    @keyframes pulse-recording {
        0% {
            box-shadow: 0 0 0 0 rgba(109, 115, 243, 0.7); /* #6D73F3 */
        }
        70% {
            box-shadow: 0 0 0 10px rgba(109, 115, 243, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(109, 115, 243, 0);
        }
    }

    /* --- MODERN RECORDING UI --- */
    .recording-interface {
        display: flex;
        align-items: center;
        width: 100%;
        height: 100%;
        padding: 0 4px;
        animation: fadeIn 0.2s ease-out;
    }

    .btn-cancel-record {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(239, 68, 68, 0.1);
        transition: all 0.2s;
        margin-right: 12px;
        border: none;
        cursor: pointer;
    }

    .btn-cancel-record:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: scale(1.1);
    }

    .btn-cancel-edit {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        transition: all 0.2s;
        margin-left: 8px;
        border: none;
        cursor: pointer;
    }

    .btn-cancel-edit:hover {
        background: rgba(239, 68, 68, 0.2);
        transform: scale(1.05);
    }

    .recording-visualizer {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(109, 115, 243, 0.05); /* Very light theme color */
        border-radius: 20px;
        padding: 6px 16px;
        margin-right: 12px;
    }

    .recording-status {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .recording-dot-active {
        width: 10px;
        height: 10px;
        background-color: #ef4444; /* Red for active recording */
        border-radius: 50%;
        animation: pulse-recording-dot 1s infinite;
    }

    @keyframes pulse-recording-dot {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1); }
    }

    #recording-timer-modern {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-variant-numeric: tabular-nums;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .audio-wave {
        display: flex;
        align-items: center;
        gap: 3px;
        height: 20px;
    }

    .audio-wave span {
        display: block;
        width: 3px;
        background-color: #6D73F3;
        border-radius: 2px;
        animation: wave-animation 1s infinite ease-in-out;
    }

    .audio-wave span:nth-child(1) { height: 6px; animation-delay: 0s; }
    .audio-wave span:nth-child(2) { height: 12px; animation-delay: 0.1s; }
    .audio-wave span:nth-child(3) { height: 18px; animation-delay: 0.2s; }
    .audio-wave span:nth-child(4) { height: 10px; animation-delay: 0.3s; }
    .audio-wave span:nth-child(5) { height: 8px; animation-delay: 0.4s; }

    @keyframes wave-animation {
        0%, 100% { transform: scaleY(1); }
        50% { transform: scaleY(2); }
    }

    .btn-send-record {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #6D73F3 0%, #5860d6 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(109, 115, 243, 0.4);
        transition: transform 0.2s;
    }

    .btn-send-record:hover {
        transform: scale(1.05);
    }
    
    .btn-send-record:active {
        transform: scale(0.95);
    }
    
    .btn-send-record i {
        font-size: 20px;
        margin-left: 2px;
    }
    
    /* Preview Player Styles */
    .preview-player {
        position: relative;
    }
    
    #preview-progress-bar {
        transition: width 0.1s linear;
    }
    
    .recording-state, .recording-preview-state {
        animation: fadeIn 0.2s ease-out;
    }

    /* --- SCROLL TO BOTTOM BUTTON --- */
    .scroll-to-bottom-btn {
        position: absolute;
        bottom: 98px;
        right: 27px;
        width: 42px;
        height: 42px;
        background-color: #6D73F3;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(109, 115, 243, 0.4);
        cursor: pointer;
        z-index: 50;
        transition: opacity 0.3s ease, transform 0.2s ease, visibility 0.3s;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .scroll-to-bottom-btn.visible {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        animation: fadeInButton 0.3s ease-out;
    }

    .scroll-to-bottom-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(109, 115, 243, 0.5);
    }

    .scroll-to-bottom-btn:active {
        transform: scale(0.95) translateY(0);
    }

    .scroll-to-bottom-btn i {
        font-size: 24px;
        line-height: 1;
    }

    @keyframes fadeInButton {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* --- TYPING INDICATORS --- */
    /* 1. Header Typing */
    #header-typing-indicator {
        transition: opacity 0.3s ease;
    }

    /* 2. Chat Body Bubble - Modern Fluid Wave */
    .typing-bubble {
        padding: 8px 14px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f3f4f6 0%, #ffffff 100%);
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        width: fit-content;
        border: 1px solid #f0f0f0;
    }

    .typing-dots {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 10px;
        min-width: 36px;
        gap: 4px;
    }

    .typing-dots span {
        display: block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #9ca3af;
        animation: fluid-wave 1.4s infinite ease-in-out both;
    }
    
    .typing-dots span:nth-child(1) { animation-delay: 0s; }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes fluid-wave {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.6;
        }
        30% {
            transform: translateY(-4px);
            opacity: 1;
            background-color: #6D73F3; /* Primary Color Accent at peak */
        }
    }

    /* 3. Sidebar Typing */
    .sidebar-typing-indicator {
        display: inline-flex;
        align-items: center;
        margin-left: 8px;
    }
    .sidebar-typing-indicator span {
        display: inline-block;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: #6b7280; /* Gray */
        margin: 0 1px;
        animation: sidebar-typing 1.2s infinite ease-in-out both;
    }
    .sidebar-typing-indicator span:nth-child(1) { animation-delay: 0s; }
    .sidebar-typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .sidebar-typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes sidebar-typing {
        0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
        50% { transform: scale(1); opacity: 1; }
    }

    /* Adjust Sidebar Layout */
    .chat-user-item .user-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden; /* Prevent spill */
    }
    .chat-user-item .user-name {
        display: flex;
        align-items: center;
    }
</style>
