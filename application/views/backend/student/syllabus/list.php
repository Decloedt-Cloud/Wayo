<style>
.btn-chat-ai {
    background: linear-gradient(90deg, #f47a1f 0%, #fbb040 100%);
    border: none;
    color: #fff;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.676rem 1.8rem;
    line-height: 1.5;
    display: inline-flex;
    align-items: center;
    vertical-align: middle;
}
.btn-chat-ai:hover {
    background: linear-gradient(135deg, #ffb74d 0%, #ff9800 100%);
    color: #fff;
}
.sparkle-icon {
    display: inline-block;
    animation: sparkle 1.5s ease-in-out infinite;
    margin-right: 6px;
}
@keyframes sparkle {
    0%, 100% {
        text-shadow: 0 0 5px rgba(255, 255, 255, 0.8),
                     0 0 10px rgba(255, 255, 255, 0.6),
                     0 0 15px rgba(255, 215, 0, 0.4);
        transform: scale(1) rotate(0deg);
    }
    25% {
        text-shadow: 0 0 10px rgba(255, 255, 255, 1),
                     0 0 20px rgba(255, 215, 0, 0.8),
                     0 0 30px rgba(255, 152, 0, 0.6);
        transform: scale(1.1) rotate(5deg);
    }
    50% {
        text-shadow: 0 0 15px rgba(255, 255, 255, 1),
                     0 0 25px rgba(255, 215, 0, 1),
                     0 0 35px rgba(255, 152, 0, 0.8);
        transform: scale(1.2) rotate(0deg);
    }
    75% {
        text-shadow: 0 0 10px rgba(255, 255, 255, 1),
                     0 0 20px rgba(255, 215, 0, 0.8),
                     0 0 30px rgba(255, 152, 0, 0.6);
        transform: scale(1.1) rotate(-5deg);
    }
}

/* Mobile: afficher uniquement les icônes */
@media (max-width: 768px) {
    .btn-text-mobile {
        display: none;
    }
    .btn-chat-ai {
        padding: 0.676rem 1rem;
    }
    .btn-chat-ai .sparkle-icon {
        margin-right: 0;
    }
    .btn.btn-info.mdi-download {
        padding: 0.24rem 1rem;
    }
}
</style>

<?php
$school_id = school_id();
if (isset($class_id) ):
    // Si class_id est "all", récupérer tous les syllabus de l'école active
    if ($class_id == 'all') {
        $sql = "SELECT syllabuses.* FROM syllabuses
                INNER JOIN classes ON syllabuses.class_id = classes.id
                WHERE classes.school_id = ? AND syllabuses.session_id = ?";
        $syllabuses = $this->db->query($sql, array($school_id, active_session()))->result_array();
    } else {
        $this->db->reset_query();
        $syllabuses = $this->db->get_where('syllabuses', array('class_id' => $class_id, 'session_id' => active_session()))->result_array();
    }
    if(count($syllabuses) > 0):?>
    <table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
        <thead>
            <tr>
                <th><i class="mdi mdi-file-document-outline thead-icon"></i><?php echo get_phrase('title'); ?></th>
                <th><i class="mdi mdi-book-open-page-variant-outline thead-icon"></i><?php echo get_phrase('actions'); ?></th>
               
            </tr>
        </thead>
        <tbody>
            <?php foreach($syllabuses as $syllabus):
                $file_ext = strtolower(pathinfo($syllabus['file'], PATHINFO_EXTENSION));
                $supported_formats = array('pdf', 'docx', 'doc', 'txt');
                $is_supported = in_array($file_ext, $supported_formats);
            ?>
                <tr>
                    <td><?php echo $syllabus['title']; ?></td>
                    <td>
                        <a href="<?php echo base_url('uploads/syllabus/'.$syllabus['file']); ?>" class="btn btn-info mdi mdi-download" download><span class="btn-text-mobile"><?php echo get_phrase('download'); ?></span></a>
                        <?php if($is_supported): ?>
                        <a href="<?php echo site_url('student/chat_document_syllabus/'.$syllabus['id']); ?>" class="btn btn-chat-ai">
                            <i class="fas fa-wand-magic-sparkles sparkle-icon"></i><span class="btn-text-mobile"> <?php echo get_phrase('chat_ai'); ?></span>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <?php include APPPATH.'views/backend/empty.php'; ?>
    <?php endif; ?>
<?php else: ?>
    <?php include APPPATH.'views/backend/empty.php'; ?>
<?php endif; ?>
