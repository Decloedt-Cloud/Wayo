<div class="modern-dashboard">
    <?php
    $current_school_id = school_id();
    $student_status_check = db()->table('students')
        ->where('user_id', session()->get('user_id'))
        ->where('school_id', $current_school_id)
        ->get()
        ->getRowArray();

    if ($student_status_check && (int)$student_status_check['status'] === 0):
    ?>
        <div class="alert alert-warning mb-4" role="alert" style="border-radius: 15px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: linear-gradient(135deg, #fff3cd, #ffecb5);">
            <h4 class="alert-heading d-flex align-items-center" style="color: #856404; font-weight: 700;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo get_phrase('pending_approval'); ?>
            </h4>
            <p class="mb-0 mt-2" style="color: #856404; font-size: 1.1em;">
                <?php echo get_phrase('you_must_wait_for_the_community_owner_approval_to_access_services'); ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="dash-header">
        <h1>
            <div class="icon-box">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <?php echo get_phrase('invoicing'); ?>
        </h1>
        <div class="header-actions">
            <div class="date-badge">
                <i class="far fa-calendar-alt"></i>
                <span><?php echo get_phrase(strtolower(date('l'))) . ', ' . date('j') . ' ' . get_phrase(strtolower(date('F'))) . ' ' . date('Y'); ?></span>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="modern-card">
        <div class="modern-card-header">
            <div class="header-content">
                <h4 class="card-title"><?php echo get_phrase('student_fee_report'); ?></h4>
                <p class="card-subtitle"><?php echo get_phrase('track_your_payments_and_invoices'); ?></p>
            </div>
        </div>

        <div class="modern-card-body">
            <div class="invoice_content">
                <?php include 'list.php'; ?>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Outfit:wght@400;500;600&display=swap');

    :root {
        --primary-color: #4318FF;
        --secondary-color: #F4F7FE;
        --text-color: #2B3674;
        --text-secondary: #A3AED0;
        --card-bg: #FFFFFF;
        --border-radius: 20px;
        --shadow-sm: 0 2px 12px rgba(43, 54, 116, 0.04);
        --shadow-md: 0 4px 20px rgba(43, 54, 116, 0.08);
        --font-primary: 'DM Sans', sans-serif;
        --font-secondary: 'Outfit', sans-serif;
    }

    /* Base Layout */
    .modern-dashboard {
        padding: 2rem;
        font-family: var(--font-primary);
        color: var(--text-color);
        background-color: var(--secondary-color);
        min-height: 100vh;
    }

    /* Header Styles */
    .dash-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
    }

    .dash-header h1 {
        font-family: var(--font-secondary);
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-color);
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 0;
    }

    .icon-box {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary-color), #6b4cff);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        box-shadow: 0 8px 16px rgba(67, 24, 255, 0.2);
    }

    .date-badge {
        background: white;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        color: var(--text-secondary);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: var(--shadow-sm);
    }

    /* Card Styles */
    .modern-card {
        background: var(--card-bg);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .modern-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .card-title {
        font-family: var(--font-secondary);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-color);
        margin: 0 0 0.5rem 0;
    }

    .card-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
    }

    /* Filter/Controls */
    .modern-select {
        padding: 0.75rem 1.5rem;
        border: 1px solid #E0E5F2;
        border-radius: 12px;
        background-color: #F8F9FC;
        color: var(--text-color);
        font-family: var(--font-primary);
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        min-width: 200px;
    }

    .modern-select:focus {
        border-color: var(--primary-color);
        background-color: white;
        outline: none;
        box-shadow: 0 0 0 3px rgba(67, 24, 255, 0.1);
    }

    .modern-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modern-btn.primary {
        background: linear-gradient(135deg, var(--primary-color), #6b4cff);
        color: white;
        box-shadow: 0 4px 12px rgba(67, 24, 255, 0.2);
    }

    .modern-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(67, 24, 255, 0.3);
    }

    /* Table Styles */
    .modern-table-wrapper {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #E0E5F2;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
    }

    .modern-table th {
        background-color: #F8F9FC;
        padding: 1rem 1.5rem;
        text-align: left;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modern-table td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #F4F7FE;
        color: var(--text-color);
        font-size: 0.95rem;
    }

    .modern-table tr:last-child td {
        border-bottom: none;
    }

    .modern-table tr:hover td {
        background-color: #F8F9FC;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state img {
        width: 120px;
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-family: var(--font-secondary);
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--text-secondary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modern-dashboard {
            padding: 1rem;
        }

        .dash-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .modern-card {
            padding: 1.5rem;
        }

        .modern-table-wrapper {
            overflow-x: auto;
        }
    }
</style>

<script>
    var showAllInvoices = function() {
        var url = '<?php echo route('invoice/list'); ?>';
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                date: $('#selectedValue').text()
            },
            success: function(response) {
                $('.invoice_content').html(response);
                // Re-init any plugins if needed
            }
        });
    }
</script>