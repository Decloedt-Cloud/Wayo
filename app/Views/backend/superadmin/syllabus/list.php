<?php
$school_id = school_id();
if (isset($class_id) ):
       if ($class_id == 'all') {
        // Tous les syllabus de l'école et session active
        $syllabuses = db()->table('syllabuses')->where('school_id', $school_id)->where('session_id', active_session())->get()->getResultArray();
    } else {
        //Syllabus filtrés par classe
        $syllabuses = db()->table('syllabuses')->where('class_id', $class_id)->where('session_id', active_session())->get()->getResultArray();
    }
    if(count($syllabuses) > 0):?>
    <table id="basic-datatable" class="table table-striped dt-responsive nowrap table-modern" width="100%">
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
                    <td><a href="<?php echo site_url('superadmin/syllabus/download/'.$syllabus['id']); ?>" class="btn btn-info mdi mdi-download" download><?php echo get_phrase('download'); ?></a></td>
           
                    <td>
                        <button type="button" class="btn btn-icon btn-secondary btn-sm" style="margin-right:5px;" onclick="confirmModal('<?php echo route('syllabus/delete/'.$syllabus['id']); ?>', showAllSyllabuses)" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-original-title="<?php echo get_phrase('delete_syllabus'); ?>"> <i class="mdi mdi-window-close"></i></button>
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
