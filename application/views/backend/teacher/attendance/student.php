<style>
    /* Premium Table Styling */
    .exp-table-wrapper {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        margin-top: 1.5rem;
    }

    .exp-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .exp-table th {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
    }

    .exp-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        vertical-align: middle;
    }

    .exp-table tr:last-child td {
        border-bottom: none;
    }

    .exp-table tr:hover td {
        background-color: #f8fafc;
    }

    /* Avatar & Name */
    .exp-user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .exp-user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #64748b;
        font-size: 0.9rem;
    }

    .exp-user-name {
        font-weight: 600;
        color: #0f172a;
    }

    /* Toggle Switch for Attendance */
    .exp-attendance-toggle {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        position: relative;
    }

    .exp-attendance-radio {
        display: none;
    }

    .exp-attendance-label {
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        user-select: none;
    }

    .exp-attendance-label:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .exp-attendance-radio:checked + .exp-attendance-label.present {
        background: #10b981;
        color: white;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .exp-attendance-radio:checked + .exp-attendance-label.absent {
        background: #ef4444;
        color: white;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    /* Action Buttons */
    .exp-actions-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .exp-action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        border: 2px solid transparent;
        cursor: pointer;
        text-decoration: none !important;
    }

    .exp-btn-present-all {
        background: #ecfdf5;
        color: #059669;
        border-color: #d1fae5;
    }

    .exp-btn-present-all:hover {
        background: #d1fae5;
        transform: translateY(-2px);
    }

    .exp-btn-absent-all {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fee2e2;
    }

    .exp-btn-absent-all:hover {
        background: #fee2e2;
        transform: translateY(-2px);
    }

    /* Responsive Media Queries */
    @media (max-width: 768px) {
        .exp-attendance-toggle {
            flex-direction: column;
            gap: 4px;
            padding: 2px;
        }
        
        .exp-attendance-label {
            padding: 6px 12px;
            font-size: 0.8rem;
            justify-content: center;
            min-width: 80px;
        }
        
        .exp-actions-row {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .exp-action-btn {
            font-size: 0.85rem;
            padding: 0.75rem;
        }
        
        .exp-user-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .exp-user-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }
        
        .exp-table th,
        .exp-table td {
            padding: 0.75rem 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .exp-attendance-toggle {
            width: 100%;
        }
        
        .exp-attendance-label {
            padding: 5px 10px;
            font-size: 0.75rem;
            min-width: 70px;
        }
        
        .exp-table-wrapper {
            margin-top: 1rem;
            border-radius: 12px;
        }
        
        .exp-table th {
            font-size: 0.7rem;
            padding: 0.5rem 0.75rem;
        }
        
        .exp-table td {
            padding: 0.5rem 0.75rem;
        }
    }

</style>

<?php $check_permission = has_permission($class_id, 'attendance'); ?>
<?php if ($check_permission): ?>
    <?php $school_id = school_id(); ?>

    <!-- Hidden CSRF Token for AJAX handling -->
    <input type="hidden" class="fresh-csrf-token" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>" />

    <div class="exp-actions-row">
        <a href="javascript:void(0);" class="exp-action-btn exp-btn-present-all" onclick="present_all()">
            <i class="mdi mdi-check-circle"></i>
            <?php echo get_phrase('present_all'); ?>
        </a>
        <a href="javascript:void(0);" class="exp-action-btn exp-btn-absent-all" onclick="absent_all()">
            <i class="mdi mdi-alert-circle"></i>
            <?php echo get_phrase('absent_all'); ?>
        </a>
    </div>

    <div class="exp-table-wrapper">
        <table class="exp-table">
            <thead>
                <tr>
                    <th><?php echo get_phrase('student'); ?></th>
                    <th style="text-align: center;"><?php echo get_phrase('status'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $enrols = $this->db->get_where('enrols', array('class_id' => $class_id, 'school_id' => $school_id, 'session' => active_session()))->result_array(); ?>
                <?php foreach($enrols as $enroll): ?>
                <tr>
                    <td>
                        <div class="exp-user-info">
                            <?php 
                                $student_details = $this->db->get_where('students', array('id' => $enroll['student_id']))->row_array();
                                $user_details = $this->db->get_where('users', array('id' => $student_details['user_id']))->row_array();
                                $initials = strtoupper(substr($user_details['name'], 0, 1));
                            ?>
                            <div class="exp-user-avatar">
                                <?php echo $initials; ?>
                            </div>
                            <span class="exp-user-name">
                                <?php echo $user_details['name']; ?>
                            </span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <input type="hidden" name="student_id[]" value="<?php echo $enroll['student_id']; ?>">
                        
                        <?php 
                            $update_attendance = $this->db->get_where('daily_attendances', array('timestamp' => $attendance_date, 'class_id' => $class_id, 'session_id' => active_session(), 'school_id' => $school_id, 'student_id' => $enroll['student_id'])); 
                            $status = -1;
                            if($update_attendance->num_rows() > 0) {
                                $row = $update_attendance->row();
                                $status = $row->status;
                                echo '<input type="hidden" name="attendance_id[]" value="'.$row->id.'">';
                            }
                        ?>

                        <div class="exp-attendance-toggle">
                            <!-- Present Option -->
                            <input type="radio" 
                                   id="status-p-<?php echo $enroll['student_id']; ?>" 
                                   name="status-<?php echo $enroll['student_id']; ?>" 
                                   value="1" 
                                   class="exp-attendance-radio present-radio" 
                                   <?php if($status == 1) echo 'checked'; ?> 
                                   required>
                            <label class="exp-attendance-label present" for="status-p-<?php echo $enroll['student_id']; ?>">
                                <i class="mdi mdi-check"></i> <?php echo get_phrase('present'); ?>
                            </label>

                            <!-- Absent Option -->
                            <input type="radio" 
                                   id="status-a-<?php echo $enroll['student_id']; ?>" 
                                   name="status-<?php echo $enroll['student_id']; ?>" 
                                   value="0" 
                                   class="exp-attendance-radio absent-radio" 
                                   <?php if($status != 1) echo 'checked'; ?> 
                                   required>
                            <label class="exp-attendance-label absent" for="status-a-<?php echo $enroll['student_id']; ?>">
                                <i class="mdi mdi-close"></i> <?php echo get_phrase('absent'); ?>
                            </label>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script type="text/javascript">
        function present_all() {
            $(".present-radio").prop('checked', true);
        }

        function absent_all() {
            $(".absent-radio").prop('checked',true);
        }
    </script>

<?php else: ?>
    <div class="col-md-12 text-center mt-3">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading"><?php echo get_phrase('access_denied'); ?>!</h4>
            <hr>
            <p class="mb-0"><?php echo get_phrase('sorry_you_do_not_have_permission_to_take_attendance_for_this_class') ?></p>
        </div>
    </div>
<?php endif; ?>