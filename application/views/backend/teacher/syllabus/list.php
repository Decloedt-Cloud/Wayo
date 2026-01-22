<?php
$school_id = school_id();
if (isset($class_id) ):
       if ($class_id == 'all') {
        // Tous les syllabus de l'école et session active
        $syllabuses = $this->db->get_where('syllabuses', array(
            'school_id' => $school_id,
            'session_id' => active_session()
        ))->result_array();
    } else {
        //Syllabus filtrés par classe
        $syllabuses = $this->db->get_where('syllabuses', array(
            'class_id' => $class_id,
            'session_id' => active_session()
        ))->result_array();
    }
    
    if(count($syllabuses) > 0):?>
    <div class="exp-table-container">
        <table id="basic-datatable" class="exp-report-table" width="100%">
            <thead>
                <tr>
                    <th><i class="mdi mdi-file-document-outline thead-icon"></i><?php echo get_phrase('title'); ?></th>
                    <th><i class="mdi mdi-book-open-page-variant-outline thead-icon"></i><?php echo get_phrase('syllabus'); ?></th>
                    <th><?php echo get_phrase('option'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($syllabuses as $syllabus):?>
                    <tr>
                        <td><?php echo $syllabus['title']; ?></td>
                        <td>
                            <a href="<?php echo base_url('uploads/syllabus/'.$syllabus['file']); ?>" class="exp-btn-sm exp-btn-download" download>
                                <i class="mdi mdi-download"></i> <?php echo get_phrase('download'); ?>
                            </a>
                        </td>
                        <td>
                            <button type="button" class="exp-btn-sm exp-btn-delete" onclick="confirmModal('<?php echo route('syllabus/delete/'.$syllabus['id']); ?>', showAllSyllabuses)" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo get_phrase('delete_syllabus'); ?>">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <?php include APPPATH.'views/backend/empty.php'; ?>
    <?php endif; ?>
<?php else: ?>
    <?php include APPPATH.'views/backend/empty.php'; ?>
<?php endif; ?>
