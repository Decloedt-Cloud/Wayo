<?php $school_id = school_id(); ?>
<div class="row" style="margin-bottom: 10px; width: 100%;">
    <div class="col-6"><a href="javascript:" class="btn btn-sm btn-secondary" onclick="present_all()"><?php echo get_phrase('present_all'); ?></a></div>
    <div class="col-6"><a href="javascript:" class="btn btn-sm btn-secondary float-end" onclick="absent_all()"><?php echo get_phrase('absent_all'); ?></a></div>
</div>

<div class="table-responsive-sm row col-md-12" style="padding-right: 0px;">
    <table class="table table-bordered table-centered mb-0">
        <thead>
            <tr>
                <th><?php echo get_phrase('name'); ?></th>
                <th><?php echo get_phrase('status'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $enrols = db()->table('enrols')->where('class_id', $class_id)->where('school_id', $school_id)->where('session', active_session())->get()->getResultArray(); ?>
                <?php foreach($enrols as $enroll): ?>
                <tr>
                    <td>
                        <?php echo $this->user_model->get_user_details(db()->table('students')->where('id', $enroll['student_id'])->get()->getRow()->user_id ?? '', 'name'); ?>
                    </td>
                    <td>
                        <input type="hidden" name="student_id[]" value="<?php echo $enroll['student_id']; ?>">
                        <div class="custom-control custom-radio">
                            <?php $update_attendance = db()->table('daily_attendances')->where('timestamp', $attendance_date)->get()->getResultArray(); ?>
                            <?php if(count($update_attendance) > 0): ?>
                                <?php $row = $update_attendance[0]; ?>
                                <input type="hidden" name="attendance_id[]" value="<?php echo $row->id; ?>">
                                <input type="radio" id="" name="status-<?php echo $enroll['student_id']; ?>" value="1" class="present" <?php if($row->status == 1) echo 'checked'; ?> required> <?php echo get_phrase('present'); ?> &nbsp;
                                <input type="radio" id="" name="status-<?php echo $enroll['student_id']; ?>" value="0" class="absent" <?php if($row->status != 1) echo 'checked'; ?> required> <?php echo get_phrase('absent'); ?>
                            <?php else: ?>
                                <input type="radio" id="" name="status-<?php echo $enroll['student_id']; ?>" value="1" class="present" required> <?php echo get_phrase('present'); ?> &nbsp;
                                <input type="radio" id="" name="status-<?php echo $enroll['student_id']; ?>" value="0" class="absent" required> <?php echo get_phrase('absent'); ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function present_all() {
        $(".present").prop('checked', true);
    }

    function absent_all() {
        $(".absent").prop('checked',true);
    }
</script>
