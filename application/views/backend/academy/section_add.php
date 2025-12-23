<style>
.section-modal-form {
    padding: 8px 0;
}
.section-modal-form .form-group {
    margin-bottom: 20px;
}
.section-modal-form label {
    display: block;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 8px;
}
.section-modal-form label .required {
    color: #ef4444;
}
.section-modal-form .form-control {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
    color: #334155;
    transition: all 0.15s ease;
    background: #ffffff;
}
.section-modal-form .form-control:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}
.section-modal-form .form-control::placeholder {
    color: #94a3b8;
}
.section-modal-form .text-muted {
    display: block;
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 6px;
}
.section-modal-form .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid #e8ecf1;
}
.section-modal-form .btn-submit {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border: none;
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}
.section-modal-form .btn-submit:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
}
.section-modal-form .btn-submit i {
    font-size: 0.9rem;
}
.section-modal-form .input-icon-wrapper {
    position: relative;
}
.section-modal-form .input-icon-wrapper i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1rem;
}
.section-modal-form .input-icon-wrapper .form-control {
    padding-left: 42px;
}
</style>

<form action="<?php echo site_url('addons/courses/course_sections/'.$param1.'/add'); ?>" method="post" class="section-modal-form">

    <!-- Champ caché pour le jeton CSRF -->
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
    
    <div class="form-group">
        <label for="title">
            <i class="fas fa-folder" style="margin-right: 6px; color: #6366f1;"></i>
            <?php echo get_phrase('title'); ?><span class="required"> * </span>
        </label>
        <div class="input-icon-wrapper">
            <i class="fas fa-heading"></i>
            <input class="form-control" type="text" name="title" id="title" placeholder="<?php echo get_phrase('enter_section_title'); ?>" required autofocus>
        </div>
        <small class="text-muted"><?php echo get_phrase('provide_a_section_name'); ?></small>
    </div>
    
    <div class="form-actions">
        <button class="btn-submit" type="submit" name="button">
            <i class="fas fa-plus"></i>
            <?php echo get_phrase('add_section'); ?>
        </button>
    </div>
</form>
