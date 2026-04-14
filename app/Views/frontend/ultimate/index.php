<?php

$request_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$request_host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8080';
$fallback_base_url = $request_scheme . '://' . $request_host . '/';
$base_url = function_exists('base_url') ? base_url() : $fallback_base_url;
$theme = $theme ?? 'ultimate';
$page_name = $page_name ?? 'home';
$page_title = $page_title ?? 'Home';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!function_exists('get_settings')) {
    function get_settings($key = '') {
        $settings = ['system_title' => 'School Management System'];
        return $key ? ($settings[$key] ?? '') : $settings;
    }
}

if (!function_exists('get_frontend_settings')) {
    function get_frontend_settings($key = '') {
        $settings = ['theme' => 'ultimate', 'website_title' => 'School Management System'];
        return $key ? ($settings[$key] ?? '') : $settings;
    }
}

if (!function_exists('get_current_lang_code')) {
    function get_current_lang_code() {
        return 'en';
    }
}

if (!function_exists('get_user_language')) {
    function get_user_language() {
        return 'english';
    }
}

if (!function_exists('get_phrase')) {
    function get_phrase($phrase = '') {
        return ucfirst(str_replace('_', ' ', $phrase));
    }
}

if (!function_exists('lang_route')) {
    function lang_route($route = '') {
        $url = function_exists('base_url') ? base_url() : $fallback_base_url;
        return rtrim($url, '/') . '/' . ltrim($route, '/');
    }
}

$school_title = get_settings('system_title');
$active_school_id = 1;
$html_lang = get_current_lang_code();
$is_rtl = (get_user_language() === 'arabic');
?>
<!DOCTYPE html>
<html lang="<?php echo $html_lang; ?>" <?php echo $is_rtl ? 'dir="rtl"' : 'dir="ltr"'; ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?> - <?php echo $school_title; ?></title>
    <meta name="description" content="School Management System">
    <?php if (!empty($canonical_url)): ?>
        <link rel="canonical" href="<?php echo esc($canonical_url, 'attr'); ?>">
    <?php endif; ?>
    <?php if (!empty($prev_url)): ?>
        <link rel="prev" href="<?php echo esc($prev_url, 'attr'); ?>">
    <?php endif; ?>
    <?php if (!empty($next_url)): ?>
        <link rel="next" href="<?php echo esc($next_url, 'attr'); ?>">
    <?php endif; ?>

    <!-- Preconnect to external CDNs for faster loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/<?php echo $theme; ?>/vendor/font-awesome/css/fontawesome-all.min.css">
    <!-- Font Awesome 6 for .fa-solid icons used across frontend -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/<?php echo $theme; ?>/vendor/animate.css/animate.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/<?php echo $theme; ?>/vendor/fancybox/jquery.fancybox.css">
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/<?php echo $theme; ?>/vendor/cubeportfolio/css/cubeportfolio.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/<?php echo $theme; ?>/css/theme.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/theme.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/footer.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/navigation.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/general.min.css">

    <!-- Custom CSS - always loaded for hero and common styles -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/custom.min.css">

    <!-- Page-specific CSS -->
    <?php if ($page_name == "about"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/about-page.min.css">
    <?php elseif ($page_name == "contact"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/contact.min.css">
    <?php elseif ($page_name == "online_admission"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/online-admission.min.css">
    <?php elseif ($page_name == "affiliation"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/affiliation.min.css">
    <?php elseif ($page_name == "terms" || $page_name == "terms_conditions"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/terms-conditions.css?v=<?php echo filemtime(ROOTPATH . 'assets/frontend/ultimate/css/terms-conditions.css'); ?>">
    <?php elseif ($page_name == "privacy" || $page_name == "privacy_policy"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/privacy-policy.min.css">
    <?php elseif ($page_name == "faq"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/faq.min.css">
    <?php elseif ($page_name == "tutorial"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/tutorial.css?v=<?php echo filemtime(ROOTPATH . 'assets/frontend/ultimate/css/tutorial.css'); ?>">
    <?php elseif ($page_name == "communities"): ?>
        <link rel="stylesheet" href="<?php echo $base_url; ?>assets/frontend/ultimate/css/communities.css?v=<?php echo filemtime(ROOTPATH . 'assets/frontend/ultimate/css/communities.css'); ?>">
    <?php endif; ?>
 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rellax/1.12.1/rellax.min.js" defer></script>
</head>
<body>

    <?php 
    $ci3Path = APPPATH . 'Views/frontend/ultimate/';
    
    if (file_exists($ci3Path . 'navigation.php')) {
        include $ci3Path . 'navigation.php';
    }
    ?>

    <?php 
    if (file_exists($ci3Path . $page_name . '.php')) {
        include $ci3Path . $page_name . '.php';
    }
    ?>

    <?php 
    if (file_exists($ci3Path . 'footer.php')) {
        include $ci3Path . 'footer.php';
    }
    ?>

    <?php 
    if (file_exists($ci3Path . 'javascripts.php')) {
        include $ci3Path . 'javascripts.php';
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js" defer></script>
    <script src="<?php echo $base_url; ?>assets/frontend/<?php echo $theme; ?>/vendor/cubeportfolio/js/jquery.cubeportfolio.min.js" defer></script>
    <script src="<?php echo $base_url; ?>assets/frontend/<?php echo $theme; ?>/js/theme.min.js" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" defer></script>
    <?php if ($page_name == "home"): ?>
        <script src="<?php echo $base_url; ?>assets/frontend/ultimate/js/home.min.js" defer></script>
    <?php endif; ?>

</body>
</html>
