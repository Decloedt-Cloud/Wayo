
<style>
    .attendance-summary {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }
    
    .attendance-summary::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: white;
        opacity: 0.1;
        border-radius: 50%;
        transform: translate(30%, -30%);
    }

    .attendance-summary h4 {
        color: white;
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-family: 'Outfit', sans-serif;
        position: relative;
        z-index: 1;
    }
    .attendance-summary h5 {
        color: rgba(255,255,255,0.9);
        font-size: 1rem;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    
    .modern-table-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow-x: auto;
    }
    
    .modern-table {
        width: 100%;
        margin-bottom: 0;
    }
    .modern-table thead th {
        background: var(--bg-main);
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }
    .modern-table tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-dark);
        font-weight: 500;
        font-size: 0.9rem;
    }
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }
    .modern-table tbody tr:hover td {
        background-color: var(--bg-main);
    }
    
    .status-icon {
        font-size: 0.8rem;
    }
</style>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="attendance-summary">
        <h4><?php echo get_phrase('attendance_report').' '.get_phrase('of').' '.date('F', $attendance_date); ?></h4>
        <h5><?php
            $classRow = db()->table('classes')->where('id', $class_id)->get()->getRow();
            echo get_phrase('class'); ?> : <?php echo $classRow->name ?? '';
        ?></h5>
        <h5>
        <?php echo get_phrase('last_updated_at'); ?> :
        <?php if (get_settings('date_of_last_updated_attendance') == ""): ?>
            <?php echo get_phrase('not_updated_yet'); ?>
        <?php else: ?>
            <?php echo date('d-M-Y', get_settings('date_of_last_updated_attendance')); ?> 
            <span class="mx-2">•</span>
            <?php echo date('H:i:s', get_settings('date_of_last_updated_attendance')); ?>
        <?php endif; ?>
        </h5>
    </div>
  </div>
</div>

<div class="modern-table-wrapper">
    <table class="table modern-table table-responsive-sm">
        <thead>
            <tr>
                <th width="200px" style="position: sticky; left: 0; background: var(--bg-main); z-index: 10;"><?php echo get_phrase('student'); ?></th>
                <?php
                $number_of_days = date('m', $attendance_date) == 2 ? (date('Y', $attendance_date) % 4 ? 28 : (date('m', $attendance_date) % 100 ? 29 : (date('m', $attendance_date) % 400 ? 28 : 29))) : ((date('m', $attendance_date) - 1) % 7 % 2 ? 30 : 31);
                for ($i = 1; $i <= $number_of_days; $i++): ?>
                <th class="text-center" style="min-width: 35px;"><?php echo $i; ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
        <?php
        $student_data = $this->user_model->get_logged_in_student_details($school_id);
        $student_data = is_array($student_data) ? $student_data : [];
        $logged_student_id = (int) ($student_data['student_id'] ?? 0);
        $student_id_count = 0;
        $active_sesstion = active_session();
        $attendance_of_students = [];
        if ($logged_student_id > 0) {
            $attendance_of_students = db()->table('daily_attendances')
                ->where('class_id', $class_id)
                ->where('student_id', $logged_student_id)
                ->where('school_id', $school_id)
                ->where('session_id', $active_sesstion)
                ->orderBy('student_id', 'asc')
                ->get()
                ->getResultArray();
        }


        foreach($attendance_of_students as $attendance_of_student){  ?>
                
            <?php if ((int) ($attendance_of_student['student_id'] ?? 0) === $logged_student_id): ?>
            <?php if(date('m', $attendance_date) == date('m', $attendance_of_student['timestamp'])): ?>
                <?php if($student_id_count != $attendance_of_student['student_id']): ?>
                <tr>
                    <td style="position: sticky; left: 0; background: white; z-index: 5; border-right: 1px solid var(--border-color);">
                        <?php
                            $studentRow = db()->table('students')->where('id', $attendance_of_student['student_id'])->get()->getRow();
                            $studentName = '';
                            if (!empty($studentRow->user_id) && isset($this->user_model)) {
                                $studentName = $this->user_model->get_user_details($studentRow->user_id, 'name');
                            }
                            echo esc((string) $studentName);
                        ?>
                    </td>
                    <?php for ($i = 1; $i <= $number_of_days; $i++): ?>
                    <?php $date = $i.' '.$month.' '.$year; ?>
                    <?php $timestamp = strtotime($date); ?>
                    <td class="text-center">
                        <?php
                            $statusRow = db()->table('daily_attendances')->where('class_id', $class_id)->where('school_id', $school_id)->where('session_id', $active_sesstion)->where('student_id', $attendance_of_student['student_id'])->where('timestamp', $timestamp)->get()->getRow();
                            $status = $statusRow->status ?? null;
                        ?>
                        <?php if($status == 1){ ?>
                        <i class="mdi mdi-circle text-success status-icon"></i>
                        <?php }elseif($status === "0"){ ?>
                        <i class="mdi mdi-circle text-danger status-icon"></i>
                        <?php } ?>
                    </td>
                    <?php endfor; ?>
                </tr>
                <?php endif; ?>
                <?php $student_id_count = $attendance_of_student['student_id']; ?>
            <?php endif; ?>
            <?php endif; ?>
        <?php } ?>
        </tbody>
    </table>
</div>

<div class="row d-print-none mt-4">
  <div class="col-12 text-end">
      <a href="javascript:window.print()" class="modern-btn">
          <i class="mdi mdi-printer"></i> <?php echo get_phrase('print'); ?>
      </a>
  </div>
</div>
