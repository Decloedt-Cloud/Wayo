<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">

<style>
/* ============================================================================
   INVOICE INDEX - MODERN FILTER DESIGN
   ============================================================================ */

:root {
    --idx-primary: #6366f1;
    --idx-primary-light: #eef2ff;
    --idx-success: #059669;
    --idx-dark: #1e293b;
    --idx-gray: #64748b;
    --idx-light: #f8fafc;
    --idx-border: #e2e8f0;
}

/* Header Card */
.idx-header {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.idx-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.idx-header-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.idx-header-text h4 {
    margin: 0;
    color: white;
    font-size: 1.35rem;
    font-weight: 700;
}

.idx-header-text p {
    margin: 0.25rem 0 0;
    color: #94a3b8;
    font-size: 0.85rem;
}

.idx-header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.idx-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.idx-btn-primary {
    background: linear-gradient(135deg, #1e293b, #1e293b);
    color: white;
}

.idx-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    color: white;
}

.idx-btn-success {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    color: white;
}

.idx-btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
    color: white;
}

/* Filter Panel */
.idx-filter-panel {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--idx-border);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.idx-filter-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--idx-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.idx-filter-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--idx-dark);
    font-size: 0.95rem;
}

.idx-filter-title i {
    color: var(--idx-primary);
}

.idx-filter-body {
    padding: 1.5rem;
}

.idx-filter-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 1rem;
    align-items: end;
}

@media (max-width: 1200px) {
    .idx-filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .idx-filter-grid {
        grid-template-columns: 1fr;
    }
}

.idx-filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.idx-filter-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--idx-gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.idx-filter-label i {
    font-size: 0.9rem;
    color: var(--idx-primary);
}

.idx-date-picker {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px solid var(--idx-border);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
    color: var(--idx-dark);
}

.idx-date-picker:hover {
    border-color: var(--idx-primary);
    background: white;
}

.idx-date-picker i {
    color: var(--idx-primary);
}

.idx-date-picker .idx-date-text {
    flex: 1;
    text-align: center;
    font-weight: 500;
}

.idx-select {
    padding: 0.75rem 1rem;
    border: 2px solid var(--idx-border);
    border-radius: 10px;
    font-size: 0.9rem;
    background: white;
    color: var(--idx-dark);
    cursor: pointer;
    transition: all 0.2s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234f46e5' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
}

.idx-select:focus {
    outline: none;
    border-color: var(--idx-primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.idx-filter-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.idx-filter-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Export Section */
.idx-export-row {
    display: flex;
    justify-content: flex-end;
    padding: 1rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-top: 1px solid var(--idx-border);
    gap: 0.5rem;
}

.idx-export-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid var(--idx-border);
    background: white;
    color: var(--idx-gray);
    cursor: pointer;
    transition: all 0.2s;
}

.idx-export-btn:hover {
    background: var(--idx-dark);
    color: white;
    border-color: var(--idx-dark);
}

.idx-export-btn.csv {
    color: #059669;
    border-color: #a7f3d0;
}

.idx-export-btn.csv:hover {
    background: #059669;
    color: white;
    border-color: #059669;
}

.idx-export-btn.pdf {
    color: #dc2626;
    border-color: #fecaca;
}

.idx-export-btn.pdf:hover {
    background: #dc2626;
    color: white;
    border-color: #dc2626;
}

/* Content Card */
.idx-content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border: 1px solid var(--idx-border);
    overflow: hidden;
}

.idx-content-body {
    padding: 0;
}

/* Loading State */
.idx-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: var(--idx-gray);
}

.idx-loading i {
    font-size: 2rem;
    animation: spin 1s linear infinite;
    margin-right: 0.75rem;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<!-- CSRF Token -->
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="csrf_token">

<!-- Header -->
<div class="idx-header">
    <div class="idx-header-left">
        <div class="idx-header-icon">
            <i class="mdi mdi-file-document-multiple"></i>
        </div>
        <div class="idx-header-text">
            <h4><?php echo get_phrase('invoicing'); ?></h4>
            <p><?php echo get_phrase('manage_invoices_and_payments'); ?></p>
        </div>
    </div>
    <div class="idx-header-actions">
        <!-- <button type="button" class="idx-btn idx-btn-success" onclick="rightModal('<?php // echo site_url('modal/popup/invoice/mass'); ?>', '<?php echo get_phrase('add_mass_invoice'); ?>')">
            <i class="mdi mdi-account-group"></i> <?php //echo get_phrase('add_mass_invoice'); ?>
        </button> -->
        <button type="button" class="idx-btn idx-btn-primary" onclick="rightModal('<?php echo site_url('modal/popup/invoice/single'); ?>', '<?php echo get_phrase('add_single_invoice'); ?>')">
            <i class="mdi mdi-plus"></i> <?php echo get_phrase('add_single_invoice'); ?>
        </button>
    </div>
</div>

<!-- Filter Panel -->
<div class="idx-filter-panel">
    <div class="idx-filter-header">
        <span class="idx-filter-title">
            <i class="mdi mdi-filter-variant"></i>
            <?php echo get_phrase('filter_invoices'); ?>
        </span>
    </div>
    <div class="idx-filter-body">
        <div class="idx-filter-grid">
            <!-- Date Range -->
            <div class="idx-filter-group">
                <label class="idx-filter-label">
                    <i class="mdi mdi-calendar-range"></i>
                    <?php echo get_phrase('date_range'); ?>
                </label>
                <div id="reportrange" class="idx-date-picker" data-toggle="date-picker-range" data-target-display="#selectedValue" data-cancel-class="btn-light">
                    <i class="mdi mdi-calendar"></i>
                    <span id="selectedValue" class="idx-date-text"><?php echo date('d M Y', $date_from).' — '.date('d M Y', $date_to); ?></span>
                    <i class="mdi mdi-chevron-down"></i>
                </div>
            </div>
            
            <!-- Class Filter -->
            <div class="idx-filter-group">
                <label class="idx-filter-label">
                    <i class="mdi mdi-school"></i>
                    <?php echo get_phrase('class'); ?>
                </label>
                <select name="class" id="class_id_invoice" class="idx-select">
                    <option value="all"><?php echo get_phrase('all_classes'); ?></option>
                    <?php
                    $classes = $this->db->get_where('classes', array('school_id' => school_id()))->result_array();
                    $school_id = school_id();
                    foreach($classes as $class){
                        $this->db->where('class_id', $class['id']); 
                        $this->db->where('school_id', $school_id);
                        $total_student = $this->db->get('enrols');
                    ?>
                    <option value="<?php echo $class['id']; ?>">
                        <?php echo $class['name']; ?> (<?php echo $total_student->num_rows(); ?>)
                    </option>
                    <?php } ?>
                </select>
            </div>
            
            <!-- Status Filter -->
            <div class="idx-filter-group">
                <label class="idx-filter-label">
                    <i class="mdi mdi-flag"></i>
                    <?php echo get_phrase('status'); ?>
                </label>
                <select name="status" id="status_invoice" class="idx-select">
                    <option value="all"><?php echo get_phrase('all_statuses'); ?></option>
                    <option value="paid"><?php echo get_phrase('paid'); ?></option>
                    <option value="unpaid"><?php echo get_phrase('unpaid'); ?></option>
                </select>
            </div>
            
            <!-- Filter Button -->
            <div class="idx-filter-group">
                <label class="idx-filter-label">&nbsp;</label>
                <button type="button" class="idx-filter-btn" onclick="showAllInvoices()">
                    <i class="mdi mdi-magnify"></i>
                    <?php echo get_phrase('apply_filter'); ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Export Row -->
    <div class="idx-export-row">
        <span style="color: var(--idx-gray); font-size: 0.85rem; margin-right: auto;">
            <i class="mdi mdi-download"></i> <?php echo get_phrase('export'); ?>:
        </span>
        <button type="button" class="idx-export-btn csv" onclick="getExportUrl('csv')">
            <i class="mdi mdi-file-delimited"></i> CSV
        </button>
        <button type="button" class="idx-export-btn pdf" onclick="getExportUrl('pdf')">
            <i class="mdi mdi-file-pdf-box"></i> PDF
        </button>
    </div>
</div>

<!-- Content Card -->
<div class="idx-content-card">
    <div class="idx-content-body">
        <div class="invoice_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>

<script>
var showAllInvoices = function () {
    // Show loading state
    $('.invoice_content').html('<div class="idx-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
    
    var url = '<?php echo route('invoice/list'); ?>';
    var dateRange = $('#selectedValue').text();
    var selectedClass = $('#class_id_invoice').val();
    var selectedStatus = $('#status_invoice').val();
    
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            date: dateRange, 
            selectedClass: selectedClass, 
            selectedStatus: selectedStatus
        },
        success: function(response) {
            $('.invoice_content').html(response);
            initDataTable("basic-datatable");
        },
        error: function() {
            $('.invoice_content').html('<div class="idx-loading" style="color: #dc2626;"><i class="mdi mdi-alert-circle"></i> <?php echo get_phrase('error_loading_data'); ?></div>');
        }
    });
}

function getExportUrl(type) {
    var url = '<?php echo route('export/url'); ?>';
    var dateRange = $('#selectedValue').text();
    var selectedClass = $('#class_id_invoice').val();
    var selectedStatus = $('#status_invoice').val();
    
    // Get CSRF token
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = $('#csrf_token').val();
    
    // Validate CSRF token exists
    if (!csrfHash) {
        console.error('CSRF token not found');
        alert('<?php echo get_phrase('error_security_token'); ?>');
        return;
    }
    
    var postData = {
        type: type, 
        dateRange: dateRange, 
        selectedClass: selectedClass, 
        selectedStatus: selectedStatus
    };
    postData[csrfName] = csrfHash;
    
    $.ajax({
        type: 'POST',
        url: url,
        data: postData,
        dataType: 'json',
        success: function(response) {
            if (response && response.url) {
                if (type == 'csv') {
                    window.open(response.url, '_self');
                } else {
                    window.open(response.url, '_blank');
                }
                
                // Update CSRF token for next request
                if (response.csrf && response.csrf.csrfHash) {
                    $('#csrf_token').val(response.csrf.csrfHash);
                }
            } else {
                console.error('Invalid response:', response);
                alert('<?php echo get_phrase('export_error'); ?>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Export error:', status, error);
            alert('<?php echo get_phrase('export_error'); ?>: ' + error);
        }
    });
}

// Auto-apply filter on select change (optional UX improvement)
$('#class_id_invoice, #status_invoice').on('change', function() {
    showAllInvoices();
});
</script>
