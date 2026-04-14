<?php
$enableToasts = isset($enable_toasts) ? (bool) $enable_toasts : true;
$session = session();
$infoMessage = (string) ($session->getFlashdata('info_message') ?? '');
$errorMessage = (string) ($session->getFlashdata('error_message') ?? '');
$flashMessage = (string) ($session->getFlashdata('flash_message') ?? '');
$ajaxFlashMessage = (string) ($session->getFlashdata('ajax_flash_message') ?? '');
$ajaxErrorMessage = (string) ($session->getFlashdata('ajax_error_message') ?? '');
?>
<!-- Toastr and alert notifications for PHP scripts -->
<script type="text/javascript">
function notify(message) {
  <?php if ($enableToasts): ?>
  $.NotificationApp.send("<?php echo get_phrase('heads_up'); ?>!", message ,"top-right","rgba(0,0,0,0.2)","info");
  <?php endif; ?>
}

function success_notify(message) {
  <?php if ($enableToasts): ?>
  $.NotificationApp.send("<?php echo get_phrase('success'); ?> !", message ,"top-right","rgba(0,0,0,0.2)","success");
  <?php endif; ?>
}

function error_notify(message) {
  <?php if ($enableToasts): ?>
  $.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?> !", message ,"top-right","rgba(0,0,0,0.2)","error");
  <?php endif; ?>
}
</script>

<?php if ($enableToasts): ?>

<?php if ($infoMessage !== ''):?>
<script type="text/javascript">
  $.NotificationApp.send("<?php echo get_phrase('success'); ?>!", '<?php echo $infoMessage; ?>' ,"top-right","rgba(0,0,0,0.2)","info");
</script>
<?php endif;?>

<?php if ($errorMessage !== ''):?>
<script type="text/javascript">
  $.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", '<?php echo $errorMessage; ?>' ,"top-right","rgba(0,0,0,0.2)","error");
</script>
<?php endif;?>

<?php 
$allowed_roles = ['superadmin', 'admin', 'teacher', 'student'];
$flash_message = $flashMessage;

// Vérifier si on vient de la page de login
$referrer = service('request')->getServer('HTTP_REFERER');
$comes_from_login = $referrer && (strpos($referrer, 'login') !== false);

// Vérifier si c'est le message welcome back
$flash_message_lower = strtolower($flash_message ?? '');
$is_welcome_message = (strpos($flash_message_lower, 'welcome') !== false) || 
                     (strpos($flash_message_lower, 'bienvenue') !== false);
$uri = service('uri');

if ($flash_message != "" && 
    in_array($uri->getSegment(1), $allowed_roles) && 
    $uri->getSegment(2) === 'dashboard'):
    
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

<?php endif; ?>

<script>
	function error_required_field() {
	  <?php if ($enableToasts): ?>
	  $.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", "<?php echo get_phrase('please_fill_all_the_required_fields'); ?>" ,"top-right","rgba(0,0,0,0.2)","error");
	  <?php endif; ?>
	}
</script>



<!-- SHOW TOASTR NOTIFICATION FOR AJAX-->
<?php if ($enableToasts): ?>
<?php if ($ajaxFlashMessage !== ''):?>

<script type="text/javascript">
	$.NotificationApp.send("<?php echo get_phrase('congratulations'); ?>!", "<?php echo $ajaxFlashMessage; ?>" ,"top-right","rgba(0,0,0,0.2)","success");
</script>

<?php endif;?>

<?php if ($ajaxErrorMessage !== ''):?>

<script type="text/javascript">
	$.NotificationApp.send("<?php echo get_phrase('oh_snap'); ?>!", "<?php echo $ajaxErrorMessage; ?>" ,"top-right","rgba(0,0,0,0.2)","error");
</script>
<?php endif;?>
<?php endif;?>