<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">

<style>
:root {
    --promo-primary: #6366f1;
    --promo-primary-rgb: 99, 102, 241;
    --promo-success: #059669;
    --promo-dark: #1e293b;
    --promo-gray: #64748b;
    --promo-light: #f8fafc;
    --promo-border: #e2e8f0;
}

.promo-header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); }
.promo-header-left { display: flex; align-items: center; gap: 1rem; }
.promo-header-icon { width: 50px; height: 50px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
.promo-header-text h4 { margin: 0; color: white; font-size: 1.35rem; font-weight: 700; }
.promo-header-text p { margin: 0.25rem 0 0; color: rgba(255,255,255,0.7); font-size: 0.85rem; }

.promo-filter-section { padding: 1.5rem; background: white; border-radius: 16px; margin-bottom: 1.5rem; border: 1px solid var(--promo-border); box-shadow: 0 2px 12px rgba(0,0,0,0.04); }
.promo-filter-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 140px; gap: 1rem; align-items: end; }
.promo-filter-group { display: flex; flex-direction: column; gap: 0.5rem; }
.promo-filter-label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 600; color: var(--promo-dark); text-transform: uppercase; letter-spacing: 0.5px; }
.promo-filter-label i { color: var(--promo-primary); font-size: 1rem; }
.promo-filter-input { width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--promo-border); border-radius: 10px; font-size: 0.9375rem; background: var(--promo-light); color: var(--promo-dark); transition: all 0.2s; cursor: pointer; }
.promo-filter-input:focus { outline: none; border-color: var(--promo-primary); background: white; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
.promo-filter-btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, var(--promo-primary), #8b5cf6); color: white; border: none; border-radius: 10px; font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: all 0.2s; height: fit-content; }
.promo-filter-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }

.promo-content-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); border: 1px solid var(--promo-border); overflow: hidden; }
.promo-content-header { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); padding: 1rem 1.5rem; border-bottom: 1px solid var(--promo-border); display: flex; justify-content: space-between; align-items: center; }
.promo-content-title { display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--promo-dark); font-size: 0.95rem; }
.promo-content-title i { color: var(--promo-primary); }
.promo-content-body { padding: 0; }

.promo-loading { display: flex; align-items: center; justify-content: center; padding: 3rem; color: var(--promo-gray); }
.promo-loading i { font-size: 2rem; animation: promo-spin 1s linear infinite; margin-right: 0.75rem; }
@keyframes promo-spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

@media (max-width: 992px) { .promo-filter-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 768px) { 
    .promo-header { flex-direction: column; text-align: center; }
    .promo-header-left { flex-direction: column; }
    .promo-filter-grid { grid-template-columns: 1fr; }
}
</style>

<!-- Header -->
<div class="promo-header">
    <div class="promo-header-left">
        <div class="promo-header-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="promo-header-text">
            <h4><?php echo get_phrase('student_promotion'); ?></h4>
            <p><?php echo get_phrase('promote_students_to_next_class'); ?></p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="promo-filter-section">
    <div class="promo-filter-grid">
        <div class="promo-filter-group">
            <label class="promo-filter-label">
                <i class="mdi mdi-calendar"></i>
                <span><?php echo get_phrase('current_session'); ?></span>
            </label>
            <select class="promo-filter-input" id="session_from" name="session_from">
                <option value=""><?php echo get_phrase('session_from'); ?></option>
                <?php $sessions = $this->crud_model->get_session();
                foreach ($sessions as $session): ?>
                <option value="<?php echo $session['id']; ?>"><?php echo $session['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="promo-filter-group">
            <label class="promo-filter-label">
                <i class="mdi mdi-calendar-arrow-right"></i>
                <span><?php echo get_phrase('next_session'); ?></span>
            </label>
            <select class="promo-filter-input" id="session_to" name="session_to">
                <option value=""><?php echo get_phrase('session_to'); ?></option>
                <?php foreach ($sessions as $session): ?>
                <option value="<?php echo $session['id']; ?>"><?php echo $session['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="promo-filter-group">
            <label class="promo-filter-label">
                <i class="mdi mdi-arrow-right-bold-box"></i>
                <span><?php echo get_phrase('promoting_from'); ?></span>
            </label>
            <select class="promo-filter-input" id="class_id_from" name="class_id_from">
                <option value=""><?php echo get_phrase('promoting_from'); ?></option>
                <?php $classes = $this->crud_model->get_classes();
                $school_id = school_id();
                foreach ($classes as $class):
                    $total_student = db()->table('enrols')
                        ->where('class_id', $class['id'])
                        ->where('school_id', $school_id)
                        ->get()
                        ->getResultArray(); ?>
                <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?> (<?php echo count($total_student); ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="promo-filter-group">
            <label class="promo-filter-label">
                <i class="mdi mdi-arrow-left-bold-box"></i>
                <span><?php echo get_phrase('promoting_to'); ?></span>
            </label>
            <select class="promo-filter-input" id="class_id_to" name="class_id_to">
                <option value=""><?php echo get_phrase('promoting_to'); ?></option>
                <?php foreach ($classes as $class): ?>
                <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="promo-filter-group">
            <button type="button" class="promo-filter-btn" onclick="manageStudent()">
                <i class="mdi mdi-account-switch"></i>
                <span><?php echo get_phrase('manage'); ?></span>
            </button>
        </div>
    </div>
</div>

<!-- Content Card -->
<div class="promo-content-card">
    <div class="promo-content-header">
        <span class="promo-content-title">
            <i class="mdi mdi-account-group"></i>
            <?php echo get_phrase('students_to_promote'); ?>
        </span>
    </div>
    <div class="promo-content-body">
        <div class="student_to_promote_content">
            <?php include 'list.php'; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
$('document').ready(function(){
    $('select.select2:not(.normal)').each(function () { $(this).select2({ dropdownParent: '#right-modal' }); });
});

document.addEventListener('DOMContentLoaded', function () {
    $('#class_id_from').select2();
    $('#class_id_to').select2();

    $('#class_id_from').on('change', function () {
        const selectedValue = $(this).val();
        $('#class_id_to option').prop('disabled', false);
        if (selectedValue) {
            $(`#class_id_to option[value="${selectedValue}"]`).prop('disabled', true);
        }
        $('#class_id_to').select2();
    });
});

function manageStudent() {
    var session_from   = $('#session_from').val();
    var session_to     = $('#session_to').val();
    var class_id_from  = $('#class_id_from').val();
    var class_id_to    = $('#class_id_to').val();
    var csrfName = $('input[name="<?= csrf_token(); ?>"]').attr('name');
    var csrfHash = $('input[name="<?= csrf_token(); ?>"]').val();

    if(session_from > 0 && session_to > 0 && class_id_from > 0 && class_id_to > 0) {
        $('.student_to_promote_content').html('<div class="promo-loading"><i class="mdi mdi-loading"></i> <?php echo get_phrase('loading'); ?>...</div>');
        
        $.ajax({
            type: 'POST',
            url: '<?php echo route('promotion/list'); ?>',
            data: { session_from: session_from, session_to: session_to, class_id_from: class_id_from, class_id_to: class_id_to, [csrfName]: csrfHash },
            dataType: 'json',
            success: function(response) {
                $('.student_to_promote_content').html(response.status);
                var newCsrfName = response.csrf.csrfName;
                var newCsrfHash = response.csrf.csrfHash;
                $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
            }
        });
    } else {
        toastr.error('<?php echo get_phrase('please_make_sure_to_fill_all_the_necessary_fields'); ?>');
    }
}

function enrollStudent(promotion_data, enroll_id) {
    $.ajax({
        type: 'get',
        url: '<?php echo route('promotion/promote/'); ?>'+promotion_data,
        success: function(response) {
            if (response) {
                $("#success_"+enroll_id).show();
                $("#danger_"+enroll_id).hide();
                success_notify('<?php echo get_phrase('student_promoted_successfully'); ?>');
            } else {
                toastr.error(<?= js_phrase(get_phrase('an_error_occured')); ?>);
            }
        }
    });
}
</script>
