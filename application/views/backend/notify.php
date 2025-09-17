<!-- Toastr and alert notifications for PHP scripts -->
<script type="text/javascript">
function notify(message) {
  $.NotificationApp.send("<?php echo get_phrase('heads_up'); ?>!", message ,"top-right","rgba(0,0,0,0.2)","info");
}

function success_notify(message) {
  $.NotificationApp.send("<?php echo get_phrase('success'); ?> !", message ,"top-right","rgba(0,0,0,0.2)","success");
}

function error_notify(message) {
  $.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?> !", message ,"top-right","rgba(0,0,0,0.2)","error");
}
</script>

<?php if ($this->session->flashdata('info_message') != ""):?>
<script type="text/javascript">
  $.NotificationApp.send("<?php echo get_phrase('success'); ?>!", '<?php echo $this->session->flashdata("info_message");?>' ,"top-right","rgba(0,0,0,0.2)","info");
</script>
<?php endif;?>

<?php if ($this->session->flashdata('error_message') != ""):?>
<script type="text/javascript">
  $.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", '<?php echo $this->session->flashdata("error_message");?>' ,"top-right","rgba(0,0,0,0.2)","error");
</script>
<?php endif;?>

<?php 
$allowed_roles = ['superadmin', 'admin', 'teacher', 'student'];
$flash_message = $this->session->flashdata('flash_message');

// Vérifier si on vient de la page de login
$referrer = $this->input->server('HTTP_REFERER');
$comes_from_login = $referrer && (strpos($referrer, 'login') !== false);

// Vérifier si c'est le message welcome back
$is_welcome_message = (strpos(strtolower($flash_message), 'welcome') !== false) || 
                     (strpos(strtolower($flash_message), 'bienvenue') !== false);

if ($flash_message != "" && 
    in_array($this->uri->segment(1), $allowed_roles) && 
    $this->uri->segment(2) === 'dashboard'):
    
    // Si c'est un message welcome, l'afficher seulement si on vient du login
    if ($is_welcome_message && $comes_from_login) {
        // Afficher le message welcome
        ?>
        <script type="text/javascript">
          $.NotificationApp.send("<?php echo get_phrase('success'); ?> !", '<?php echo $flash_message; ?>', "top-right", "rgba(0,0,0,0.2)", "success");
        </script>
        <?php
    } elseif (!$is_welcome_message) {
        // Afficher les autres messages normalement
        ?>
        <script type="text/javascript">
          $.NotificationApp.send("<?php echo get_phrase('success'); ?> !", '<?php echo $flash_message; ?>', "top-right", "rgba(0,0,0,0.2)", "success");
        </script>
        <?php
    }
endif; ?>

<script>
	function error_required_field() {
	  $.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", "<?php echo get_phrase('please_fill_all_the_required_fields'); ?>" ,"top-right","rgba(0,0,0,0.2)","error");
	}
</script>



<!-- SHOW TOASTR NOTIFICATION FOR AJAX-->
<?php if ($this->session->flashdata('ajax_flash_message') != ""):?>

<script type="text/javascript">
	$.NotificationApp.send("<?php echo get_phrase('congratulations'); ?>!", "<?php echo $this->session->flashdata('ajax_flash_message') ?>" ,"top-right","rgba(0,0,0,0.2)","success");
</script>

<?php endif;?>

<?php if ($this->session->flashdata('ajax_error_message') != ""):?>

<script type="text/javascript">
	$.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", "<?php echo $this->session->flashdata('ajax_error_message') ?>" ,"top-right","rgba(0,0,0,0.2)","error");
</script>
<?php endif;?>