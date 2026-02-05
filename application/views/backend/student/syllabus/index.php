<?php $student_data = $this->user_model->get_logged_in_student_details(); ?>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
/* ========== MODERN DASHBOARD STYLES ========== */
.modern-dashboard {
  /* MONOCHROMATIC THEME (INDIGO) */
  --primary: #6366f1;
  --primary-light: #818cf8;
  --primary-lighter: #e0e7ff;
  --primary-dark: #4338ca;
  --secondary: #10b981; /* Green for success */
  --bg-main: #f8fafc;
  --bg-card: #ffffff;
  --text-dark: #1e293b;
  --text-muted: #64748b;
  --border-color: #e2e8f0;
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
  --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
  
  font-family: 'DM Sans', sans-serif;
  background: var(--bg-main);
  min-height: 100vh;
  padding: 1.5rem;
  margin: -15px -15px 0 -15px;
}

/* Header */
.dash-header {
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}

.dash-header h1 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.dash-header h1 .icon-box {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
}

.dash-header .date-badge {
  background: var(--bg-card);
  padding: 0.625rem 1rem;
  border-radius: 50px;
  font-size: 0.875rem;
  color: var(--text-muted);
  box-shadow: var(--shadow-sm);
  border: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dash-header .date-badge i {
  color: var(--primary);
}

/* Modern Card */
.modern-card {
  background: var(--bg-card);
  border-radius: 20px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  overflow: hidden;
  transition: all 0.3s ease;
  margin-bottom: 1.5rem;
}

.modern-card:hover {
  box-shadow: var(--shadow-lg);
}

.modern-card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(180deg, #fafbfc, transparent);
}

.modern-card-header h3 {
  font-family: 'Outfit', sans-serif;
  font-size: 1.0625rem;
  font-weight: 600;
  color: var(--text-dark);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.modern-card-header h3 i {
  color: var(--primary);
  font-size: 1.125rem;
}

.modern-card-body {
  padding: 1.5rem;
}

/* Form Elements (from Attendance) */
.modern-select {
    border: 1px solid var(--border-color);
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
    color: var(--text-dark);
    transition: all 0.2s;
    background-color: white;
}
.modern-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-lighter);
    outline: none;
}

.modern-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.6rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    transition: all 0.2s ease;
    gap: 0.5rem;
    cursor: pointer;
    background: var(--primary);
    color: white;
    box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
    width: 100%;
}
.modern-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
    color: white;
}

/* Modern Table (from Attendance) */
.modern-table-wrapper {
    background: white;
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    margin-bottom: 0;
}
.modern-table thead th {
    background: var(--bg-main);
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
    border-top: none;
}
.modern-table tbody td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-dark);
    font-weight: 500;
    font-size: 0.9rem;
}
.modern-table tbody tr:last-child td {
    border-bottom: none;
}
.modern-table tbody tr:hover td {
    background-color: var(--bg-main);
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current, 
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: var(--primary) !important;
    color: white !important;
    border-color: var(--primary) !important;
}

/* Chat AI Button Styles */
.btn-chat-ai {
    background: linear-gradient(90deg, #f47a1f 0%, #fbb040 100%);
    border: none;
    color: #fff;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    line-height: 1.5;
    display: inline-flex;
    align-items: center;
    vertical-align: middle;
    border-radius: 8px;
    font-size: 0.85rem;
}
.btn-chat-ai:hover {
    background: linear-gradient(135deg, #ffb74d 0%, #ff9800 100%);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(244, 122, 31, 0.3);
}
.sparkle-icon {
    display: inline-block;
    animation: sparkle 1.5s ease-in-out infinite;
    margin-right: 6px;
}
@keyframes sparkle {
    0%, 100% {
        text-shadow: 0 0 5px rgba(255, 255, 255, 0.8), 0 0 10px rgba(255, 255, 255, 0.6), 0 0 15px rgba(255, 215, 0, 0.4);
        transform: scale(1) rotate(0deg);
    }
    25% {
        text-shadow: 0 0 10px rgba(255, 255, 255, 1), 0 0 20px rgba(255, 215, 0, 0.8), 0 0 30px rgba(255, 152, 0, 0.6);
        transform: scale(1.1) rotate(5deg);
    }
    50% {
        text-shadow: 0 0 15px rgba(255, 255, 255, 1), 0 0 25px rgba(255, 215, 0, 1), 0 0 35px rgba(255, 152, 0, 0.8);
        transform: scale(1.2) rotate(0deg);
    }
    75% {
        text-shadow: 0 0 10px rgba(255, 255, 255, 1), 0 0 20px rgba(255, 215, 0, 0.8), 0 0 30px rgba(255, 152, 0, 0.6);
        transform: scale(1.1) rotate(-5deg);
    }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}
.empty-state img {
    max-width: 200px;
    opacity: 0.8;
    margin-bottom: 1.5rem;
}
.empty-state p {
    color: var(--text-muted);
    font-size: 1.1rem;
}
</style>

<div class="modern-dashboard">
    <!-- Header -->
    <div class="dash-header">
        <h1>
            <div class="icon-box"><i class="fas fa-folder-open"></i></div>
            <?php echo get_phrase('syllabus'); ?>
        </h1>
        <div class="date-badge">
            <i class="mdi mdi-calendar-today"></i> <?php echo date('d M, Y'); ?>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="modern-card">
        <div class="modern-card-header">
            <h3><i class="mdi mdi-filter-variant"></i> <?php echo get_phrase('Filter Syllabus'); ?></h3>
        </div>
        <div class="modern-card-body">
            <div class="row align-items-end">
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted fw-bold mb-2"><?php echo get_phrase('Class'); ?></label>
                    <select name="class" id="class_id_syllabus" class="form-control modern-select" required>
                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <button class="modern-btn" onclick="filter_syllabus()"><i class="mdi mdi-filter"></i> <?php echo get_phrase('filter'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Syllabus Content Card -->
    <div class="modern-card">
        <div class="modern-card-header">
            <h3><i class="mdi mdi-file-document-outline"></i> <?php echo get_phrase('Syllabus List'); ?></h3>
        </div>
        <div class="modern-card-body syllabus_content">
            <?php  include 'list.php'; ?>
        </div>
    </div>
</div>

<script>
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); }); 

    // Charger automatiquement les classes de l'école active et sélectionner "all"
    var active_school_id = '<?php echo school_id(); ?>';
    if(active_school_id) {
        schoolWiseClasse(active_school_id);
    }
});

function filter_syllabus(){
    var class_id = $('#class_id_syllabus').val();
    
    if(class_id != "" ){
        showAllSyllabuses();
    }else{
        toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
    }
}

var showAllSyllabuses = function () {
    var class_id = $('#class_id_syllabus').val();
   
    if(class_id != ""){
        $.ajax({
            url: '<?php echo route('syllabus/list/') ?>'+class_id,
            success: function(response){
                $('.syllabus_content').html(response);
                initDataTable('basic-datatable');
            }
        });
    }
}

function schoolWiseClasse(school_id) {
    $.ajax({
        url: "<?php echo route('academy/list/'); ?>"+school_id,
        success: function(response){
            $('#class_id_syllabus').html(response);
            // Sélectionner automatiquement "all" et charger les syllabus
            $('#class_id_syllabus').val('all');
            showAllSyllabuses();
        }
    });
}
</script>