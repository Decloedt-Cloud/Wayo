<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
  --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
  
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

/* Modern Cards */
.modern-card {
  background: var(--bg-card);
  border-radius: 20px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-color);
  overflow: hidden;
  transition: all 0.3s ease;
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

/* Form Elements */
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
}
.modern-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
    color: white;
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
            <div class="icon-box">
                <i class="fas fa-clipboard-user"></i>
            </div>
            <?php echo get_phrase('daily_attendance'); ?>
        </h1>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="modern-card">
                <div class="modern-card-header">
                    <h3><i class="fas fa-filter"></i> <?php echo get_phrase('filter_attendance'); ?></h3>
                </div>
                <div class="modern-card-body">
                    <div class="row align-items-end d-print-none">
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted fw-bold mb-2"><?php echo get_phrase('Month'); ?></label>
                            <select name="month" id="month" class="form-control modern-select" required>
                                <option value=""><?php echo get_phrase('select_a_month'); ?></option>
                                <option value="Jan" <?php if (date('M') == 'Jan') echo 'selected'; ?>><?php echo get_phrase('january'); ?></option>
                                <option value="Feb" <?php if (date('M') == 'Feb') echo 'selected'; ?>><?php echo get_phrase('february'); ?></option>
                                <option value="Mar" <?php if (date('M') == 'Mar') echo 'selected'; ?>><?php echo get_phrase('march'); ?></option>
                                <option value="Apr" <?php if (date('M') == 'Apr') echo 'selected'; ?>><?php echo get_phrase('april'); ?></option>
                                <option value="May" <?php if (date('M') == 'May') echo 'selected'; ?>><?php echo get_phrase('may'); ?></option>
                                <option value="Jun" <?php if (date('M') == 'Jun') echo 'selected'; ?>><?php echo get_phrase('june'); ?></option>
                                <option value="Jul" <?php if (date('M') == 'Jul') echo 'selected'; ?>><?php echo get_phrase('july'); ?></option>
                                <option value="Aug" <?php if (date('M') == 'Aug') echo 'selected'; ?>><?php echo get_phrase('august'); ?></option>
                                <option value="Sep" <?php if (date('M') == 'Sep') echo 'selected'; ?>><?php echo get_phrase('september'); ?></option>
                                <option value="Oct" <?php if (date('M') == 'Oct') echo 'selected'; ?>><?php echo get_phrase('october'); ?></option>
                                <option value="Nov" <?php if (date('M') == 'Nov') echo 'selected'; ?>><?php echo get_phrase('november'); ?></option>
                                <option value="Dec" <?php if (date('M') == 'Dec') echo 'selected'; ?>><?php echo get_phrase('december'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted fw-bold mb-2"><?php echo get_phrase('Year'); ?></label>
                            <select name="year" id="year" class="form-control modern-select" required>
                                <option value=""><?php echo get_phrase('select_a_year'); ?></option>
                                <?php for ($year = 2015; $year <= date('Y'); $year++) { ?>
                                    <option value="<?php echo $year; ?>" <?php if (date('Y') == $year) echo 'selected'; ?>><?php echo $year; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label text-muted fw-bold mb-2"><?php echo get_phrase('Class'); ?></label>
                            <select name="class" id="class_id_attendance" class="form-control modern-select" required>
                                <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <button class="modern-btn w-100" onclick="filter_attendance()">
                                <i class="mdi mdi-filter"></i> <?php echo get_phrase('filter'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="attendance_content mt-4">
                        <div class="empty-state">
                            <img src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" alt="No Data" />
                            <p><?php echo get_phrase('no_data_found'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('select.select2:not(.normal)').each(function() {
            $(this).select2({
                dropdownParent: '#right-modal'
            });
        });
        //initSelect2(['#month', '#year', '#class_id']);
        
        // Charger les classes de l'école active au chargement de la page
        loadClassesForActiveSchool();
    });


    function filter_attendance() {
        var month = $('#month').val();
        var year = $('#year').val();
        var class_id = $('#class_id_attendance').val();

        if (class_id != "" && month != "" && year != "") {
            getDailtyAttendance();
        } else {
            toastr.error('<?php echo get_phrase('please_select_in_all_fields!'); ?>');
        }
    }

    var getDailtyAttendance = function() {
        var month = $('#month').val();
        var year = $('#year').val();
        var class_id = $('#class_id_attendance').val();
        var school_id = <?php echo session()->get('active_school_id'); ?>; // Utiliser l'école active
        // Récupérer le nom et la valeur du jeton CSRF depuis l'input caché
        var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
        if (class_id != "" && month != "" && year != "") {
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('student/attendance/filter'); ?>',
                data: {
                    month: month,
                    year: year,
                    class_id: class_id,
                    school_id: school_id,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function(response) {
                    $('.attendance_content').html(response.status);
                    // Mettre à jour le jeton CSRF avec le nouveau jeton renvoyé dans la réponse
                    var newCsrfName = response.csrf.csrfName;
                    var newCsrfHash = response.csrf.csrfHash;
                    $('input[name="' + newCsrfName + '"]').val(newCsrfHash); // Mise à jour du token CSRF
                    initDataTable('basic-datatable');
                }
            });
        }
    }

    // Charger les classes de l'école active
    function loadClassesForActiveSchool() {
        var school_id = <?php echo session()->get('active_school_id'); ?>;
        if (school_id) {
            var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
            var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();
            
            $.ajax({
                type: 'POST',
                url: '<?php echo site_url('student/academy/list/'); ?>' + school_id,
                data: {
                    [csrfName]: csrfHash
                },
                success: function(response) {
                    $('#class_id_attendance').html(response);
                }
            });
        }
    }
</script>
