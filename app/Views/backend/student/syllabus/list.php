<?php
$school_id = school_id();
if (isset($class_id) ):
    // Si class_id est "all", récupérer tous les syllabus de l'école active
    if ($class_id == 'all') {
        $syllabuses = $this->db->table('syllabuses')
            ->select('syllabuses.*')
            ->join('classes', 'syllabuses.class_id = classes.id', 'inner')
            ->where('classes.school_id', $school_id)
            ->where('syllabuses.session_id', active_session())
            ->get()
            ->getResultArray();
    } else {
        $syllabuses = $this->db->table('syllabuses')
            ->where('class_id', (int) $class_id)
            ->where('session_id', active_session())
            ->get()
            ->getResultArray();
    }
    if(count($syllabuses) > 0):?>
    <div class="modern-table-wrapper">
        <table class="table modern-table table-responsive-sm" width="100%">
            <thead>
                <tr>
                    <th><?php echo get_phrase('title'); ?></th>
                    <th><?php echo get_phrase('actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($syllabuses as $syllabus):
                    $file_ext = strtolower(pathinfo($syllabus['file'], PATHINFO_EXTENSION));
                    $supported_formats = array('pdf', 'docx', 'doc', 'txt');
                    $is_supported = in_array($file_ext, $supported_formats);
                ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="icon-box-sm" style="width:32px; height:32px; background:var(--primary-lighter); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--primary);">
                                    <i class="mdi mdi-file-document-outline"></i>
                                </div>
                                <span><?php echo $syllabus['title']; ?></span>
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo site_url('student/syllabus/download/'.$syllabus['id']); ?>" class="btn btn-light btn-sm" download style="color:var(--text-dark); border:1px solid var(--border-color);">
                                <i class="mdi mdi-download"></i> <?php echo get_phrase('download'); ?>
                            </a>
                            <?php if($is_supported): ?>
                            <a href="<?php echo site_url('student/chat_document_syllabus/'.$syllabus['id']); ?>" class="btn btn-chat-ai">
                                <i class="fas fa-wand-magic-sparkles sparkle-icon"></i> <?php echo get_phrase('chat_ai'); ?>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="empty-state">
            <img src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" alt="No Data" />
            <p><?php echo get_phrase('no_data_found'); ?></p>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="empty-state">
        <img src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" alt="No Data" />
        <p><?php echo get_phrase('please_select_a_class'); ?></p>
    </div>
<?php endif; ?>
