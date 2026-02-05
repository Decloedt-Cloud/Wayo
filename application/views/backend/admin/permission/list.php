    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/responsive.css">
    
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
        overflow: hidden;
        background: white;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        min-height: 400px;
    }

    .exp-list-header {
        display: grid;
        grid-template-columns: 2fr 0.8fr 0.8fr 0.8fr 0.8fr;
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

    .exp-list-item {
        display: grid;
        grid-template-columns: 2fr 0.8fr 0.8fr 0.8fr 0.8fr;
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
                                    $classes = $this->db->get_where('classes', array('school_id' => school_id()))->result_array();
                                    $school_id = school_id();
                                    foreach($classes as $class){
                                        $this->db->where('class_id', $class['id']); 
                                        $this->db->where('school_id', $school_id);
                                        $total_student = $this->db->get('enrols');
                                    ?>
                                        <option value="<?php echo $class['id']; ?>">
                                            <?php echo $class['name']; ?>
                                            <?php echo "(".$total_student->num_rows().")"; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <button class="exp-filter-btn" onclick="filter()">
                                <i class="mdi mdi-filter"></i> <?php echo get_phrase('filter'); ?>
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

<script>
    function filter(){
        var class_id = $('#class_id_perm').val();
 
        if(class_id != "" ){
            // Show loading state or opacity
            $('.permission_content').css('opacity', '0.5');
            
            $.ajax({
                url: '<?php echo route('permission/filter/') ?>'+class_id,
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

        var value = $('#'+checkbox_id).val();
        if(value == 1){
            value = 0;
        }else{
            value = 1;
        }
           
        // Récupérer le nom et la valeur du jeton CSRF depuis l'input caché
        var csrfName = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').attr('name');
        var csrfHash = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
        
        var class_id = $('#class_id_perm').val();
            
        $.ajax({
            type: 'POST',
            url: '<?php echo route('permission/modify_permission/') ?>',
            data: {class_id : class_id, teacher_id : teacher_id, column_name : column_name,  value : value , [csrfName]: csrfHash},
            dataType: 'json',
            success: function(response){
              
                // Mise à jour du jeton CSRF avec le nouveau jeton renvoyé dans la réponse
                if(response.csrfName && response.csrfHash) {
                    $('input[name="' + response.csrfName + '"]').val(response.csrfHash); // Mise à jour du token CSRF
                }
                
                // Update checkbox value
                $('#'+checkbox_id).val(value);
                
                toastr.success('<?php echo get_phrase('permission_updated_successfully.'); ?>');
            },
            error: function() {
                toastr.error('<?php echo get_phrase('error_updating_permission'); ?>');
                // Revert checkbox state on error
                $('#'+checkbox_id).prop('checked', !$('#'+checkbox_id).prop('checked'));
            }
        });
    }
</script>
