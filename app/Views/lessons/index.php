<?php
// Disable caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$lms_model = $this->lms_model ?? model('App\\Models\\addons\\Lms_model');
$course_row = (is_object($lms_model) && method_exists($lms_model, 'get_course_by_id'))
    ? $lms_model->get_course_by_id($course_id)
    : [];
$course_title = is_array($course_row) ? ($course_row['title'] ?? 'Course') : ((is_object($course_row) && isset($course_row->title)) ? $course_row->title : 'Course');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?php echo $course_title; ?> | <?php echo db()->table('schools')->where('id', school_id())->get()->getRowArray()['name'] ?? ''; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Online Course Learning Platform" />
    <meta name="author" content="Creativeitem" />
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $this->settings_model->get_favicon(); ?>">
    
    <?php include 'includes_top.php'; ?>
</head>
<body style="background: var(--bg-main, #f8fafc) !important; margin: 0; padding: 0;">
    <!-- CSRF Token -->
    <input type="hidden" name="<?= csrf_token(); ?>" value="<?= csrf_hash(); ?>">
    
    <!-- Main Content -->
    <?php
        include 'lessons.php';
        include 'includes_bottom.php';
        include 'common_scripts.php';
    ?>
</body>
</html>
