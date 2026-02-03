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
    echo '<meta name="description" content="Wayo Academy offers the best tools for learning, training, and skill development.">';
}
?>

<?php 
// Keywords - add article tags if available
if (isset($article) && isset($article['tags'])) {
    echo '<meta name="keywords" content="' . htmlspecialchars($article['tags']) . ', Wayo Academy, online learning">';
} else {
    echo '<meta name="keywords" content="Wayo Academy, online learning, training, coaching, school">';
}
?>

<meta name="robots" content="index, follow">
<meta content="Wayo Academy" name="author" />
<meta name="google-site-verification" content="x3eKErXcyx6Umefjdw45FBGJo8YVk_Ly8dKWLWy0C74" />

<?php if (isset($article)): ?>
<!-- Open Graph Meta Tags for Social Sharing -->
<meta property="og:title" content="<?php echo htmlspecialchars($article['meta_title'] ?? $article['title']); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($article['meta_description'] ?? $article['summary']); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($article['image']); ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="<?php echo current_url(); ?>">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($article['meta_title'] ?? $article['title']); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($article['meta_description'] ?? $article['summary']); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($article['image']); ?>">
<?php endif; ?>

<!-- App favicon -->
<link rel="shortcut icon" href="<?php echo $this->settings_model->get_favicon(); ?>">
