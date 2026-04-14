<?php
// Set default working page if not set
if (!isset($working_page)) {
    $working_page = 'filter';
}
?>
<?php if ($working_page == 'filter'): ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
    
    <style>
    /* ============================================================================
       TEACHER MANAGER - MODERN DESIGN (MATCHING STUDENT STYLE)
       ============================================================================ */

    :root {
        --exp-primary: #6366f1;
        --exp-primary-light: #eef2ff;
        --exp-success: #059669;
        --exp-dark: #1e293b;
        --exp-gray: #64748b;
        --exp-light: #f8fafc;
        --exp-border: #e2e8f0;
        --exp-warning: #f59e0b;
        --exp-danger: #ef4444;
    }

    /* Header Card */
    .exp-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
    }

    .exp-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .exp-header-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .exp-header-text h4 {
        margin: 0;
        color: white;
        font-size: 1.35rem;
        font-weight: 700;
    }

    .exp-header-text p {
        margin: 0.25rem 0 0;
        color: rgba(255,255,255,0.7);
        font-size: 0.85rem;
    }

    .exp-header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .exp-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 1.75rem;
        border-radius: 16px;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 4px 12px rgba(0, 0, 0, 0.08),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
        text-transform: uppercase;
    }

    .exp-btn-primary {
        background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
    }

    .exp-btn-primary:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 
            0 12px 30px rgba(99, 102, 241, 0.6),
            0 4px 15px rgba(139, 92, 246, 0.4);
        background: linear-gradient(135deg, #8b5cf6, var(--exp-primary));
    }

    /* Content Card */
    .exp-content-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        border: 1px solid var(--exp-border);
        overflow: hidden;
    }

    .exp-content-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--exp-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .exp-content-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--exp-dark);
        font-size: 0.95rem;
    }

    .exp-content-title i {
        color: var(--exp-primary);
    }

    .exp-content-body {
        padding: 1.5rem;
    }

    /* Filter Section */
    .exp-filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 1px solid var(--exp-border);
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    }

    .exp-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .exp-filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .exp-filter-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--exp-gray);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .exp-filter-input {
        padding: 0.75rem 1rem;
        border: 1px solid var(--exp-border);
        border-radius: 10px;
        font-size: 0.9rem;
        color: var(--exp-dark);
        background: var(--exp-light);
        transition: all 0.2s;
        width: 100%;
    }

    .exp-filter-input:focus {
        outline: none;
        border-color: var(--exp-primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .exp-filter-btn {
        padding: 0.75rem 1.5rem;
        background: var(--exp-dark);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .exp-filter-btn:hover {
        background: #0f172a;
        transform: translateY(-2px);
    }

    /* Filter Chips */
    .exp-filter-chips {
        display: flex;
        gap: 0.5rem;
    }

    .exp-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        border: 2px solid var(--exp-border);
        border-radius: 100px;
        background: white;
        color: var(--exp-gray);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .exp-chip:hover {
        border-color: var(--exp-primary);
        color: var(--exp-primary);
    }

    .exp-chip.active {
        background: linear-gradient(135deg, var(--exp-primary), #8b5cf6);
        border-color: var(--exp-primary);
        color: white;
    }

    .exp-chip-count {
        padding: 0.125rem 0.5rem;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .exp-chip.active .exp-chip-count {
        background: rgba(255, 255, 255, 0.2);
    }
    
    /* Loading */
    .exp-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        color: var(--exp-gray);
        font-size: 1.1rem;
    }
    
    .exp-loading i {
        margin-right: 0.5rem;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin { 100% { transform: rotate(360deg); } }

    /* List Styles */
    .exp-list-header {
        display: grid;
        grid-template-columns: 1fr 2fr 2fr 1fr;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-bottom: 1px solid var(--exp-border);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        color: white;
        letter-spacing: 0.5px;
    }

    .exp-list-header div {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .exp-list-header i {
        opacity: 0.7;
    }

    .exp-list-item {
        display: grid;
        grid-template-columns: 1fr 2fr 2fr 1fr;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--exp-border);
        align-items: center;
        transition: background 0.2s;
        font-size: 0.9rem;
        color: var(--exp-dark);
    }

    .exp-list-item:hover {
        background: var(--exp-primary-light);
    }
    
    .exp-list-item:last-child {
        border-bottom: none;
    }

    </style>

    <!--title-->
    <div class="row">
        <div class="col-12">
            <input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />
            <div class="exp-header">
                <div class="exp-header-left">
                    <div class="exp-header-icon">
                        <i class="mdi mdi-account-tie"></i>
                    </div>
                    <div class="exp-header-text">
                        <h4><?php echo get_phrase('teachers'); ?></h4>
                        <p><?php echo get_phrase('manage_teacher_details'); ?></p>
                    </div>
                </div>
                <div class="exp-header-actions">
                    <button type="button" class="exp-btn exp-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/teacher/create'); ?>', '<?php echo get_phrase('create_teacher'); ?>')">
                        <i class="mdi mdi-plus"></i>
                        <span><?php echo get_phrase('add_new_teacher'); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="exp-content-card">
                <div class="exp-content-header">
                    <span class="exp-content-title">
                        <i class="mdi mdi-format-list-bulleted"></i>
                        <?php echo get_phrase('teacher_list'); ?>
                    </span>
                </div>
                <div class="exp-content-body teacher_content">
                    <?php include 'list.php'; ?>
                </div>
            </div>
        </div>
    </div>
<?php elseif ($working_page == 'create'): ?>
    <?php include 'create.php'; ?>
<?php elseif ($working_page == 'edit'): ?>
    <?php include 'update.php'; ?>
<?php endif; ?>

<script>
    // Ensure showNotification is globally available
    window.showNotification = function(type, message) {
        if (typeof ENABLE_TOASTS !== 'undefined' && !ENABLE_TOASTS) {
            return;
        }
        if(typeof toastr !== 'undefined') {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 5000,
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut',
            };
            if (type === 'success') {
                toastr.success(message);
            } else if (type === 'error') {
                toastr.error(message);
            } else if (type === 'warning') {
                toastr.warning(message);
            } else {
                toastr.info(message);
            }
        } else {
            console.log(type + ': ' + message);
        }
    };

    $('document').ready(function () {
        // Charger tous les enseignants au démarrage
        if(typeof window.showAllTeachers === 'function') {
            //window.showAllTeachers();
        }
    });

    window.showAllTeachers = function () {
        // Visual feedback
        $('.teacher_content').css('opacity', '0.5');

        // Capture current state before refresh
        var currentSearch = $('#exp-search').val();

        var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
        
        console.log('Fetching teacher list...');
        
        $.ajax({
            url: '<?php echo site_url('app/mentor/list') ?>',
            data: { [csrfName]: csrfHash },
            success: function (response) {
                console.log('Teacher list fetched successfully');
                $('.teacher_content').html(response).css('opacity', '1');
                
                // Restore state and trigger updates
                if (currentSearch) {
                    var searchInput = document.getElementById('exp-search');
                    if (searchInput) {
                        searchInput.value = currentSearch;
                        searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching teacher list:', error);
                $('.teacher_content').css('opacity', '1');
                if(typeof toastr !== 'undefined') {
                    toastr.error('Failed to update list');
                }
            }
        });
    }
</script>
