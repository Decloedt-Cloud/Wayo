    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.min.css">
    
    <style>
    /* ============================================================================
       PERMISSION MANAGER - MODERN DESIGN (MATCHING TEACHER/STUDENT STYLE)
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
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .exp-filter-group {
        flex: 1;
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

    .exp-filter-select {
        padding: 0.75rem 1rem;
        border: 1px solid var(--exp-border);
        border-radius: 10px;
        font-size: 0.9rem;
        color: var(--exp-dark);
        background: var(--exp-light);
        transition: all 0.2s;
        width: 100%;
        cursor: pointer;
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
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .exp-filter-select:focus {
        outline: none;
        border-color: var(--exp-primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .exp-filter-btn {
        padding: 0.75rem 2rem;
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

    .exp-export-btn {
        background: #059669;
    }

    .exp-export-btn:hover {
        background: #047857;
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
    .exp-list-container {
        border: 1px solid var(--exp-border);
        border-radius: 12px;
        overflow-x: auto;
        background: white;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        min-height: 400px;
    }

    .exp-list-header,
    .exp-list-item {
        display: grid;
        grid-template-columns: minmax(200px, 2.2fr) repeat(3, minmax(110px, 1fr)) minmax(110px, 0.9fr);
        min-width: 720px;
        column-gap: 0.75rem;
    }

    .exp-list-header {
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-bottom: 1px solid var(--exp-border);
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: white;
        letter-spacing: 0.4px;
    }

    .exp-list-header div {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        min-width: 0;
        white-space: nowrap;
    }

    .exp-list-header div:first-child {
        justify-content: flex-start;
    }

    .exp-list-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--exp-border);
        align-items: center;
        transition: background 0.2s;
        font-size: 0.95rem;
        color: var(--exp-dark);
        background: white;
    }

    .exp-list-item:hover {
        background: var(--exp-primary-light);
    }
    
    .exp-list-item:last-child {
        border-bottom: none;
    }
    
    /* Toggle Switch */
    .exp-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    
    .exp-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .exp-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }
    
    .exp-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    input:checked + .exp-slider {
        background-color: var(--exp-success);
    }
    
    input:checked + .exp-slider:before {
        transform: translateX(20px);
    }
    
    .exp-teacher-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .exp-teacher-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--exp-border);
    }
    
    .exp-teacher-name {
        font-weight: 600;
        color: var(--exp-dark);
    }
    
    /* Empty State */
    .exp-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
    }
    
    .exp-empty-img {
        width: 180px;
        margin-bottom: 1.5rem;
        opacity: 0.8;
    }
    
    .exp-empty-text {
        font-size: 1.1rem;
        color: var(--exp-gray);
        font-weight: 500;
    }
    </style>

    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="exp-header">
                <div class="exp-header-left">
                    <div class="exp-header-icon">
                        <i class="mdi mdi-shield-account"></i>
                    </div>
                    <div class="exp-header-text">
                        <h4><?php echo get_phrase('assigned_permission_for_teacher'); ?></h4>
                        <p><?php echo get_phrase('manage_teacher_access_rights'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Content -->
    <div class="row">
        <div class="col-12">
            <div class="exp-filter-section">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="exp-filter-grid">
                            <div class="exp-filter-group">
                                <label class="exp-filter-label">
                                    <i class="mdi mdi-google-classroom"></i> <?php echo get_phrase('select_class'); ?>
                                </label>
                                <select name="class" id="class_id_perm" class="exp-filter-select" required>
                                    <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                                    <?php
                                    $classes = db()->table('classes')->where('school_id', school_id())->get()->getResultArray();
                                    $school_id = school_id();
                                    
                                    $class_student_counts = [];
                                    if (!empty($classes)) {
                                        $class_ids = array_column($classes, 'id');
                                        $enrol_counts = db()->table('enrols')
                                            ->select('class_id, COUNT(*) as student_count')
                                            ->where('school_id', $school_id)
                                            ->whereIn('class_id', $class_ids)
                                            ->groupBy('class_id')
                                            ->get()
                                            ->getResultArray();
                                        
                                        foreach ($enrol_counts as $count) {
                                            $student_count = isset($count['student_count']) ? $count['student_count'] : 0;
                                            $class_student_counts[$count['class_id']] = $student_count;
                                        }
                                    }
                                    
                                    foreach($classes as $class){
                                        $student_count = isset($class_student_counts[$class['id']]) ? $class_student_counts[$class['id']] : 0;
                                        $selected = (isset($class_id) && $class_id == $class['id']) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $class['id']; ?>" <?php echo $selected; ?>>
                                            <?php echo $class['name']; ?>
                                            <?php echo "(".$student_count.")"; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="exp-filter-group">
                                <label class="exp-filter-label">
                                    <i class="mdi mdi-magnify"></i> <?php echo get_phrase('search_teacher'); ?>
                                </label>
                                <input type="text" id="teacher_search" class="exp-filter-input" placeholder="<?php echo get_phrase('search_by_name_or_email'); ?>" onkeyup="searchTeachers()">
                            </div>
                            <button class="exp-filter-btn" onclick="filter()">
                                <i class="mdi mdi-filter"></i> <?php echo get_phrase('filter'); ?>
                            </button>
                            <button class="exp-filter-btn exp-export-btn" onclick="exportPermissions()">
                                <i class="mdi mdi-file-export"></i> <?php echo get_phrase('export'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
            
            <div class="exp-list-container">
                <div class="permission_content">
                    <div class="exp-empty-state">
                        <img class="exp-empty-img" src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" />
                        <div class="exp-empty-text"><?php echo get_phrase('please_select_a_class_to_view_data'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Champ caché pour le jeton CSRF -->
<input type="hidden" name="<?=csrf_token();?>" value="<?=csrf_hash();?>" />

<script>
    function getPermissionCsrfData() {
        var csrfCookieName = '<?= config('Security')->cookieName; ?>';
        var fallbackCsrfName = '<?= csrf_token(); ?>';
        var cookieHash = null;
        var cookieParts = document.cookie ? document.cookie.split('; ') : [];
        for (var i = 0; i < cookieParts.length; i++) {
            var part = cookieParts[i];
            if (part.indexOf(csrfCookieName + '=') === 0) {
                cookieHash = decodeURIComponent(part.substring(csrfCookieName.length + 1));
                break;
            }
        }

        var csrfInput = document.querySelector('input[name="' + fallbackCsrfName + '"]');
        var csrfName = (csrfInput && csrfInput.name) ? csrfInput.name : (window.csrfName || fallbackCsrfName);
        var csrfHash = cookieHash || (csrfInput ? csrfInput.value : '') || window.csrfHash || '';

        if (!csrfName || !csrfHash) {
            return {};
        }

        var csrfData = {};
        csrfData[csrfName] = csrfHash;
        return csrfData;
    }

    function filter(){
        var class_id = $('#class_id_perm').val();
 
        if(class_id != "" ){
            // Show loading state or opacity
            $('.permission_content').css('opacity', '0.5');
            
            $.ajax({
                url: '<?php echo route('permission/filter/') ?>/'+class_id,
                success: function(response){
                    $('.permission_content').html(response).css('opacity', '1');
                },
                error: function() {
                    $('.permission_content').css('opacity', '1');
                    toastr.error('<?php echo get_phrase('error_loading_data'); ?>');
                }
            });
        }else{
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
        }
    }
    
    function togglePermission(checkbox_id, column_name, teacher_id){
        var $checkbox = $('#'+checkbox_id);
        var previousChecked = !$checkbox.is(':checked'); // onchange already applied
        var value = $checkbox.is(':checked') ? 1 : 0;
        
        var class_id = $('#class_id_perm').val();
        
        // Validation côté client
        if (!class_id || class_id === "") {
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
            $checkbox.prop('checked', previousChecked);
            return false;
        }
        
        if (!teacher_id || teacher_id === "") {
            toastr.error('<?php echo get_phrase('invalid_teacher_id'); ?>');
            $checkbox.prop('checked', previousChecked);
            return false;
        }
        
        var validColumns = ['marks', 'attendance', 'all'];
        if (!validColumns.includes(column_name)) {
            toastr.error('<?php echo get_phrase('invalid_permission_type'); ?>');
            $checkbox.prop('checked', previousChecked);
            return false;
        }
        
        // Désactiver le checkbox pendant la requête
        $checkbox.prop('disabled', true);
        
        var payload = $.extend({
            class_id: class_id,
            teacher_id: teacher_id,
            column_name: column_name,
            value: value
        }, getPermissionCsrfData());
            
        $.ajax({
            type: 'POST',
            url: '<?php echo route('permission/modify_permission/') ?>',
            data: payload,
            dataType: 'json',
            success: function(response){
              
                // Mise à jour du jeton CSRF avec le nouveau jeton renvoyé dans la réponse
                if(response.csrfName && response.csrfHash) {
                    $('input[name="' + response.csrfName + '"]').val(response.csrfHash); // Mise à jour du token CSRF
                }
                
                // Update list content returned by backend
                if(response.html) {
                    $('.permission_content').html(response.html);
                } else if (response.status === true) {
                    // Keep local state in sync when no html payload is returned
                    $checkbox.val(value);
                    toastr.success('<?php echo get_phrase('permission_updated_successfully'); ?>');
                }
                else {
                    $checkbox.prop('checked', previousChecked);
                    toastr.error(response.notification || '<?php echo get_phrase('error_updating_permission'); ?>');
                }
            },
            error: function() {
                toastr.error('<?php echo get_phrase('error_updating_permission'); ?>');
                // Revert checkbox state on error
                $checkbox.prop('checked', previousChecked);
            },
            complete: function() {
                // Réactiver le checkbox après la requête
                $checkbox.prop('disabled', false);
            }
        });
    }
    
    function viewPermissionHistory(teacher_id) {
        var class_id = $('#class_id_perm').val();
        
        if (!class_id || class_id === "") {
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
            return false;
        }
        
        window.location.href = '<?php echo route('permission/history/'); ?>/' + teacher_id + '/' + class_id;
    }
    
    function searchTeachers() {
        var searchTerm = $('#teacher_search').val().toLowerCase();
        
        if (searchTerm === '') {
            $('.exp-list-item').show();
            return;
        }
        
        $('.exp-list-item').each(function() {
            var teacherName = $(this).find('.exp-teacher-name').text().toLowerCase();
            var teacherEmail = $(this).find('.small.text-muted').text().toLowerCase();
            
            if (teacherName.includes(searchTerm) || teacherEmail.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    function exportPermissions() {
        var class_id = $('#class_id_perm').val();
        
        if (!class_id || class_id === "") {
            toastr.error('<?php echo get_phrase('please_select_a_class'); ?>');
            return false;
        }
        
        var rows = [];
        var headers = ['Teacher Name', 'Teacher Email', 'Marks Permission', 'Attendance Permission'];
        rows.push(headers.join(','));
        
        $('.exp-list-item:visible').each(function() {
            var teacherName = $(this).find('.exp-teacher-name').text().replace(/,/g, '');
            var teacherEmail = $(this).find('.small.text-muted').text().replace(/,/g, '');
            var marksPermission = $(this).find('input[name="marks_permission"]').prop('checked') ? 'Granted' : 'Denied';
            var attendancePermission = $(this).find('input[name="attendance_permission"]').prop('checked') ? 'Granted' : 'Denied';
            
            var row = [teacherName, teacherEmail, marksPermission, attendancePermission];
            rows.push(row.join(','));
        });
        
        var csvContent = rows.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'permissions_class_' + class_id + '_export.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        toastr.success('<?php echo get_phrase('permissions_exported_successfully'); ?>');
    }
    
    // Charger automatiquement la liste si un class_id est passé
    <?php if(isset($class_id) && $class_id > 0): ?>
    $(document).ready(function() {
        filter();
    });
    <?php endif; ?>
</script>
