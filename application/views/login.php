<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $this->settings_model->get_favicon(); ?>">

    <!-- App css -->
    <link href="<?php echo base_url(); ?>assets/backend/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/backend/css/app.min.css" rel="stylesheet" type="text/css" />
    <!--Notify for ajax-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/backend/js/jquery-3.6.0.min.js"></script>
    
    <!-- Modern Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for modern icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?php echo base_url(); ?>assets/backend/images/bg-auth.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .login-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
            min-height: 600px;
            animation: slideIn 0.6s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-form-section {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-welcome-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .logo {
            margin-bottom: 40px;
            text-align: left;
        }
        
        .logo img {
            height: 45px;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }
        
        .form-title {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .form-subtitle {
            font-size: 16px;
            color: #718096;
            margin-bottom: 40px;
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        
        .form-group.floating-label {
            margin-bottom: 32px;
        }
        
        .floating-label .form-label {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            padding: 0 8px;
            font-size: 16px;
            color: #a0aec0;
            pointer-events: none;
            transition: all 0.3s ease;
            z-index: 1;
        }
        
        .floating-label .form-input {
            padding: 20px 20px 12px 20px;
        }
        
        .floating-label .form-input:focus ~ .form-label,
        .floating-label .form-input:not(:placeholder-shown) ~ .form-label {
            top: 0;
            font-size: 12px;
            color: #667eea;
            font-weight: 500;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 8px;
            transition: color 0.3s ease;
        }
        
        .form-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            background: #f8fafc;
            color: #2d3748;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }
        
        .form-input::placeholder {
            color: #a0aec0;
            font-weight: 400;
        }
        
        .form-input.is-invalid {
            border-color: #f56565;
            background: #fff5f5;
        }
        
        .form-input.is-invalid:focus {
            border-color: #f56565;
            box-shadow: 0 0 0 4px rgba(245, 101, 101, 0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a0aec0;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: #667eea;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }
        
        .remember-me label {
            font-size: 14px;
            color: #4a5568;
            cursor: pointer;
        }
        
        .forgot-password {
            font-size: 14px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: #5a67d8;
            text-decoration: underline;
        }
        
        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.3);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .submit-btn:hover::before {
            left: 100%;
        }
        
        .error-message {
            color: #f56565;
            font-size: 14px;
            margin-top: 8px;
            font-weight: 500;
        }
        
        .welcome-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }
        
        .welcome-text {
            font-size: 18px;
            line-height: 1.6;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            max-width: 300px;
        }
        
        .welcome-icon {
            font-size: 64px;
            margin-bottom: 24px;
            opacity: 0.8;
            position: relative;
            z-index: 1;
        }
        
        .form-toggle {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #718096;
        }
        
        .form-toggle a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .form-toggle a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .submit-btn.loading .loading-spinner {
            display: inline-block;
        }
        
        .submit-btn.loading .btn-text {
            display: none;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                max-width: 400px;
                margin: 20px;
            }
            
            .login-form-section {
                padding: 40px 30px;
            }
            
            .login-welcome-section {
                padding: 30px;
                min-height: 200px;
            }
            
            .welcome-title {
                font-size: 28px;
            }
            
            .welcome-text {
                font-size: 16px;
            }
            
            .form-title {
                font-size: 28px;
            }
        }
        
        @media (max-width: 480px) {
            .login-form-section {
                padding: 30px 20px;
            }
            
            .form-title {
                font-size: 24px;
            }
            
            .form-input {
                padding: 14px 16px;
                font-size: 16px;
            }
            
            .submit-btn {
                padding: 14px;
                font-size: 15px;
            }
        }
        
        /* Toast notifications styling */
        .toast {
            border-radius: 12px !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 14px !important;
        }
        
        .toast-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%) !important;
        }
        
        .toast-error {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%) !important;
        }
        
        .toast-info {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%) !important;
        }
    </style>
</head>

<body>
<?php $this->load->view('frontend/alert_view'); ?>
    <div class="login-container">
        <div class="login-card">
            <!-- Login Form Section -->
            <div class="login-form-section">
                <div class="logo">
                    <a href="<?php echo site_url(); ?>">
                        <?php $logo_dark = base_url('uploads/images/decloedt/logo/logo_mail.png'); ?>
                        <span><img src="<?php echo $this->settings_model->get_logo_dark(); ?>" alt="Logo"></span>
                    </a>
                </div>
                
                <div id="loginFormSection">
                    <h1 class="form-title"><?php echo get_phrase('sign_in'); ?></h1>
                    <p class="form-subtitle"><?php echo get_phrase('enter_your_email_address_and_password_to_access_account'); ?>.</p>

                    <form action="<?php echo site_url('login/validate_login'); ?>" method="post" id="loginForm">
                        <!-- CSRF Token -->
                        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

                        <div class="form-group floating-label">
                            <input class="form-input" type="email" name="email" id="emailaddress" required placeholder=" ">
                            <label for="emailaddress" class="form-label"><?php echo get_phrase('email'); ?></label>
                        </div>
                        
                        <div class="form-group floating-label">
                            <div style="position: relative;">
                                <input class="form-input" type="password" name="password" required id="password" placeholder=" " style="padding-right: 60px;">
                                <label for="password" class="form-label"><?php echo get_phrase('password'); ?></label>
                                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="error-message" id="error_message"></span>
                        </div>
                        
                        <div class="form-options">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember"><?php echo get_phrase('remember_me'); ?></label>
                            </div>
                            <a href="javascript: void(0);" class="forgot-password" onclick="forgotPass();">
                                <?php echo get_phrase('forgot_your_password'); ?>?
                            </a>
                        </div>
                        
                        <button class="submit-btn" type="submit">
                            <span class="loading-spinner"></span>
                            <span class="btn-text">
                                <i class="fas fa-sign-in-alt"></i> <?php echo get_phrase('log_in'); ?>
                            </span>
                        </button>
                    </form>
                </div>

                <div id="forgotFormSection" style="display: none;">
                    <h1 class="form-title"><?php echo get_phrase('reset_password'); ?></h1>
                    <p class="form-subtitle"><?php echo get_phrase('enter_your_email_to_receive_reset_link'); ?>.</p>

                    <form action="<?php echo site_url('login/send_reset_link'); ?>" method="post" id="forgotForm">
                        <!-- CSRF Token -->
                        <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
                        
                        <div class="form-group floating-label">
                            <input class="form-input" type="email" name="email" required id="forgotEmail" placeholder=" ">
                            <label for="forgotEmail" class="form-label"><?php echo get_phrase('email'); ?></label>
                        </div>
                        
                        <button class="submit-btn" type="submit">
                            <span class="loading-spinner"></span>
                            <span class="btn-text">
                                <i class="fas fa-paper-plane"></i> <?php echo get_phrase('sent_password_reset_link'); ?>
                            </span>
                        </button>
                        
                        <div class="form-toggle">
                            <a href="javascript: void(0);" onclick="backToLogin();">
                                <i class="fas fa-arrow-left"></i> <?php echo get_phrase('back_to_login'); ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="login-welcome-section"> 
                 <div class="welcome-icon"> 
                     <!-- <i class="fas fa-graduation-cap"></i> --> 
                     <span><img src="<?php echo $this->settings_model->get_logo_light(); ?>" alt="Logo" style="max-height: 80px; width: auto; filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.2));"></span> 
                 </div> 
                 <h2 class="welcome-title"><?php echo get_phrase('welcome_back'); ?></h2> 
                <p class="welcome-text"><?php echo get_phrase('login_to_access_your_dashboard_and_manage_your_school_efficiently'); ?>.</p> 
             </div>
        </div>
    </div>

<!--Notify for ajax-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<!-- App js (loaded after our custom scripts to prevent conflicts) -->
<script src="<?php echo base_url(); ?>assets/backend/js/app.min.js" defer></script>

<!-- Configure toastr for modern look -->
<script>
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Toggle password visibility
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Modern form submission with loading states
$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    
    const $submitBtn = $(this).find('button[type="submit"]');
    const $form = $(this);
    
    // Add loading state
    $submitBtn.addClass('loading').prop('disabled', true);
    $('#error_message').text('');
    $('#password').removeClass('is-invalid');
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                // Show success message before redirect
                // toastr.success('<?php // echo get_phrase("login_successful"); ?>!', '', {
                //     closeButton: false,
                //     progressBar: true,
                //     timeOut: 1500,
                //     onHiddenClick: function() {
                //         window.location.href = response.redirect;
                //     }
                // });
                
                // Redirect after short delay
                setTimeout(function() {
                    window.location.href = response.redirect;
                }, 1500);
                
            } else {
                // Show error toast
                // toastr.error(response.message, '<?php // echo get_phrase("oh_snap"); ?>', {
                //     closeButton: true,
                //     progressBar: false,
                //     timeOut: 5000
                // });

                // Show error message
                $('#error_message').text(response.message);
                $('#password').addClass('is-invalid').val('');
                
                // Update CSRF token if provided
                if(response.csrf_token_name && response.csrf_hash) {
                    $('input[name="' + response.csrf_token_name + '"]').val(response.csrf_hash);
                }
            }
        },
        error: function(xhr, status, error) {
            $('#error_message').text('<?php echo get_phrase("invalid_your_email_or_password"); ?>');
            $('#password').addClass('is-invalid').val('');
            
            toastr.error('<?php echo get_phrase("login_failed"); ?>', '<?php echo get_phrase("oh_snap"); ?>', {
                closeButton: true,
                progressBar: false,
                timeOut: 5000
            });

            // Try to get new CSRF token from response
            try {
                var errorResponse = JSON.parse(xhr.responseText);
                if(errorResponse.csrf_token_name && errorResponse.csrf_hash) {
                    $('input[name="' + errorResponse.csrf_token_name + '"]').val(errorResponse.csrf_hash);
                }
            } catch(e) {
                console.log('Could not parse CSRF from error response');
            }
        },
        complete: function() {
            // Remove loading state
            $submitBtn.removeClass('loading').prop('disabled', false);
        }
    }); 
});

// Forgot password form submission
$('#forgotForm').on('submit', function(e) {
    e.preventDefault();
    
    const $submitBtn = $(this).find('button[type="submit"]');
    
    // Add loading state
    $submitBtn.addClass('loading').prop('disabled', true);
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                toastr.success(response.message, '<?php echo get_phrase("success"); ?>', {
                    closeButton: true,
                    progressBar: false,
                    timeOut: 5000
                });
                
                // Reset form and go back to login
                $('#forgotForm')[0].reset();
                setTimeout(function() {
                    backToLogin();
                }, 2000);
                
            } else {
                toastr.error(response.message, '<?php echo get_phrase("oh_snap"); ?>', {
                    closeButton: true,
                    progressBar: false,
                    timeOut: 5000
                });
            }
        },
        error: function() {
            toastr.error('<?php echo get_phrase("failed_to_send_reset_link"); ?>', '<?php echo get_phrase("oh_snap"); ?>', {
                closeButton: true,
                progressBar: false,
                timeOut: 5000
            });
        },
        complete: function() {
            // Remove loading state
            $submitBtn.removeClass('loading').prop('disabled', false);
        }
    });
});

// Clear error on password input
$('#password').on('input', function() {
    $('#error_message').text('');
    $(this).removeClass('is-invalid');
});

// Form toggle functions with animations
function forgotPass(){
    $('#loginFormSection').fadeOut(300, function() {
        $('#forgotFormSection').fadeIn(300);
    });
}

function backToLogin(){
    $('#forgotFormSection').fadeOut(300, function() {
        $('#loginFormSection').fadeIn(300);
    });
}

// Add input animations
$('.form-input').on('focus', function() {
    $(this).parent().find('.form-label').css('color', '#667eea');
});

$('.form-input').on('blur', function() {
    $(this).parent().find('.form-label').css('color', '#4a5568');
});

// Add enter key support for forms
$('.form-input').on('keypress', function(e) {
    if (e.which === 13) {
        $(this).closest('form').submit();
    }
});
</script>

<?php if ($this->session->flashdata('info_message') != ""):?>
    <script type="text/javascript">
    $.NotificationApp.send(<?php echo js_phrase('success'); ?>, '<?php echo $this->session->flashdata("info_message");?>' ,"top-right","rgba(0,0,0,0.2)","info");
</script>
<?php endif;?>

<?php if ($this->session->flashdata('error_message') != ""):?>
    <script type="text/javascript">
    $.NotificationApp.send(<?php echo js_phrase('oh_snap'); ?>, '<?php echo $this->session->flashdata("error_message");?>' ,"top-right","rgba(0,0,0,0.2)","error");
</script>
<?php endif;?>

<?php if ($this->session->flashdata('flash_message') != ""):?>
    <script type="text/javascript">
    $.NotificationApp.send(<?php echo js_phrase('congratulations'); ?>, '<?php echo $this->session->flashdata("flash_message");?>' ,"top-right","rgba(0,0,0,0.2)","success");
</script>
<?php endif;?>
</body>

</html>