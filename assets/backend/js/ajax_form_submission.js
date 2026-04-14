//Form Submition
function ajaxSubmit(e, form, callBackFunction) {

    if (form.valid()) {
        e.preventDefault();

        var action = form.attr('action');
        var form2 = e.target;
        var data = new FormData(form2);
        $.ajax({
            type: "POST",
            url: action,
            processData: false,
            contentType: false,
            dataType: 'json',
            data: data,
            success: function (response) {
                // Handle both legacy (status as JSON string) and CI4-safe (status as object) responses.
                var statusData = response && response.status;
                if (typeof statusData === 'string') {
                    try {
                        statusData = JSON.parse(statusData);
                    } catch (err) {
                        statusData = { status: false, notification: 'Invalid server response' };
                    }
                }
                if (!statusData || typeof statusData !== 'object') {
                    statusData = { status: false, notification: 'Invalid server response' };
                }

                var csrf = response && response.csrf ? response.csrf : {};
                var csrfName = csrf.csrfName || csrf.name;
                var csrfHash = csrf.csrfHash || csrf.hash;
                if (csrfName && csrfHash) {
                    $('input[name="' + csrfName + '"]').val(csrfHash);
                }
                if (statusData.status) {
                    success_notify(statusData.notification);
                    if (form.attr('class') === 'ajaxDeleteForm') {
                        $('#alert-modal').modal('toggle')
                    } else {
                        $('#right-modal').modal('hide');
                    }
                    callBackFunction();
                } else {
                    error_notify(statusData.notification);
                }
                console.log()
                // Mettre à jour le jeton CSRF pour les futures soumissions
                

                setTimeout(() => {
                    if (response.refresh) {
                        window.location.reload()
                    }
                }, 3000);
            }
        });
    } else {
        error_required_field();
    }
}
