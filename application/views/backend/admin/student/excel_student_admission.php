<?php $school_id = school_id(); ?>
<form method="POST" class="d-block ajaxForm" action="<?php echo route('student/create_excel'); ?>" id="student_admission_form" enctype="multipart/form-data">
    <!-- CSRF Token -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <!-- Info Card -->
    <div class="ec-info-card">
        <div class="ec-info-card-icon">
            <i class="mdi mdi-file-excel"></i>
        </div>
        <div class="ec-info-card-content">
            <h4><?php echo get_phrase('bulk_import_from_excel'); ?></h4>
            <p><?php echo get_phrase('upload_a_csv_file_to_import_students_in_bulk'); ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <!-- Class Selection -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-google-classroom"></i>
                    <span><?php echo get_phrase('class'); ?></span>
                    <span class="ec-required">*</span>
                </label>
                <div class="ec-input-wrapper">
                    <select name="class_id" id="class_id_excel" class="ec-form-input" required>
                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                        <?php $classes = $this->db->get_where('classes', array('school_id' => $school_id))->result_array(); ?>
                        <?php foreach($classes as $class){ ?>
                            <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                        <?php } ?>
                    </select>
                    <i class="mdi mdi-school ec-input-icon"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- Tools -->
            <div class="ec-form-group">
                <label class="ec-form-label">
                    <i class="mdi mdi-tools"></i>
                    <span><?php echo get_phrase('tools'); ?></span>
                </label>
                <div class="d-flex gap-2">
                    <a href="<?php echo base_url('assets/csv_file/student.generate.csv'); ?>" class="ec-btn ec-btn-secondary" download style="min-width: auto; flex: 1;">
                        <i class="mdi mdi-download"></i>
                        <span><?php echo get_phrase('generate_csv'); ?></span>
                    </a>
                    <button type="button" class="ec-btn ec-btn-secondary" onclick="largeModal('<?php echo site_url('modal/popup/student/csv_preview'); ?>', 'CSV Format');" style="min-width: auto;">
                        <i class="mdi mdi-eye"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- File Upload -->
    <div class="ec-form-group">
        <label class="ec-form-label">
            <i class="mdi mdi-cloud-upload"></i>
            <span><?php echo get_phrase('upload_csv_file'); ?></span>
            <span class="ec-required">*</span>
        </label>
        <div class="ec-input-wrapper">
            <input type="file" id="csv_file" class="ec-form-input" name="csv_file" required accept=".csv" style="padding-left: 1rem;">
            <!-- Removed icon for file input as it interferes with the native file picker text -->
        </div>
    </div>

    <!-- Form Actions -->
    <div class="ec-form-actions justify-content-center">
        <button type="submit" class="ec-btn ec-btn-primary" id="submit-btn">
            <i class="mdi mdi-database-import"></i>
            <span><?php echo get_phrase('import_students'); ?></span>
        </button>
    </div>
</form>

<script>
$(document).ready(function(){
    initCustomFileUploader();

    $('#student_admission_form').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#submit-btn');
        const originalBtnHtml = btn.html();
        
        btn.prop('disabled', true)
           .html('<i class="mdi mdi-loading mdi-spin"></i> <span><?php echo get_phrase('importing'); ?>...</span>');

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if(response.type === 'error') {
                    btn.prop('disabled', false).html(originalBtnHtml);
                    error_notify(response.notification);
                } else {
                    btn.removeClass('ec-btn-primary')
                        .addClass('ec-btn-success')
                        .html('<i class="mdi mdi-check-circle"></i> <span><?php echo get_phrase('imported'); ?>!</span>');
                    
                    success_notify(response.notification);
                    
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalBtnHtml);
                error_notify("<?php echo get_phrase('an_error_occurred'); ?>");
            }
        });
    });
});
</script>