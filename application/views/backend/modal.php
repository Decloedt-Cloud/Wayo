<script type="text/javascript">
// Define showNotification globally if not already defined
if (typeof showNotification !== 'function') {
    window.showNotification = function(type, message) {
        if (typeof ENABLE_TOASTS !== 'undefined' && !ENABLE_TOASTS) {
            return;
        }
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
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
            if (type === 'success') {
                toastr.success(message);
            } else if (type === 'error') {
                toastr.error(message);
            } else if (type === 'warning') {
                toastr.warning(message);
            } else {
                toastr.info(message);
            }
        } else {
            // Fallback if toastr is not loaded
            console.log(type.toUpperCase() + ': ' + message);
            alert(message);
        }
    }
}

var callBackFunction;
var callBackFunctionForGenericConfirmationModal;
function largeModal(url, header)
{
  jQuery('#large-modal').modal('show', {backdrop: 'true'});
  // SHOW AJAX RESPONSE ON REQUEST SUCCESS
  $.ajax({
    url: url,
    success: function(response)
    {
      jQuery('#large-modal .modal-body').html(response);
      jQuery('#large-modal .modal-title').html(header);
    }
  });
}

function previewModal(url, header)
{
  jQuery('#preview-modal').modal('show', {backdrop: 'true'});
  // SHOW AJAX RESPONSE ON REQUEST SUCCESS
  $.ajax({
    url: url,
    success: function(response)
    {
      jQuery('#preview-modal .modal-body').html(response);
      jQuery('#preview-modal .modal-title').html(header);
    }
  });
}

function rightModal(url, header)
{
  

  // LOADING THE AJAX MODAL
  jQuery('#right-modal').modal('show', {backdrop: 'true'});



  // SHOW AJAX RESPONSE ON REQUEST SUCCESS
  $.ajax({
    url: url,
    success: function(response)
    {
      jQuery('#right-modal .modal-body').html(response);
      jQuery('#right-modal .modal-title').html(header);
      
    }
  });
}


function confirmModal(delete_url, callback) {
    Swal.fire({
        title: '<?php echo get_phrase("are_you_sure"); ?>',
        text: '<?php echo get_phrase("you_will_not_be_able_to_revert_this"); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?php echo get_phrase("yes_delete_it"); ?>',
        cancelButtonText: '<?php echo get_phrase("cancel"); ?>',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
            var csrfHash = $('input[name="' + csrfName + '"]').val();

            $.ajax({
                url: delete_url,
                type: 'POST',
                data: { [csrfName]: csrfHash },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        showNotification('success', response.notification || '<?php echo js_phrase('deleted_successfully'); ?>');
                        if (response.csrf) {
                            $('input[name="' + response.csrf.csrfName + '"]').val(response.csrf.csrfHash);
                        }
                        setTimeout(function() {
                            if (callback && typeof callback === 'function') {
                                callback(response);
                            } else {
                                location.reload();
                            }
                        }, 500);
                    } else {
                        showNotification('error', response.notification || '<?php echo js_phrase('failed_to_delete'); ?>');
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('error', '<?php echo js_phrase('failed_to_delete'); ?>');
                }
            });
        }
    });
}

function confirmModalRedirect(delete_url) {
    Swal.fire({
        title: '<?php echo get_phrase("are_you_sure"); ?>',
        text: '<?php echo get_phrase("you_will_not_be_able_to_revert_this"); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?php echo get_phrase("yes_delete_it"); ?>',
        cancelButtonText: '<?php echo get_phrase("cancel"); ?>',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.getElementById('delete_form');
            form.action = delete_url;
            form.submit();
        }
    });
}

function genericConfirmModal(callBackFunction) {
    Swal.fire({
        title: '<?php echo get_phrase("are_you_sure"); ?>',
        text: '<?php echo get_phrase("you_will_not_be_able_to_revert_this"); ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?php echo get_phrase("continue"); ?>',
        cancelButtonText: '<?php echo get_phrase("cancel"); ?>',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            callBackFunction();
        }
    });
}

function callTheCallBackFunction() {
  // Kept for compatibility
}
function blankFunction(){

}
function reloadFunction(){
  // reload the current page
  window.location.reload();
}

function updateAjaxModal(url, header) {
    // Afficher un chargeur pendant la requête
    jQuery('#scrollable-modal .modal-body').html('<div style="text-align:center;margin-top:200px;"><img style="width: 100px; opacity: 0.4; " src="<?php echo base_url().'assets/backend/images/straight-loader.gif'; ?>" /></div>');
    jQuery('#scrollable-modal .modal-title').html('...');

    // Charger le contenu via AJAX
    $.ajax({
        url: url,
        success: function(response) {
            jQuery('#scrollable-modal .modal-body').html(response);
            jQuery('#scrollable-modal .modal-title').html(header);
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors du chargement du contenu : ', error);
            console.error('Statut : ', status);
            console.error('Réponse : ', xhr.responseText);
            error_notify('Erreur lors du rechargement de la liste des questions.');
        }
    });
}

function updateLargeModal(url, header) {
    // S'assurer que le modal est visible
    if (!jQuery('#large-modal').hasClass('show')) {
        jQuery('#large-modal').modal('show', {backdrop: 'true'});
    }

    // Afficher un chargeur pendant la requête
    jQuery('#large-modal .modal-body').html('<div style="text-align:center;margin-top:200px;"><img style="width: 100px; opacity: 0.4; " src="<?php echo base_url().'assets/backend/images/straight-loader.gif'; ?>" /></div>');
    jQuery('#large-modal .modal-title').html('...');

    // Charger le contenu via AJAX
    $.ajax({
        url: url,
        success: function(response) {
            jQuery('#large-modal .modal-body').html(response);
            jQuery('#large-modal .modal-title').html(header);
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors du chargement du contenu : ', error);
            console.error('Statut : ', status);
            console.error('Réponse : ', xhr.responseText);
            error_notify('Erreur lors du rechargement de la liste des questions.');
        }
    });
}
</script>



<!-- Right modal content -->
<div id="right-modal" class="modal fade" tabindex="0" role="dialog" aria-hidden="true" style="overflow-y: hidden !important;">
  <div class="modal-dialog modal-lg modal-right" style="width: 100% !important; max-width: 440px !important; min-height: 100% !important;">
    <div class="modal-content modal_height">

      <div class="modal-header border-1">
        <button type="button" class="btn btn-outline-secondary py-0 px-1" data-bs-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style="overflow-y: auto !important;">
        <div class="container-fluid text-center">
          <img src="<?php echo base_url('assets/backend/images/straight-loader.gif'); ?>" style="width: 60px; padding: 50% 0px; opacity: .6;">
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
  var myModalEl = document.getElementById('right-modal')
    myModalEl.addEventListener('hidden.bs.modal', function (event) {
      $('select.select2:not(.normal)').each(function () { $(this).select2(); });
  });
</script>


<!--  Large Modal -->
<div class="modal fade" id="large-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header d-print-none">
        <h4 class="modal-title" id="myLargeModalLabel"></h4>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body">

      </div>
    </div>
<!--/.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
 

<!-- Hidden form for confirmModal (CSRF token) -->
<form method="POST" class="ajaxDeleteForm" action="" id="delete_form" style="display: none;">
    <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />
</form>



<div class="modal fade" id="preview-modal" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content course-preview-modal">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="pageReload()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center" style="min-height: 300px;">
            <img style="width: 60px; margin-top: 100px;" src="<?php echo site_url('assets/backend/images/straight-loader.gif'); ?>">
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  function pageReload(){
    //filterCourse();
    filterCourseFullPage();
    //location.reload();
  }
</script>

<!-- <script>
    jQuery(".ajaxDeleteForm").submit(function(e) {

        var form = $(this);
        ajaxSubmit(e, form, callBackFunction);
    });
</script> -->

<script>
  function showAjaxModal(url, header)
  {
      // SHOWING AJAX PRELOADER IMAGE
      jQuery('#scrollable-modal .modal-body').html('<div style="text-align:center;margin-top:200px;"><img style="width: 100px; opacity: 0.4; " src="<?php echo base_url().'assets/backend/images/straight-loader.gif'; ?>" /></div>');
      jQuery('#scrollable-modal .modal-title').html('...');
      // LOADING THE AJAX MODAL
      jQuery('#scrollable-modal').modal('show', {backdrop: 'true'});

      // SHOW AJAX RESPONSE ON REQUEST SUCCESS
      $.ajax({
          url: url,
          success: function(response)
          {
              jQuery('#scrollable-modal .modal-body').html(response);
              jQuery('#scrollable-modal .modal-title').html(header);
          }
      });
  }
</script>
<!-- Scrollable modal -->
<div class="modal fade" id="scrollable-modal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="scrollableModalTitle">Modal title</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body ms-2 me-2">

          </div>
          <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal"><?php echo get_phrase("close"); ?></button>
          </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>