<style>
    .exp-report-header {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .exp-report-bg-icon {
        position: absolute;
        right: -20px;
        top: -20px;
        font-size: 10rem;
        color: rgba(99, 102, 241, 0.05);
        transform: rotate(15deg);
        pointer-events: none;
    }

    .exp-report-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .exp-report-title h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .exp-report-title .class-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #eff6ff;
        color: #3b82f6;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-top: 1rem;
    }

    .exp-report-stats {
        display: flex;
        gap: 2rem;
    }

    .exp-stat-item {
        text-align: right;
    }

    .exp-stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 600;
        display: block;
        margin-bottom: 0.25rem;
    }

    .exp-stat-value {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .exp-table-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .exp-report-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .exp-report-table th, 
    .exp-report-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }

    .exp-report-table th {
        background: #f8fafc;
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e2e8f0;
        text-align: center;
        white-space: nowrap;
    }

    .exp-report-table th:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        background: #f8fafc;
        z-index: 10;
        border-right: 2px solid #e2e8f0;
    }

    .exp-report-table td:first-child {
        position: sticky;
        left: 0;
        background: white;
        z-index: 10;
        border-right: 2px solid #e2e8f0;
        font-weight: 600;
        color: #1e293b;
    }

    .exp-report-table tr:hover td {
        background-color: #f8fafc;
    }
    
    .exp-report-table tr:hover td:first-child {
        background-color: #f8fafc;
    }

    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        transition: transform 0.2s;
    }

    .status-dot:hover {
        transform: scale(1.2);
    }

    .status-dot.present {
        background: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    }

    .status-dot.absent {
        background: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
    }

    .exp-print-btn {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s;
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
    }

    .exp-print-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
    }
</style>

<?php $school_id = school_id(); ?>
<div id="printable-section">
    
    <div class="exp-report-header">
        <i class="mdi mdi-calendar-check exp-report-bg-icon"></i>
        <div class="exp-report-content">
            <div class="exp-report-title">
                <h4><?php echo get_phrase('attendance_report'); ?> - <?php echo date('F', $attendance_date); ?></h4>
                <div class="class-badge">
                    <i class="mdi mdi-google-classroom"></i>
                    <?php echo get_phrase('class'); ?> : <?php echo db()->table('classes')->where('id', $class_id)->get()->getRow()->name ?? ''; ?>
                </div>
            </div>
            
            <div class="exp-report-stats">
                <div class="exp-stat-item">
                    <span class="exp-stat-label"><?php echo get_phrase('last_updated'); ?></span>
                    <span class="exp-stat-value">
                        <i class="mdi mdi-clock-outline"></i>
                        <?php if (get_settings('date_of_last_updated_attendance') == ""): ?>
                            <?php echo get_phrase('not_updated_yet'); ?>
                        <?php else: ?>
                            <?php echo date('d M Y', get_settings('date_of_last_updated_attendance')); ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="exp-table-container">
        <div class="table-responsive">
            <table class="exp-report-table">
                <thead>
                    <tr>
                        <th width="200px"><?php echo get_phrase('student'); ?></th>
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
                                                <span class="status-dot present" title="Present"></span>
                                            <?php elseif ($status === "0"): ?>
                                                <span class="status-dot absent" title="Absent"></span>
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
    </div>
</div>

<div class="row d-print-none mt-4">
    <div class="col-12 text-end">
        <button onclick="printSection()" class="exp-print-btn">
            <i class="mdi mdi-printer"></i> <?php echo get_phrase('print_report'); ?>
        </button>
    </div>
</div>

<script>
    function printSection() {
        const printContent = document.getElementById('printable-section').innerHTML;
        const originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        // Reload to restore events
        location.reload(); 
    }
</script>