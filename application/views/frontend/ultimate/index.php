<?php

  // Désactivation du cache navigateur
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");


  $school_title = get_settings('system_title');
  $theme        = get_frontend_settings('theme');
  $active_school_id = $this->frontend_model->get_active_school_id();
?>
<!DOCTYPE html>
<?php 
  // Get current language code for HTML lang attribute
  $html_lang = get_current_lang_code();
  $is_rtl = (get_user_language() === 'arabic');
?>
<html lang="<?php echo $html_lang; ?>" <?php echo $is_rtl ? 'dir="rtl"' : 'dir="ltr"'; ?>>
  <head>
  
    <?php include 'metas.php'; ?>
    <?php include 'stylesheets.php';?>
    
    
  </head>
  <body>

    <?php include 'navigation.php';?>

    <?php include $page_name . '.php';?>
    

    <?php include 'footer.php';?>

    <?php include 'javascripts.php'; ?>

  </body>
</html>
