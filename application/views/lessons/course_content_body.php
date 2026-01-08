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

<div class="play-lesson-card preview-card" id="video_player_area" style="max-height: calc(100vh - 100px); overflow-y: auto;">
    <!-- Header -->
    <div class="preview-header">
        <div class="preview-type">
             <?php
                $lesson_type = $lesson_details['lesson_type'];
                if ($lesson_type == 'video' || empty($lesson_type)) {
                    echo '<i class="fas fa-file-video"></i> <span>'.get_phrase("video").'</span>';
                } elseif ($lesson_type == 'quiz') {
                    echo '<i class="fas fa-circle-question"></i> <span>'.get_phrase("quiz").'</span>';
                } else {
                    echo '<i class="fas fa-file-lines"></i> <span>'.get_phrase("lesson").'</span>';
                }
             ?>
        </div>
    </div>
    
    <!-- Title -->
    <div class="preview-title-section">
         <h2><?php echo $lesson_details['title']; ?></h2>
    </div>

    <!-- Content -->
    <div class="preview-content" style="padding: 0;">
        <?php
        // If the lesson type is video
        if($lesson_details['lesson_type'] == 'video' || $lesson_details['lesson_type'] == '' || $lesson_details['lesson_type'] == NULL):
            $video_url = $lesson_details['video_url'];
            $provider = $lesson_details['video_type'];
            ?>

            <div class="">
            <!-- If the video is youtube video -->
            <?php if (strtolower($provider) == 'youtube'): ?>
                <!------------- PLYR.IO ------------>
                <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">

                <div class="plyr__video-embed" id="player">
                    <iframe class="w-100" style="aspect-ratio: 16/9; min-height: 400px;" src="<?php echo $video_url;?>?origin=https://plyr.io&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1" allowfullscreen allowtransparency allow="autoplay"></iframe>
                </div>

                <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
                <script>if (typeof player === 'undefined') { var player = new Plyr('#player'); }</script>
                <!------------- PLYR.IO ------------>

                <!-- If the video is vimeo video -->
            <?php elseif (strtolower($provider) == 'vimeo'):
                $video_details = $this->video_model->getVideoDetails($video_url);
                $video_id = $video_details['video_id'];?>
                <!------------- PLYR.IO ------------>
                <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
                <div class="plyr__video-embed" id="player">
                    <iframe class="w-100" style="aspect-ratio: 16/9; min-height: 400px;" src="https://player.vimeo.com/video/<?php echo $video_id; ?>?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media" allowfullscreen allowtransparency allow="autoplay"></iframe>
                </div>

                <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
                <script>if (typeof player === 'undefined') { var player = new Plyr('#player'); }</script>
                <!------------- PLYR.IO ------------>
                <?php elseif (strtolower($provider) == 'mydevice'):; ?>
                   <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
                   <video class="w-100" poster="<?php echo $lesson_thumbnail_url;?>" controls playsinline>
                    <source src="<?php echo base_url('uploads/videos/'.$lesson_details['video_uplaod']); ?>" type="video/mp4">
                </video>
            <?php else :?>
                <!------------- PLYR.IO ------------>
                <link rel="stylesheet" href="<?php echo base_url();?>assets/global/plyr/plyr.css">
                <video class="w-100" poster="<?php echo $lesson_thumbnail_url;?>" id="player" playsinline controls>
                    <?php if (get_video_extension($video_url) == 'mp4'): ?>
                        <source src="<?php echo $video_url; ?>" type="video/mp4">
                    <?php elseif (get_video_extension($video_url) == 'webm'): ?>
                        <source src="<?php echo $video_url; ?>" type="video/webm">
                    <?php else: ?>
                        <h4><?php get_phrase('video_url_is_not_supported'); ?></h4>
                    <?php endif; ?>
                </video>

                <script src="<?php echo base_url();?>assets/global/plyr/plyr.js"></script>
                <script>if (typeof player === 'undefined') { var player = new Plyr('#player'); }</script>
                <!------------- PLYR.IO ------------>
            <?php endif; ?>
            </div>
            
        <?php elseif ($lesson_details['lesson_type'] == 'quiz'): ?>
            <div style="padding: 20px;">
                <?php include 'quiz_view.php'; ?>
            </div>
        <?php else: ?>
            <div>
                <!-- Other types (files etc) could go here -->
            </div>
        <?php endif; ?>

        <!-- Instruction / Summary Content -->
        <div class="preview-content" style="padding-top: 20px;">
            <?php if ($lesson_details['summary'] == ""): ?>
                <p class="text-muted"><?php echo $lesson_details['lesson_type'] == 'quiz' ? get_phrase('no_instruction_found') : get_phrase('no_summary_found'); ?></p>
            <?php else: ?>
                <?php echo $lesson_details['summary']; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
