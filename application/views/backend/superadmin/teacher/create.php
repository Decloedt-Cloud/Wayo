<link rel="stylesheet" href="<?php echo base_url(); ?>assets/backend/css/edit-design-button.css">
<style>
    #email_select option[value=""] {
        font-weight: bold;
        color: #536de6 !important;
        background-color: #f8f9fa !important;
        padding-left: 10px;
    }
</style>

<form method="POST" class="d-block ajaxForm" action="<?php echo route('teacher/create'); ?>" enctype="multipart/form-data">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
    <input type="hidden" name="existing_user_id" id="existing_user_id" value="">

    <div class="form-row">
        <div class="form-group mb-3">
            <label for="email_field"><?php echo get_phrase("email"); ?><span class="required"> * </span></label>
            <div id="email-container">
                <input type="email" class="form-control" id="email" name="email" required autocomplete="off" placeholder="<?php echo get_phrase("enter_email"); ?>">
            </div>
            <small class="form-text text-muted email-status"></small>
        </div>

        <div class="regular-fields">
            <div class="form-group mb-1">
                <label for="password"><?php echo get_phrase("password"); ?><span class="required"> * </span></label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group mb-1">
                <label for="name"><?php echo get_phrase("name"); ?><span class="required"> * </span></label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
            <div class="form-group mb-1">
                <label for="phone"><?php echo get_phrase("phone_number"); ?><span class="required"> * </span></label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>
            <div class="form-group mb-1">
                <label for="gender"><?php echo get_phrase("gender"); ?></label>
                <select name="gender" id="gender" class="form-control">
                    <option value=""><?php echo get_phrase("select_a_gender"); ?></option>
                    <option value="Male"><?php echo get_phrase("male"); ?></option>
                    <option value="Female"><?php echo get_phrase("female"); ?></option>
                    <option value="Others"><?php echo get_phrase("others"); ?></option>
                </select>
            </div>
            <div class="form-group mb-1">
                <label for="address"><?php echo get_phrase("address"); ?></label>
                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
            </div>
            <div class="form-group mb-1">
                <label for="image_file"><?php echo get_phrase("upload_image"); ?></label>
                <input type="file" class="form-control" id="image_file" name="image_file">
            </div>
        </div>

        <div class="always-visible">
            <div class="form-group mb-1">
                <label for="designation"><?php echo get_phrase("designation"); ?><span class="required"> * </span></label>
                <input type="text" class="form-control" id="designation" name="designation" required>
            </div>
            <div class="form-group mb-1">
                <label><?php echo get_phrase("linkedin_profile_link"); ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="mdi mdi-linkedin"></i></span>
                    </div>
                    <input type="text" class="form-control" name="linkedin_link">
                </div>
            </div>
            <div class="form-group mb-1">
                <label for="about"><?php echo get_phrase("about"); ?><span class="required"> * </span></label>
                <textarea class="form-control" id="about" name="about" rows="5" required></textarea>
            </div>
            <!-- <div class="form-group mb-1">
                <label for="show_on_website"><?php echo get_phrase("show_on_website"); ?></label>
                <select name="show_on_website" id="show_on_website" class="form-control">
                    <option value="1"><?php echo get_phrase("show"); ?></option>
                    <option value="0"><?php echo get_phrase("do_not_need_to_show"); ?></option>
                </select>
            </div> -->
            <div class="form-group mt-3">
                <button class="btn btn-primary btn-l px-4" id="submit-btn" type="submit" disabled>
                    <i class="mdi mdi-plus"></i><?php echo get_phrase("create_teacher"); ?>
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    const phrases = {
        enter_email: "<?= get_phrase("enter_email") ?>",
        or_enter_new_email: "<?= get_phrase("or_enter_new_email") ?>",
        email_available: "<?= get_phrase("email_available") ?>",
        user_found_click_to_select: "<?= get_phrase("user_found_click_to_select") ?>",
        creating: "<?= get_phrase("creating") ?>",
        create_teacher: "<?= get_phrase("create_teacher") ?>",
        an_error_occurred_during_submission: "<?= get_phrase("an_error_occurred_during_submission") ?>"
    };
    const checkEmailUrl = "<?= route('check_teacher_email') ?>";

    $(document).ready(function() {
        let emailTimer;
        let isEmailDropdown = false;

        function resetToInput() {
            $('#email-container').html(
                `<input type="email" class="form-control" id="email" name="email" required autocomplete="off" placeholder="${phrases.enter_email}">`
            );
            $('#existing_user_id').val('');
            $('.regular-fields').show();
            $('#submit-btn').prop('disabled', true);
            isEmailDropdown = false;
            rebindEmailKeyup();
            toggleRequiredFields();
        }

        function toggleRequiredFields() {
            if (isEmailDropdown) {
                $('#name, #password, #phone, #gender, #address').removeAttr('required');
            } else {
                $('#name, #password, #phone').attr('required', 'required');
            }
        }

        function transformToDropdown(user) {
            $('#email-container').html(
                `<select class="form-control" id="email_select" name="email_select" required>
                    <option value="${user.id}">${user.name} (${user.email})</option>
                </select>`
            );
            $('#existing_user_id').val(user.id);
            $('.regular-fields').hide();
            $('.always-visible').show();
            $('#submit-btn').prop('disabled', false);
            isEmailDropdown = true;

            $('#email_select').prepend(`<option value="">${phrases.or_enter_new_email}</option>`);
            toggleRequiredFields();
        }

        function rebindEmailKeyup() {
            $(document).off('keyup', '#email');
            $(document).on('keyup', '#email', function() {
                clearTimeout(emailTimer);
                const email = $(this).val().trim();
                const $status = $('.email-status');

                $status.html('').removeClass('text-success text-danger text-warning');
                $('#existing_user_id').val('');
                $('#submit-btn').prop('disabled', true);

                if (email.length < 5) return;

                emailTimer = setTimeout(function() {
                    $.ajax({
                        url: checkEmailUrl,
                        type: 'POST',
                        data: {
                            email: email,
                            [csrfName]: csrfHash
                        },
                        success: function(res) {
                            let response = typeof res === 'object' ? res : JSON.parse(res);

                            $status.html('').removeClass('text-success text-danger text-warning');
                            $('#existing_user_id').val('');
                            $('#submit-btn').prop('disabled', true);

                            if (response.status === 'new') {
                                $status.html('<span class="text-success">' + phrases.email_available + '</span>');
                                $('.regular-fields').show();
                                $('#submit-btn').prop('disabled', false);

                            } else if (response.status === 'exists') {
                                $status.html('<span class="text-warning">' + phrases.user_found_click_to_select + '</span>');
                                transformToDropdown(response.user);

                            } else if (response.status === 'exists_in_school') {
                                $status.html('<span class="text-danger">' + response.message + '</span>');
                                resetToInput();
                                $('#submit-btn').prop('disabled', true);
                            }
                        }
                    });
                }, 600);
            });
        }

        rebindEmailKeyup();

        $(document).on('change', '#email_select', function() {
            if ($(this).val() === '') {
                resetToInput();
                $('.email-status').html('');
            } else {
                $('#existing_user_id').val($(this).val());
                $('#submit-btn').prop('disabled', false);
            }
        });

        $(document).on('blur', '#email', function() {
            if ($(this).val().trim() === '' && !isEmailDropdown) {
                $('.email-status').html('');
                $('#submit-btn').prop('disabled', true);
            }
        });

        $(".ajaxForm").submit(function(e) {
            e.preventDefault();
            const $btn = $('#submit-btn');
            $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i>' + phrases.creating + '...');

            const formData = new FormData(this);

            if (isEmailDropdown && $('#email_select').length) {
                formData.set('existing_user_id', $('#existing_user_id').val());
                formData.delete('email');
                formData.delete('password');
                formData.delete('name');
                formData.delete('phone');
                formData.delete('gender');
                formData.delete('address');
                formData.delete('image_file');
            }

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        success_notify(response.notification);
                        $('input[name="' + response.csrf.name + '"]').val(response.csrf.hash);
                        setTimeout(() => location.reload(), 3000);
                    } else {
                        error_notify(response.notification || 'Erreur');
                        $btn.prop('disabled', false).html('<i class="mdi mdi-plus"></i>' + phrases.create_teacher);
                    }
                },
                error: function() {
                    error_notify(phrases.an_error_occurred_during_submission);
                    $btn.prop('disabled', false).html('<i class="mdi mdi-plus"></i>' + phrases.create_teacher);
                }
            });
        });
    });
</script>