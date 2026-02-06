<meta charset="utf-8" />
<?php 
// SEO Meta Title
if (isset($article) && isset($article['meta_title'])) {
    // For blog articles with custom meta title
    echo '<title>' . htmlspecialchars($article['meta_title']) . ' | ' . get_settings('system_title') . '</title>';
} else {
    // Default title
    echo '<title>' . get_settings('system_title') . ' | ' . get_phrase($page_title) . '</title>';
}
?>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php 
// SEO Meta Description
if (isset($article) && isset($article['meta_description'])) {
    // For blog articles with custom meta description
    echo '<meta name="description" content="' . htmlspecialchars($article['meta_description']) . '">';
} else {
    // Default description
    echo '<meta name="description" content="Wayo offers the best tools for learning, training, and skill development.">';
}
?>

<?php 
// Keywords - add article tags if available
if (isset($article) && isset($article['tags'])) {
    echo '<meta name="keywords" content="' . htmlspecialchars($article['tags']) . ', Wayo, online learning">';
} else {
    echo '<meta name="keywords" content="Wayo, online learning, training, coaching, school">';
}
?>

<meta name="robots" content="index, follow">
<meta content="Wayo" name="author" />
<meta name="google-site-verification" content="x3eKErXcyx6Umefjdw45FBGJo8YVk_Ly8dKWLWy0C74" />

<?php
// =====================================================
// HREFLANG TAGS FOR SEO MULTILINGUAL
// =====================================================
// Helps Google understand language versions of the page
$supported_langs = array(
    'fr' => 'fr',
    'en' => 'en', 
    'ar' => 'ar',
    'es' => 'es',
    'nl' => 'nl'
);

// Get current URI and determine the base path without language prefix
$current_uri = $this->uri->uri_string();
$uri_segments = explode('/', $current_uri);
$current_lang_code = get_current_lang_code();

// Check if first segment is a language code
$path_without_lang = $current_uri;
if (!empty($uri_segments[0]) && array_key_exists($uri_segments[0], $supported_langs)) {
    // Remove language prefix from path
    array_shift($uri_segments);
    $path_without_lang = implode('/', $uri_segments);
}

// Generate hreflang tags for each language
foreach ($supported_langs as $code => $hreflang) {
    $lang_url = rtrim(base_url(), '/') . '/' . $code;
    if (!empty($path_without_lang)) {
        $lang_url .= '/' . $path_without_lang;
    }
    echo '<link rel="alternate" hreflang="' . $hreflang . '" href="' . $lang_url . '" />' . "\n";
}

// x-default points to the default language (English)
$default_url = rtrim(base_url(), '/') . '/en';
if (!empty($path_without_lang)) {
    $default_url .= '/' . $path_without_lang;
}
echo '<link rel="alternate" hreflang="x-default" href="' . $default_url . '" />' . "\n";

// Canonical URL - points to the current language version
$canonical_url = rtrim(base_url(), '/') . '/' . $current_lang_code;
if (!empty($path_without_lang)) {
    $canonical_url .= '/' . $path_without_lang;
}
echo '<link rel="canonical" href="' . $canonical_url . '" />' . "\n";

// Content-Language meta tag
echo '<meta http-equiv="content-language" content="' . $current_lang_code . '" />' . "\n";

// Open Graph locale tags for social sharing
$og_locales = array(
    'fr' => 'fr_FR',
    'en' => 'en_US',
    'ar' => 'ar_SA',
    'es' => 'es_ES',
    'nl' => 'nl_NL'
);
$current_og_locale = isset($og_locales[$current_lang_code]) ? $og_locales[$current_lang_code] : 'en_US';
echo '<meta property="og:locale" content="' . $current_og_locale . '" />' . "\n";

// Alternate locales for Open Graph
foreach ($og_locales as $code => $locale) {
    if ($code !== $current_lang_code) {
        echo '<meta property="og:locale:alternate" content="' . $locale . '" />' . "\n";
    }
}
?>

<?php if (isset($article)): ?>
<!-- Open Graph Meta Tags for Articles -->
<meta property="og:title" content="<?php echo htmlspecialchars($article['meta_title'] ?? $article['title']); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($article['meta_description'] ?? $article['summary']); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($article['image']); ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="<?php echo $canonical_url; ?>">
<meta property="og:site_name" content="<?php echo get_settings('system_title'); ?>">

<!-- Twitter Card Meta Tags for Articles -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($article['meta_title'] ?? $article['title']); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($article['meta_description'] ?? $article['summary']); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($article['image']); ?>">
<?php else: ?>
<!-- Open Graph Meta Tags for Pages -->
<meta property="og:title" content="<?php echo get_settings('system_title') . ' | ' . get_phrase($page_title); ?>">
<meta property="og:description" content="<?php echo get_phrase('wayo_meta_description') !== 'wayo_meta_description' ? get_phrase('wayo_meta_description') : 'Wayo offers the best tools for learning, training, and skill development.'; ?>">
<meta property="og:image" content="<?php echo base_url('uploads/system/' . get_settings('light_logo')); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo $canonical_url; ?>">
<meta property="og:site_name" content="<?php echo get_settings('system_title'); ?>">

<!-- Twitter Card Meta Tags for Pages -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo get_settings('system_title') . ' | ' . get_phrase($page_title); ?>">
<meta name="twitter:description" content="<?php echo get_phrase('wayo_meta_description') !== 'wayo_meta_description' ? get_phrase('wayo_meta_description') : 'Wayo offers the best tools for learning, training, and skill development.'; ?>">
<meta name="twitter:image" content="<?php echo base_url('uploads/system/' . get_settings('light_logo')); ?>">
<?php endif; ?>

<!-- App favicon -->
<link rel="shortcut icon" href="<?php echo $this->settings_model->get_favicon(); ?>">
