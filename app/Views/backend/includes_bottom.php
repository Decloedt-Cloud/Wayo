<!-- bundle -->
<?php error_log("includes_bottom.php loaded"); ?>
<script type="text/javascript">
  const ENABLE_TOASTS = true;
</script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/app.min.js"></script>


<!-- third party js -->
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/dataTables.bootstrap5.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/responsive.bootstrap5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/buttons.bootstrap5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/buttons.html5.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/buttons.flash.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/dataTables.keyTable.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/jquery-ui.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/fullcalendar.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/summernote-bs4.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/toastr.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/sweetalert.min.js"></script>

<!-- JS de bootstrap-select -->
<script src="<?php echo base_url(); ?>assets/backend/js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function() {
  $('#class_id').selectpicker();
});
</script>
<!-- third party js ends -->

<!-- Typehead -->
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/handlebars.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/typeahead.bundle.min.js"></script>
<script>
window.TYPEAHEAD_DATA_BASE = <?php echo json_encode(base_url('assets/backend/data/typeahead/')); ?>;
</script>

<!-- demo app -->
<script src="<?php echo base_url(); ?>assets/backend/js/pages/demo.typehead.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/pages/demo.datatable-init.js"></script>
<!-- end demo js-->

<script src="<?php echo base_url(); ?>assets/backend/js/vendor/jquery.validate.min.js"></script>

<script src="<?php echo base_url(); ?>assets/backend/js/ajax_form_submission.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/content-placeholder.min.js"></script>
<script src="<?php echo base_url(); ?>assets/backend/js/init.min.js"></script>

<!-- dragula js-->
<script src="<?php echo base_url(); ?>assets/backend/js/vendor/dragula.min.js"></script>
<!-- component js -->
<script src="<?php echo base_url(); ?>assets/backend/js/ui/component.dragula.js"></script>
<!-- Timepicker -->

<script type="text/javascript">
	$(document).ready(function () {
        $('select.select2:not(.normal)').each(function () { $(this).select2(); });

        $(window).resize(function(){
		    if($(window).width() <= 767){
			    $('.leftside-menu-detached').removeClass('show');
			}			
		});
    });

    if($(window).width() <= 767){
	    $('.leftside-menu-detached').removeClass('show');
	}



</script>

<!-- Global Chat Notification Script -->
<?php include APPPATH . 'Views/backend/global_chat_notification.php'; ?>
