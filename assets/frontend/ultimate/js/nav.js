$(document).ready(function () {
    const $userButton = $(".user-section");
    const $userDropdown = $(".user-dropdown");
    const $loginToggle = $(".login-toggle");
    const $loginDropdown = $(".login-dropdown");
    const $registerToggle = $(".register-link");
    const $registerDropdown = $(".register-dropdown");
    const $forgetToggle = $(".forget-link");
    const $forgetDropdown = $(".forget-dropdown");
    // Configurer Toastr
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 1500,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
        onHidden: function() {} // Sera remplacé dynamiquement pour certaines actions
    };

    // User section logic
    if ($userButton.length) {
        $userButton.on("click", function () {
            if ($userDropdown.hasClass("display-none")) {
                $userDropdown.removeClass("display-none");
                setTimeout(() => $userDropdown.css('opacity', '1'), 10);
            } else {
                $userDropdown.css('opacity', '0');
                setTimeout(() => $userDropdown.addClass("display-none"), 100);
            }
        });
    }

    // Login toggle logic
    if ($loginToggle.length) {
        $loginToggle.on("click", function () {
            if (!$registerDropdown.hasClass("display-none")) {
                $registerDropdown.css('opacity', '0');
                setTimeout(() => $registerDropdown.addClass("display-none"), 100);
            }
            if ($loginDropdown.hasClass("display-none")) {
                $loginDropdown.removeClass("display-none").show();
                setTimeout(() => $loginDropdown.css('opacity', '1'), 10);
                $(this).attr('aria-expanded', 'true');
            } else {
                $loginDropdown.css('opacity', '0');
                setTimeout(() => {
                    $loginDropdown.addClass("display-none").hide();
                }, 100);
                $(this).attr('aria-expanded', 'false');
            }
        });
    }

    // Register toggle logic
    if ($registerToggle.length) {
        $registerToggle.on("click", function () {
            if (!$loginDropdown.hasClass("display-none")) {
                $loginDropdown.css('opacity', '0');
                setTimeout(() => $loginDropdown.addClass("display-none"), 100);
                $loginToggle.attr('aria-expanded', 'false');
            }
            if (!$forgetDropdown.hasClass("display-none")) {
                $forgetDropdown.css('opacity', '0');
                setTimeout(() => $forgetDropdown.addClass("display-none"), 100);
            }
            if ($registerDropdown.hasClass("display-none")) {
                $registerDropdown.removeClass("display-none");
                setTimeout(() => {
                    $registerDropdown.css('opacity', '1');
                    $('.learner-form-container').removeClass('display-none');
                    setTimeout(() => $('.learner-form-container').css('opacity', '1'), 10);
                    updateStep(1, 'learner-form');
                }, 10);
            }
        });
    }

    // Forget toggle logic
    if ($forgetToggle.length) {
        $forgetToggle.on("click", function () {
            if ($forgetDropdown.hasClass("display-none")) {
                const $loginForm = $loginDropdown.find(".login-form");
                const $registerPhrase = $loginDropdown.find(".register-phrase");
                const $forgetPhrase = $loginDropdown.find(".forget-phrase");
                if ($loginForm.length) $loginForm.addClass("display-none");
                if ($registerPhrase.length) $registerPhrase.addClass("display-none");
                if ($forgetPhrase.length) $forgetPhrase.addClass("display-none");
                $forgetDropdown.removeClass("display-none");
                setTimeout(() => $forgetDropdown.css('opacity', '1'), 10);
            }
        });
    }

    // Retour au login depuis forget-dropdown
    const $loginForgetLink = $(".loginforge-link");
    if ($loginForgetLink.length) {
        $loginForgetLink.on("click", function () {
            if (!$forgetDropdown.hasClass("display-none")) {
                $forgetDropdown.css('opacity', '0');
                setTimeout(() => $forgetDropdown.addClass("display-none"), 100);
                const $loginForm = $loginDropdown.find(".login-form");
                const $registerPhrase = $loginDropdown.find(".register-phrase");
                const $forgetPhrase = $loginDropdown.find(".forget-phrase");
                if ($loginForm.length) $loginForm.removeClass("display-none");
                if ($registerPhrase.length) $registerPhrase.removeClass("display-none");
                if ($forgetPhrase.length) $forgetPhrase.removeClass("display-none");
            }
        });
    }

    // Exit button for login-dropdown
    if ($loginDropdown.length) {
        const $loginExitSvg = $(".login-exit-svg");
        $loginExitSvg.on("click", function () {
            $loginDropdown.css('opacity', '0');
            $loginDropdown.removeClass("has-error"); // Reset error class
            const errorDiv = document.getElementById("loginError");
            if (errorDiv) {
                errorDiv.textContent = ""; // Clear error message
                errorDiv.classList.add("display-none"); // Hide error message
            }
            setTimeout(() => $loginDropdown.addClass("display-none"), 100);
            $loginToggle.attr('aria-expanded', 'false');
            if (!$forgetDropdown.hasClass("display-none")) {
                $forgetDropdown.css('opacity', '0');
                setTimeout(() => $forgetDropdown.addClass("display-none"), 100);
                const $loginForm = $loginDropdown.find(".login-form");
                const $registerPhrase = $loginDropdown.find(".register-phrase");
                const $forgetPhrase = $loginDropdown.find(".forget-phrase");
                if ($loginForm.length) $loginForm.removeClass("display-none");
                if ($registerPhrase.length) $registerPhrase.removeClass("display-none");
                if ($forgetPhrase.length) $forgetPhrase.removeClass("display-none");
            }
        });
    }

    // Step navigation function
    function updateStep(step, formId) {
        const $form = $(`#${formId}`);
        const $formContainer = $form.find('.form-steps-container');
        
        // Hide all steps
        $formContainer.find('.form-step').css('opacity', '0').addClass('display-none');
        
        // Show the selected step
        const $newStep = $formContainer.find(`.form-step[data-step="${step}"]`);
        $newStep.removeClass('display-none');
        setTimeout(() => {
            $newStep.css('opacity', '1');
        }, 10);
        
        // Update active step indicator
        $form.find('.step').removeClass('active');
        $form.find(`.step[data-step="${step}"]`).addClass('active');
        $form.data('currentStep', step);
    }

    // Initialize forms with step tracking
    $('#learner-form').data('currentStep', 1);

    // Next button logic
    $('.next-btn').on('click', function (e) {
        e.preventDefault();
        const $currentStep = $(this).closest('.form-step');
        const $form = $currentStep.closest('form');
        const formId = $form.attr('id');
        const currentStep = $form.data('currentStep') || 1;
        const inputs = $currentStep.find('input[required], select[required], textarea[required]');
        let valid = true;

        // Validate required fields
        inputs.each(function () {
            if (!this.checkValidity()) {
                valid = false;
                $(this).addClass('invalid');
            } else {
                $(this).removeClass('invalid');
            }
        });

        const maxSteps = 2;

        // If form inputs are valid, proceed with additional checks
        if (valid && currentStep < maxSteps) {
            // Learner form: Check email in Step 1
            if (formId === 'learner-form' && currentStep === 1) {
                const email = $form.find('input[name="student_email"]').val();
                checkEmailExists(email).then((response) => {
                    const $emailInput = $form.find('input[name="student_email"]');
                    const $errorSpan = $emailInput.next('.email-error');
                    
                    // Remove any existing error message
                    if ($errorSpan.length) $errorSpan.remove();
                    
                    if (response.exists) {
                        // Display error message below email field
                        $emailInput.after('<span class="email-error text-danger">' + window.emailAlreadyInUse + '</span>');
                        $emailInput.addClass('invalid');
                    } else {
                        // Proceed to next step
                        updateStep(currentStep + 1, formId);
                    }
                    
                    // Adjust dropdown height
                    adjustLoginDropdownHeight($form);
                });
            }
            
            // Proceed to next step for other cases
            else {
                updateStep(currentStep + 1, formId);
                // Adjust dropdown height (in case errors were removed)
                adjustLoginDropdownHeight($form);
            }
        }
    });

    // Back button logic
     $('.back-btn').on('click', function (e) {
        e.preventDefault();
        const $form = $(this).closest('form');
        const formId = $form.attr('id');
        const currentStep = $form.data('currentStep') || 1;

        if (currentStep > 1) {
            updateStep(currentStep - 1, formId);
        } else {
            $registerDropdown.css('opacity', '0');
            setTimeout(() => {
                $registerDropdown.addClass("display-none");
                $('.learner-form-container').addClass('display-none');
                $('.mentor-form-container').addClass('display-none');
                $loginDropdown.removeClass("display-none");
                setTimeout(() => $loginDropdown.css('opacity', '1'), 10);
            }, 100);
        }
    });

// Gestion du formulaire learner
const learnerForm = document.getElementById("learner-form");
if (learnerForm) {
    const registerBtn = document.querySelector('#learner-form .register-btn');
    if (registerBtn) {
        registerBtn.addEventListener('click', function (event) {
            event.preventDefault();
            if (learnerForm.checkValidity()) {
                const password = document.getElementById('password-student')?.value;
                const repeatPassword = document.getElementById('repeat-password-student')?.value;
                if (password !== repeatPassword) {
                    const errorMessage = document.getElementById('errorMessage');
                    if (errorMessage) errorMessage.classList.remove('display-none');
                    return;
                } else {
                    const errorMessage = document.getElementById('errorMessage');
                    if (errorMessage) errorMessage.classList.add('display-none');
                }

                const email = document.querySelector('#learner-form input[name="student_email"]')?.value;
                if (!email) {
                    toastr.error('Adresse e-mail manquante.', 'Erreur', { timeOut: 3000 });
                    return;
                }

                // Show loading spinner and disable button
                const $spinner = $('.register-dropdown .loading-spinner');
                if ($spinner.length) $spinner.removeClass('display-none');
                registerBtn.disabled = true;

                // Fetch fresh CSRF token
                $.ajax({
                    type: "GET",
                    url: window.baseUrl + 'login/get_csrf_token',
                    dataType: 'json',
                    success: function (response) {
                        if (!response.csrfName || !response.csrfHash) {
                            if ($spinner.length) $spinner.addClass('display-none');
                            registerBtn.disabled = false;
                            registerBtn.innerHTML = 'S\'inscrire';
                            toastr.error('Jeton CSRF invalide.', 'Erreur', { timeOut: 3000 });
                            return;
                        }

                        // Update CSRF token in form
                        $('input[name="' + response.csrfName + '"]').val(response.csrfHash);

                        // Submit form via AJAX
                        $.ajax({
                            type: "POST",
                            url: learnerForm.action,
                            data: new FormData(learnerForm),
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function (response) {
                                if (response.status) {
                                    toastr.success(response.message || 'Votre inscription a été effectuée avec succès.', 'Inscription réussie !', {
                                        timeOut: 2000,
                                        onHidden: function () {
                                            $.ajax({
                                                type: "POST",
                                                url: window.baseUrl + 'login/set_student_just_registered',
                                                data: { just_registered: 1 },
                                                success: function() {
                                                    $.ajax({
                                                        type: "POST",
                                                        url: window.baseUrl + 'login/validate_login_frontend',
                                                        data: {
                                                            login_email: email,
                                                            login_password: password,
                                                            just_registered: 1,
                                                            [response.csrf?.csrfName || response.csrf_token_name]: response.csrf?.csrfHash || response.csrf_hash
                                                        },
                                                        dataType: 'json',
                                                        success: function (loginResponse) {
                                                            // Update CSRF token
                                                            let newCsrfName, newCsrfHash;
                                                            if (loginResponse.csrf && loginResponse.csrf.csrfName) {
                                                                newCsrfName = loginResponse.csrf.csrfName;
                                                                newCsrfHash = loginResponse.csrf.csrfHash;
                                                            } else if (loginResponse.csrf_token_name) {
                                                                newCsrfName = loginResponse.csrf_token_name;
                                                                newCsrfHash = loginResponse.csrf_hash;
                                                            }
                                                            if (newCsrfName && newCsrfHash) {
                                                                $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
                                                            }

                                                            if ($spinner.length) $spinner.addClass('display-none');
                                                            registerBtn.disabled = false;
                                                            registerBtn.innerHTML = 'S\'inscrire';

                                                            if (loginResponse.status) {
                                                                learnerForm.reset();
                                                                $('.register-dropdown').css('opacity', '0');
                                                                setTimeout(() => {
                                                                    $('.register-dropdown').addClass("display-none");
                                                                    window.location.href = window.baseUrl + 'home/communities';
                                                                }, 100);
                                                            } else {
                                                                toastr.error(loginResponse.message || 'Échec de la connexion automatique.', 'Erreur', { timeOut: 3000 });
                                                                window.location.href = window.baseUrl + 'login';
                                                            }
                                                        },
                                                        error: function (error) {
                                                            console.error("Error during auto-login:", error);
                                                            if ($spinner.length) $spinner.addClass('display-none');
                                                            registerBtn.disabled = false;
                                                            registerBtn.innerHTML = 'S\'inscrire';
                                                            window.location.href = window.baseUrl + 'login';
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    });
                                } else {
                                    if ($spinner.length) $spinner.addClass('display-none');
                                    registerBtn.disabled = false;
                                    registerBtn.innerHTML = 'S\'inscrire';
                                    toastr.error(response.message || 'Une erreur s\'est produite lors de l\'inscription.', 'Erreur', { timeOut: 3000 });
                                }

                                // Update CSRF token
                                const newCsrfName = response.csrf?.csrfName;
                                const newCsrfHash = response.csrf?.csrfHash;
                                if (newCsrfName && newCsrfHash) {
                                    $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
                                }
                            },
                            error: function (error) {
                                console.error("Error:", error);
                                if ($spinner.length) $spinner.addClass('display-none');
                                registerBtn.disabled = false;
                                registerBtn.innerHTML = 'S\'inscrire';
                                toastr.error('Une erreur serveur s\'est produite. Vérifiez reCAPTCHA ou contactez l\'administrateur.', 'Erreur', { timeOut: 3000 });
                            }
                        });
                    },
                    error: function (error) {
                        console.error("Error fetching CSRF token:", error);
                        if ($spinner.length) $spinner.addClass('display-none');
                        registerBtn.disabled = false;
                        registerBtn.innerHTML = 'S\'inscrire';
                        toastr.error('Impossible de récupérer le jeton CSRF.', 'Erreur', { timeOut: 3000 });
                    }
                });
            } else {
                learnerForm.reportValidity();
            }
        });
    }
}


    if ($registerDropdown.length) {
        const $registerExitSvg = $(".register-exit-svg");
        $registerExitSvg.on("click", function () {
            $registerDropdown.css('opacity', '0');
            setTimeout(() => $registerDropdown.addClass("display-none"), 100);
        });
    }

    // Navbar toggle
    const navbarInner = document.getElementById("navBar");
    const navbartoggle = document.getElementById("navToggle");
    if (navbartoggle) {
        navbartoggle.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            navbarInner.classList.toggle("collapse-nav");
            navbarInner.classList.toggle("show-nav");
        });
    }

    // Login link from register
    const $loginLink = $(".login-link");
    if ($loginLink.length) {
        $loginLink.on("click", function () {
            if ($loginDropdown.hasClass("display-none")) {
                $registerDropdown.css('opacity', '0');
                setTimeout(() => $registerDropdown.addClass("display-none"), 100);
                $loginDropdown.removeClass("display-none");
                setTimeout(() => $loginDropdown.css('opacity', '1'), 10);
            }
        });
    }

    // Input validation
    const inputs = document.querySelectorAll('.information');
    const passwordInputs = document.querySelectorAll('.password');
    const selects = document.getElementsByTagName('select');

    inputs.forEach(input => {
        input.addEventListener('input', function () {
            if (this.value !== "") {
                this.classList.remove("invalid");
            }
        });
    });

    passwordInputs.forEach(input => {
        input.addEventListener('input', function () {
            if (this.value !== "" && checkPassword()) {
                this.classList.remove("invalid");
            }
        });
    });

    Array.from(selects).forEach(select => {
        select.addEventListener('change', function () {
            if (this.value !== "") {
                this.classList.remove("invalid");
            } else {
                this.classList.add("invalid");
            }
        });
    });

    // Login submit button logic
    const loginSubmitButton = document.getElementById("loginSubmit");
    if (loginSubmitButton) {
        loginSubmitButton.addEventListener("click", function (event) {
            if (!loginSubmit()) {
                event.preventDefault();
            }
        });
    }
});

function check_email(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function checkPassword() {
    // Placeholder for password validation logic, if needed
    return true;
}

function loginSubmit() {
    const emailInput = document.getElementById("loginEmail");
    const passwordInput = document.getElementById("loginPassword");
    const errorDiv = document.getElementById("loginError");
    const loginDropdown = document.querySelector(".login-inline");
    const submitBtn = document.getElementById("loginSubmit");

    if (!emailInput || !passwordInput || !errorDiv || !loginDropdown) {
        console.warn("Login form elements not found");
        return false;
    }

    if (submitBtn && submitBtn.disabled) {
        return false;
    }

    const email = emailInput.value;
    const password = passwordInput.value;

    errorDiv.classList.add("display-none");
    errorDiv.textContent = "";
    loginDropdown.classList.remove("has-error");

    if (!check_email(email)) {
        errorDiv.textContent = 'Invalid email format';
        errorDiv.classList.remove("display-none");
        loginDropdown.classList.add("has-error");
        return false;
    }

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = submitBtn.getAttribute('data-loading-text') || 'Processing...';
    }

    validateCredentials(email, password).then((response) => {
        if (response.status) {
            document.getElementById("login-form").submit();
        } else {
            errorDiv.textContent = response.message || 'Invalid email or password';
            errorDiv.classList.remove("display-none");
            loginDropdown.classList.add("has-error");
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.getAttribute('data-original-text') || 'Log in';
            }
        }
    }).catch(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = submitBtn.getAttribute('data-original-text') || 'Log in';
        }
    });

    return false;
}

function validateCredentials(email, password) {
    return new Promise((resolve) => {
        const csrfName = $('input[name="' + window.csrfTokenName + '"]').attr('name');
        const csrfHash = $('input[name="' + window.csrfTokenName + '"]').val();

        $.ajax({
            type: "POST",
            url: window.loginValidateUrl,
            data: { email: email, password: password, [csrfName]: csrfHash },
            dataType: 'json',
            success: function (response) {
                let newCsrfName, newCsrfHash;
                if (response.csrf && response.csrf.csrfName) {
                    newCsrfName = response.csrf.csrfName;
                    newCsrfHash = response.csrf.csrfHash;
                } else if (response.csrf_token_name) {
                    newCsrfName = response.csrf_token_name;
                    newCsrfHash = response.csrf_hash;
                }
                if (newCsrfName && newCsrfHash) {
                    $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
                }

                resolve({
                    status: response.status,
                    message: response.message || 'Invalid email or password'
                });
            },
            error: function (error) {
                console.error("Error:", error);
                resolve({ status: false, message: 'Server error occurred' });
            }
        });
    });
}

// Function to adjust login-dropdown height based on error messages
function adjustLoginDropdownHeight($form) {
    const $loginDropdown = $form.closest('.login-dropdown');
    const $errorMessage = $form.find('.email-error, .school-error');
    
    if ($errorMessage.length > 0) {
        // Error message is present, increase height
        $loginDropdown.css('height', '310px');
    } else {
        // No error message, revert to default height
        $loginDropdown.css('height', '270px');
    }
}

function checkEmailExists(email) {
    return new Promise((resolve) => {
        const csrfName = $('input[name="' + window.csrfTokenName + '"]').attr('name');
        const csrfHash = $('input[name="' + window.csrfTokenName + '"]').val();

        $.ajax({
            type: "POST",
            url: window.checkEmailExistsUrl,
            data: { email: email, [csrfName]: csrfHash },
            dataType: 'json',
            success: function (response) {
                let newCsrfName, newCsrfHash;
                if (response.csrf && response.csrf.csrfName) {
                    newCsrfName = response.csrf.csrfName;
                    newCsrfHash = response.csrf.csrfHash;
                } else if (response.csrf_token_name) {
                    newCsrfName = response.csrf_token_name;
                    newCsrfHash = response.csrf_hash;
                }
                if (newCsrfName && newCsrfHash) {
                    $('input[name="' + newCsrfName + '"]').val(newCsrfHash);
                }
                resolve({ exists: response.exists });
            },
            error: function (error) {
                console.error("Error:", error);
                resolve({ exists: false }); // Assume email doesn't exist on error
            }
        });
    });
}