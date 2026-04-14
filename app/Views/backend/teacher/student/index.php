<?php if ($working_page == 'filter'): ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
    
    <style>
    /* ============================================================================
       STUDENT MANAGER - MODERN DESIGN (MATCHING EXPENSE CATEGORY STYLE)
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
        grid-template-columns: 1fr 1fr 2fr 1fr 1fr;
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
        grid-template-columns: 1fr 1fr 2fr 1fr 1fr;
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
                        <i class="mdi mdi-account-group"></i>
                    </div>
                    <div class="exp-header-text">
                        <h4><?php echo get_phrase('student'); ?></h4>
                        <p><?php echo get_phrase('manage_student_details'); ?></p>
                    </div>
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
                        <?php echo get_phrase('student_list'); ?>
                    </span>
                </div>
                <div class="exp-content-body student_content">
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
        // Keep server-rendered list on first paint to avoid flicker/hide caused by immediate AJAX refresh.
        if (!window.__csrfName) {
            window.__csrfName = '<?= csrf_token(); ?>';
        }
        if (!window.__csrfHash) {
            window.__csrfHash = $('#delete_form input[name="' + window.__csrfName + '"]').val() || $('input[name="' + window.__csrfName + '"]').last().val() || '';
        }
    });


    window.filter_student = function() {
        var class_id = $('#class_id').val();

        if (class_id != "") {
            window.showAllStudents(false);
        } else {
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
        }
    }

    window.showAllStudents = function (preserveStateOrResponse) {
        // confirmModal passes the previous AJAX response object to callbacks.
        // Keep preserveState opt-in, but also consume returned CSRF token when provided.
        var preserveState = (preserveStateOrResponse === true);
        var callbackResponse = (preserveStateOrResponse && typeof preserveStateOrResponse === 'object') ? preserveStateOrResponse : null;
        var class_id = $('#class_id').val();
        if (typeof class_id === 'undefined' || class_id === null || class_id === '') {
            class_id = 'all';
        }
        
        // Visual feedback
        $('.student_content').css('opacity', '0.5');

        // Capture current state before refresh only when explicitly requested.
        var currentSearch = preserveState ? ($('#exp-search').val() || '') : '';
        var currentFilter = preserveState ? ($('.exp-chip.active').data('filter') || 'all') : 'all';

        var csrfName = '<?= csrf_token(); ?>';
        // Always prioritize token from hidden delete form to avoid stale duplicates.
        if (window.__csrfName) {
            csrfName = window.__csrfName;
        }
        var csrfHash = window.__csrfHash || $('#delete_form input[name="' + csrfName + '"]').val();
        if (!csrfHash) {
            csrfHash = $('input[name="' + csrfName + '"]').last().val();
        }

        if (callbackResponse && callbackResponse.csrf) {
            var cbCsrfName = callbackResponse.csrf.csrfName || callbackResponse.csrf.name || csrfName;
            var cbCsrfHash = callbackResponse.csrf.csrfHash || callbackResponse.csrf.hash || '';
            if (cbCsrfHash) {
                csrfName = cbCsrfName;
                csrfHash = cbCsrfHash;
                window.__csrfName = cbCsrfName;
                window.__csrfHash = cbCsrfHash;
                var cbInputs = $('input[name="' + cbCsrfName + '"]');
                if (cbInputs.length) {
                    cbInputs.val(cbCsrfHash);
                }
                var cbDeleteInput = $('#delete_form input[name="' + cbCsrfName + '"]');
                if (cbDeleteInput.length) {
                    cbDeleteInput.val(cbCsrfHash);
                } else {
                    $('#delete_form').append('<input type="hidden" name="' + cbCsrfName + '" value="' + cbCsrfHash + '" />');
                }
            }
        }

        if (!csrfHash) {
            console.error('CSRF token missing before student list refresh');
            $('.student_content').css('opacity', '1');
            if (typeof toastr !== 'undefined') {
                toastr.error('Security token missing. Please refresh the page.');
            }
            return;
        }
        
        console.log('Fetching student list...');
        
        $.ajax({
            url: '<?php echo site_url('app/mentor/student/filter/') ?>' + (class_id === 'all' ? '' : class_id),
            type: 'POST',
            data: { [csrfName]: csrfHash },
            headers: { 'X-CSRF-TOKEN': csrfHash },
            dataType: 'json',
            success: function (response) {
                console.log('Student list fetched successfully');
                if (!response || typeof response.html === 'undefined') {
                    $('.student_content').css('opacity', '1');
                    return;
                }
                $('.student_content').html(response.html).css('opacity', '1');
                
                if (response.csrf) {
                    var newCsrfName = response.csrf.csrfName || response.csrf.name;
                    var newCsrfHash = response.csrf.csrfHash || response.csrf.hash;
                    if (newCsrfName && newCsrfHash) {
                        window.__csrfName = newCsrfName;
                        window.__csrfHash = newCsrfHash;
                        var csrfInputs = $('input[name="' + newCsrfName + '"]');
                        if (csrfInputs.length) {
                            csrfInputs.val(newCsrfHash);
                        } else {
                            $('.student_content').append('<input type="hidden" name="' + newCsrfName + '" value="' + newCsrfHash + '" />');
                        }
                        var deleteFormInput = $('#delete_form input[name="' + newCsrfName + '"]');
                        if (deleteFormInput.length) {
                            deleteFormInput.val(newCsrfHash);
                        } else {
                            $('#delete_form').append('<input type="hidden" name="' + newCsrfName + '" value="' + newCsrfHash + '" />');
                        }
                    }
                }
                
                // Restore state and trigger updates in the new list.php script
                if (preserveState && currentSearch) {
                    var searchInput = document.getElementById('exp-search');
                    if (searchInput) {
                        searchInput.value = currentSearch;
                        searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
                
                if (preserveState && currentFilter && currentFilter !== 'all') {
                    var chip = document.querySelector('.exp-chip[data-filter="' + currentFilter + '"]');
                    if (chip) {
                        chip.click(); // This will trigger the click handler in list.php
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching student list:', error);
                $('.student_content').css('opacity', '1');
                if(typeof toastr !== 'undefined') {
                    toastr.error('Failed to update list');
                }
            }
        });
    }
</script>