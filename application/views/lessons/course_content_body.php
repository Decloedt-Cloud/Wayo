<?php
$lesson_details = $this->lms_model->get_lessons('lesson', $lesson_id)->row_array();
$lesson_thumbnail_url = 'uploads/course_thumbnail/'.$this->lms_model->get_course_by_id($course_id)['thumbnail'];
if (file_exists($lesson_thumbnail_url)){
    $lesson_thumbnail_url = base_url().$lesson_thumbnail_url;
} else {
    $lesson_thumbnail_url = base_url().'uploads/course_thumbnail/placeholder.png';
}
$provider = $lesson_details['video_type'];
$opened_section_id = $lesson_details['section_id'];
?>

<style>
/* ========== MODERN CONTENT BODY STYLES ========== */
.lesson-body-card {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
    overflow: hidden;
    animation: fadeUp 0.4s ease;
}

.lesson-body-header {
    padding: 1rem 1.5rem;
    background: linear-gradient(180deg, rgba(99, 102, 241, 0.04), transparent);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}

.lesson-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.8125rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.lesson-type-badge.video {
    background: linear-gradient(135deg, var(--primary-lighter), #c7d2fe);
    color: var(--primary-dark);
}

.lesson-type-badge.quiz {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.lesson-type-badge.lesson {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.lesson-type-badge i {
    font-size: 1rem;
}

.lesson-title-section {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.lesson-title {
    font-family: var(--font-header);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
    line-height: 1.4;
}

/* Video Player Area */
.video-player-wrapper {
    background: #0f0f0f;
    position: relative;
}

.video-player-wrapper .plyr__video-embed,
.video-player-wrapper video {
    width: 100%;
    aspect-ratio: 16/9;
    min-height: 400px;
}

.video-player-wrapper iframe {
    width: 100%;
    aspect-ratio: 16/9;
    min-height: 400px;
    border: none;
}

/* Summary/Content Section */
.lesson-summary-section {
    padding: 1.5rem;
}

.lesson-summary-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid var(--border-color);
}

.lesson-summary-header i {
    color: var(--primary);
    font-size: 1.125rem;
}

.lesson-summary-header h4 {
    font-family: var(--font-header);
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0;
}

.lesson-summary-content {
    color: var(--text-dark);
    line-height: 1.8;
    font-size: 0.9375rem;
}

.lesson-summary-content p {
    margin-bottom: 1rem;
    color: var(--text-dark);
}

.lesson-summary-content a {
    color: var(--primary);
    text-decoration: underline;
}

.lesson-summary-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-md);
    margin: 1rem 0;
}

.lesson-summary-content iframe,
.lesson-summary-content video,
.lesson-summary-content embed,
.lesson-summary-content object {
    width: 100% !important;
    max-width: 100% !important;
    min-height: 350px;
    aspect-ratio: 16/9;
    border-radius: var(--radius-md);
    margin: 1rem 0;
}

.empty-summary {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    text-align: center;
    background: var(--bg-main);
    border-radius: var(--radius-lg);
    border: 2px dashed var(--border-color);
}

.empty-summary i {
    font-size: 2.5rem;
    color: var(--border-color);
    margin-bottom: 1rem;
}

.empty-summary p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.9375rem;
}

/* Quiz Area Container */
.quiz-container {
    padding: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .lesson-body-header {
        padding: 1rem;
    }
    
    .lesson-title-section {
        padding: 1rem;
    }
    
    .lesson-title {
        font-size: 1.25rem;
    }
    
    .lesson-summary-section {
        padding: 1rem;
    }
    
    .video-player-wrapper .plyr__video-embed,
    .video-player-wrapper video,
    .video-player-wrapper iframe {
        min-height: 220px;
    }
}

@media (max-width: 576px) {
    .lesson-type-badge {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }
    
    .lesson-title {
        font-size: 1.125rem;
    }
}
</style>

<div class="lesson-body-card" id="video_player_area">
    <!-- Header with Type Badge -->
    <div class="lesson-body-header">
        <?php
        $lesson_type = $lesson_details['lesson_type'];
        $type_class = 'lesson';
        $type_icon = 'fas fa-file-lines';
        $type_text = get_phrase('lesson');
        
        if ($lesson_type == 'video' || empty($lesson_type)) {
            $type_class = 'video';
            $type_icon = 'fas fa-play-circle';
            $type_text = get_phrase('video');
        } elseif ($lesson_type == 'quiz') {
            $type_class = 'quiz';
            $type_icon = 'fas fa-question-circle';
            $type_text = get_phrase('quiz');
        }
        ?>
        <div class="lesson-type-badge <?php echo $type_class; ?>">
            <i class="<?php echo $type_icon; ?>"></i>
            <span><?php echo $type_text; ?></span>
        </div>
    </div>
    
    <!-- Lesson Title -->
    <div class="lesson-title-section">
        <h1 class="lesson-title"><?php echo $lesson_details['title']; ?></h1>
    </div>

    <!-- Main Content Area -->
    <?php
    // Video Content
    if($lesson_details['lesson_type'] == 'video' || $lesson_details['lesson_type'] == '' || $lesson_details['lesson_type'] == NULL):
        $video_url = $lesson_details['video_url'];
        $provider = $lesson_details['video_type'];
    ?>
    <div class="video-player-wrapper">
        <?php if (strtolower($provider) == 'youtube'): ?>
            <!-- YouTube Player -->
            <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
            <div class="plyr__video-embed" id="player">
                <iframe src="<?php echo $video_url;?>?origin=https://plyr.io&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1" 
                        allowfullscreen 
                        allowtransparency 
                        allow="autoplay"></iframe>
            </div>
            <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
            <script>if (typeof player === 'undefined') { var player = new Plyr('#player'); }</script>

        <?php elseif (strtolower($provider) == 'vimeo'):
            $video_details = $this->video_model->getVideoDetails($video_url);
            $video_id = $video_details['video_id'];
        ?>
            <!-- Vimeo Player -->
            <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
            <div class="plyr__video-embed" id="player">
                <iframe src="https://player.vimeo.com/video/<?php echo $video_id; ?>?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media" 
                        allowfullscreen 
                        allowtransparency 
                        allow="autoplay"></iframe>
            </div>
            <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
            <script>if (typeof player === 'undefined') { var player = new Plyr('#player'); }</script>

        <?php elseif (strtolower($provider) == 'mydevice'): ?>
            <!-- Local Video -->
            <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
            <video poster="<?php echo $lesson_thumbnail_url;?>" controls playsinline id="player">
                <source src="<?php echo base_url('uploads/videos/'.$lesson_details['video_uplaod']); ?>" type="video/mp4">
            </video>
            <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
            <script>if (typeof player === 'undefined') { var player = new Plyr('#player'); }</script>

        <?php else: ?>
            <!-- HTML5 Video -->
            <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
            <video poster="<?php echo $lesson_thumbnail_url;?>" id="player" playsinline controls>
                <?php if (get_video_extension($video_url) == 'mp4'): ?>
                    <source src="<?php echo $video_url; ?>" type="video/mp4">
                <?php elseif (get_video_extension($video_url) == 'webm'): ?>
                    <source src="<?php echo $video_url; ?>" type="video/webm">
                <?php else: ?>
                    <p style="color: white; padding: 2rem; text-align: center;"><?php echo get_phrase('video_url_is_not_supported'); ?></p>
                <?php endif; ?>
            </video>
            <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
            <script>if (typeof player === 'undefined') { var player = new Plyr('#player'); }</script>
        <?php endif; ?>
    </div>
    
    <?php elseif ($lesson_details['lesson_type'] == 'quiz'): ?>
    <!-- Quiz Content -->
    <div class="quiz-container">
        <?php include 'quiz_view.php'; ?>
    </div>
    
    <?php else: ?>
    <!-- Other Lesson Types -->
    <div class="lesson-summary-section">
        <!-- Placeholder for attachments or other content types -->
    </div>
    <?php endif; ?>

    <!-- Summary/Description Section -->
    <div class="lesson-summary-section">
        <div class="lesson-summary-header">
            <i class="<?php echo $lesson_details['lesson_type'] == 'quiz' ? 'fas fa-info-circle' : 'fas fa-align-left'; ?>"></i>
            <h4><?php echo $lesson_details['lesson_type'] == 'quiz' ? get_phrase('instructions') : get_phrase('summary'); ?></h4>
        </div>
        
        <div class="lesson-summary-content">
            <?php if ($lesson_details['summary'] == ""): ?>
                <div class="empty-summary">
                    <i class="fas fa-file-alt"></i>
                    <p><?php echo $lesson_details['lesson_type'] == 'quiz' ? get_phrase('no_instruction_found') : get_phrase('no_summary_found'); ?></p>
                </div>
            <?php else: ?>
                <?php echo $lesson_details['summary']; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
