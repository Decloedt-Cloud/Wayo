<?php $check_data = $this->db->get_where('grades', array('school_id' => school_id(), 'session' => active_session()));
if($check_data->num_rows() > 0):?>
<div class="modern-table-wrapper">
    <table class="table modern-table table-responsive-sm" width="100%">
        <thead>
            <tr>
                <th><?php echo get_phrase('grade'); ?></th>
                <th><?php echo get_phrase('grade_point'); ?></th>
                <th><?php echo get_phrase('mark_from'); ?></th>
                <th><?php echo get_phrase('mark_upto'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $grades = $this->db->get_where('grades', array('school_id' => school_id(), 'session' => active_session()))->result_array();
            foreach($grades as $grade){
        ?>
        <tr>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-box-sm" style="width:32px; height:32px; background:var(--primary-lighter); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--primary);">
                        <i class="mdi mdi-school-outline"></i>
                    </div>
                    <span><?php echo $grade['name']; ?></span>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-star-circle-outline text-warning"></i>
                    <span class="fw-bold"><?php echo $grade['grade_point']; ?></span>
                </div>
            </td>
            <td><?php echo $grade['mark_from']; ?></td>
            <td><?php echo $grade['mark_upto']; ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php else: ?>
    <div class="empty-state">
        <img src="<?php echo base_url('assets/backend/images/empty_box.png'); ?>" alt="No Data" />
        <p><?php echo get_phrase('no_grades_found'); ?></p>
    </div>
<?php endif; ?>
