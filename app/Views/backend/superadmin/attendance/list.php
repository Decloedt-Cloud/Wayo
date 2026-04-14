<?php $school_id = school_id(); ?>
<div id="printable-section">

<div class="row" >
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="text-center">
                    <h4><?php echo get_phrase('attendance_report').' '.get_phrase('of').' '.date('F', $attendance_date); ?></h4>
                    <h5><?php echo get_phrase('class'); ?> : <?php echo db()->table('classes')->where('id', $class_id)->get()->getRow()->name ?? ''; ?></h5>
                    <h5>
                        <?php echo get_phrase('last_updated_at'); ?> :
                        <?php if (get_settings('date_of_last_updated_attendance') == ""): ?>
                            <?php echo get_phrase('not_updated_yet'); ?>
                        <?php else: ?>
                            <?php echo date('d-M-Y', get_settings('date_of_last_updated_attendance')); ?> <br>
                            <?php echo get_phrase('time'); ?> : <?php echo date('H:i:s', get_settings('date_of_last_updated_attendance')); ?>
                        <?php endif; ?>
                    </h5>
                </div>
            </div> <!-- end card-body-->
        </div>
    </div>
    <div class="col-md-4"></div>
</div>

<!-- Ajout de l'ID -->
<div class="w-100 table-responsive" >
    <table class="table table-bordered table-sm">
        <thead class="thead-dark">
            <tr style="font-size: 12px;">
                <th width="40px"><?php echo get_phrase('student'); ?> <i class="mdi mdi-arrow-down"></i> <?php echo get_phrase('date'); ?> <i class="mdi mdi-arrow-right"></i></th>
                <?php
                $number_of_days = date('m', $attendance_date) == 2 ? (date('Y', $attendance_date) % 4 ? 28 : (date('m', $attendance_date) % 100 ? 29 : (date('m', $attendance_date) % 400 ? 28 : 29))) : ((date('m', $attendance_date) - 1) % 7 % 2 ? 30 : 31);
                for ($i = 1; $i <= $number_of_days; $i++): ?>
                <th><?php echo $i; ?></th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $student_id_count = 0;
            $active_sesstion = active_session();
            $attendance_of_students = db()->table('daily_attendances')
                ->where('class_id', $class_id)
                ->where('school_id', $school_id)
                ->where('session_id', $active_sesstion)
                ->orderBy('student_id', 'asc')
                ->get()
                ->getResultArray();
            foreach ($attendance_of_students as $attendance_of_student): ?>
                <?php if (date('m', $attendance_date) == date('m', $attendance_of_student['timestamp'])): ?>
                    <?php if ($student_id_count != $attendance_of_student['student_id']): ?>
                        <tr>
                            <td><?php 
                                $studentData = db()->table('students')->where('id', $attendance_of_student['student_id'])->get()->getRowArray();
                                echo $this->user_model->get_user_details($studentData['user_id'], 'name'); 
                            ?></td>
                            <?php for ($i = 1; $i <= $number_of_days; $i++): ?>
                                <?php $date = $i . ' ' . $month . ' ' . $year; ?>
                                <?php $timestamp = strtotime($date); ?>
                                <td class="text-center">
                                    <?php $status = db()->table('daily_attendances')->where('class_id', $class_id)->where('school_id', $school_id)->where('session_id', $active_sesstion)->where('student_id', $attendance_of_student['student_id'])->where('timestamp', $timestamp)->get()->getRow()->status ?? null; ?>
                                    <?php if ($status == 1): ?>
                                        <i class="mdi mdi-circle text-success"></i>
                                    <?php elseif ($status === "0"): ?>
                                        <i class="mdi mdi-circle text-danger"></i>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endif; ?>
                    <?php $student_id_count = $attendance_of_student['student_id']; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="row d-print-none mt-3">
    <div class="col-12 text-end">
        <button onclick="printSection()" class="btn btn-primary">
            <i class="mdi mdi-printer"></i> <?php echo get_phrase('print'); ?>
        </button>
    </div>
</div>

</div>


<script>
    function printSection() {
     
    const printContent = document.getElementById('printable-section').innerHTML;
    const originalContent = document.body.innerHTML;

    // Remplacer tout le contenu de la page par la section imprimable
    document.body.innerHTML = printContent;

    // Lancer l'impression
    window.print();

    // Restaurer le contenu d'origine
    document.body.innerHTML = originalContent;


}

</script>
