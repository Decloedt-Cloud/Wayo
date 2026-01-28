<style>
    :root {
        --idx-primary: #6366f1;
        --idx-primary-light: #eef2ff;
        --idx-success: #059669;
        --idx-dark: #1e293b;
        --idx-gray: #64748b;
        --idx-light: #f8fafc;
        --idx-border: #e2e8f0;
    }

    .idx-title-card {
        background: linear-gradient(135deg, var(--idx-primary) 0%, #4f46e5 100%);
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(99, 102, 241, 0.2);
        margin-bottom: 24px;
        padding: 24px;
    }

    .idx-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .idx-title-group h4 {
        color: white;
        margin: 0;
        font-size: 24px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .idx-title-icon {
        background: rgba(255, 255, 255, 0.2);
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .idx-btn-add {
        background: white;
        color: var(--idx-primary);
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .idx-btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px -1px rgba(0, 0, 0, 0.15);
        background: var(--idx-light);
    }

    .idx-content-card {
        background: white;
        border: 1px solid var(--idx-border);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .idx-card-body {
        padding: 24px;
    }

    /* Loading State */
    .idx-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        color: var(--idx-gray);
        gap: 16px;
    }

    .idx-loading i {
        font-size: 32px;
        color: var(--idx-primary);
    }
</style>

<!-- start page title -->
<div class="row">
    <div class="col-xl-12">
        <div class="idx-title-card">
            <div class="idx-header">
                <div class="idx-title-group">
                    <h4>
                        <div class="idx-title-icon">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <?php echo get_phrase('expense_category'); ?>
                    </h4>
                </div>
                <button type="button" class="idx-btn-add" onclick="rightModal('<?php echo site_url('modal/popup/expense_category/create'); ?>', '<?php echo get_phrase('add_expense_category'); ?>')">
                    <i class="fa-solid fa-plus"></i>
                    <?php echo get_phrase('add_expense_category'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-12">
        <div class="idx-content-card">
            <div class="idx-card-body">
                <div class="expense_category_content">
                    <?php include 'list.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var showAllExpenseCategories = function() {
        var url = '<?php echo route('expense_category/list'); ?>';
        
        // Show loading state
        $('.expense_category_content').html('<div class="idx-loading"><i class="fa-solid fa-spinner fa-spin"></i><p><?php echo get_phrase('loading_data'); ?>...</p></div>');

        $.ajax({
            type: 'GET',
            url: url,
            success: function(response) {
                $('.expense_category_content').html(response);
                initDataTable('basic-datatable');
            },
            error: function() {
                $('.expense_category_content').html('<div class="text-center text-danger p-4"><i class="fa-solid fa-triangle-exclamation fa-2x mb-2"></i><p><?php echo get_phrase('error_loading_data'); ?></p></div>');
            }
        });
    }
</script>