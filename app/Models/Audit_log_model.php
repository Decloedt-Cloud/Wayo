<?php

namespace App\Models;

use CodeIgniter\Model;

class Audit_log_model extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'user_id',
        'user_type',
        'school_id',
        'action_type',
        'entity_type',
        'entity_id',
        'action_details',
        'ip_address',
        'user_agent',
        'created_at'
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function log_action($data)
    {
        try {
            $log_data = [
                'user_id' => $data['user_id'] ?? null,
                'user_type' => $data['user_type'] ?? null,
                'school_id' => $data['school_id'] ?? null,
                'action_type' => $data['action_type'] ?? null,
                'entity_type' => $data['entity_type'] ?? null,
                'entity_id' => $data['entity_id'] ?? null,
                'action_details' => isset($data['action_details']) ? json_encode($data['action_details']) : null,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => $this->get_user_agent(),
                'created_at' => time()
            ];

            $this->insert($log_data);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Audit log error: ' . $e->getMessage());
            return false;
        }
    }

    public function log_syllabus_create($user_id, $user_type, $school_id, $syllabus_id, $syllabus_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'action_type' => 'create',
            'entity_type' => 'syllabus',
            'entity_id' => $syllabus_id,
            'action_details' => [
                'title' => $syllabus_data['title'] ?? '',
                'class_id' => $syllabus_data['class_id'] ?? '',
                'file_name' => $syllabus_data['file'] ?? '',
                'session_id' => $syllabus_data['session_id'] ?? ''
            ]
        ]);
    }

    public function log_syllabus_delete($user_id, $user_type, $school_id, $syllabus_id, $syllabus_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'action_type' => 'delete',
            'entity_type' => 'syllabus',
            'entity_id' => $syllabus_id,
            'action_details' => [
                'title' => $syllabus_data['title'] ?? '',
                'class_id' => $syllabus_data['class_id'] ?? '',
                'file_name' => $syllabus_data['file'] ?? '',
                'file_deleted' => $syllabus_data['file_deleted'] ?? false
            ]
        ]);
    }

    public function log_syllabus_view($user_id, $user_type, $school_id, $syllabus_id, $syllabus_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'action_type' => 'view',
            'entity_type' => 'syllabus',
            'entity_id' => $syllabus_id,
            'action_details' => [
                'title' => $syllabus_data['title'] ?? '',
                'class_id' => $syllabus_data['class_id'] ?? ''
            ]
        ]);
    }

    public function log_syllabus_download($user_id, $user_type, $school_id, $syllabus_id, $syllabus_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'action_type' => 'download',
            'entity_type' => 'syllabus',
            'entity_id' => $syllabus_id,
            'action_details' => [
                'title' => $syllabus_data['title'] ?? '',
                'class_id' => $syllabus_data['class_id'] ?? '',
                'file_name' => $syllabus_data['file'] ?? ''
            ]
        ]);
    }

    public function get_syllabus_logs($school_id = null, $limit = 100, $offset = 0)
    {
        $query = $this->where('entity_type', 'syllabus');
        
        if ($school_id !== null) {
            $query = $query->where('school_id', $school_id);
        }
        
        return $query
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function get_user_syllabus_logs($user_id, $limit = 50)
    {
        return $this
            ->where('user_id', $user_id)
            ->where('entity_type', 'syllabus')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function get_entity_logs($entity_type, $entity_id, $limit = 50)
    {
        return $this
            ->where('entity_type', $entity_type)
            ->where('entity_id', $entity_id)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function log_online_admission_school($user_id, $school_id, $school_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => 'admin',
            'school_id' => $school_id,
            'action_type' => 'register',
            'entity_type' => 'school',
            'entity_id' => $school_id,
            'action_details' => [
                'school_name' => $school_data['name'] ?? '',
                'email' => $school_data['email'] ?? '',
                'country' => $school_data['country'] ?? '',
                'city' => $school_data['city'] ?? '',
                'category' => $school_data['category'] ?? '',
                'type' => $school_data['type'] ?? '',
                'phone' => $school_data['phone'] ?? '',
                'trial_period' => '14_days',
                'subscription_status' => 'trialing'
            ]
        ]);
    }

    public function log_online_admission_student($user_id, $student_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => 'student',
            'school_id' => $student_data['school_id'] ?? null,
            'action_type' => 'register',
            'entity_type' => 'student',
            'entity_id' => $user_id,
            'action_details' => [
                'name' => $student_data['name'] ?? '',
                'email' => $student_data['email'] ?? '',
                'birthday' => $student_data['birthday'] ?? '',
                'role' => 'student',
                'language' => $student_data['language'] ?? ''
            ]
        ]);
    }

    public function log_failed_registration_school($registration_data, $failure_reason)
    {
        return $this->log_action([
            'user_id' => null,
            'user_type' => 'anonymous',
            'school_id' => null,
            'action_type' => 'register_failed',
            'entity_type' => 'school',
            'entity_id' => 0,
            'action_details' => [
                'school_name' => $registration_data['name'] ?? '',
                'email' => $registration_data['email'] ?? '',
                'country' => $registration_data['country'] ?? '',
                'city' => $registration_data['city'] ?? '',
                'category' => $registration_data['category'] ?? '',
                'type' => $registration_data['type'] ?? '',
                'phone' => $registration_data['phone'] ?? '',
                'failure_reason' => $failure_reason
            ]
        ]);
    }

    public function log_failed_registration_student($registration_data, $failure_reason)
    {
        return $this->log_action([
            'user_id' => null,
            'user_type' => 'anonymous',
            'school_id' => $registration_data['school_id'] ?? null,
            'action_type' => 'register_failed',
            'entity_type' => 'student',
            'entity_id' => 0,
            'action_details' => [
                'name' => $registration_data['name'] ?? '',
                'email' => $registration_data['email'] ?? '',
                'birthday' => $registration_data['birthday'] ?? '',
                'role' => 'student',
                'language' => $registration_data['language'] ?? '',
                'failure_reason' => $failure_reason
            ]
        ]);
    }

    public function get_admission_logs($school_id = null, $limit = 100, $offset = 0)
    {
        $query = $this->where('action_type', 'register');
        
        if ($school_id !== null) {
            $query = $query->where('school_id', $school_id);
        }
        
        return $query
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function get_school_admission_logs($school_id, $limit = 50)
    {
        return $this
            ->where('school_id', $school_id)
            ->where('entity_type', 'school')
            ->where('action_type', 'register')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function log_class_create($user_id, $class_id, $class_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => session()->get('user_type'),
            'school_id' => $class_data['school_id'],
            'action_type' => 'create',
            'entity_type' => 'class',
            'entity_id' => $class_id,
            'action_details' => [
                'class_name' => $class_data['name'] ?? '',
                'price' => $class_data['price'] ?? 0,
                'start_date' => $class_data['start_date'] ?? null,
                'end_date' => $class_data['end_date'] ?? null,
                'status' => $class_data['status'] ?? 'inactive',
                'max_members' => $class_data['max_members'] ?? null
            ]
        ]);
    }

    public function log_class_update($user_id, $class_id, $class_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => session()->get('user_type'),
            'school_id' => $class_data['school_id'],
            'action_type' => 'update',
            'entity_type' => 'class',
            'entity_id' => $class_id,
            'action_details' => [
                'class_name' => $class_data['name'] ?? '',
                'price' => $class_data['price'] ?? 0,
                'start_date' => $class_data['start_date'] ?? null,
                'end_date' => $class_data['end_date'] ?? null,
                'status' => $class_data['status'] ?? 'inactive',
                'max_members' => $class_data['max_members'] ?? null
            ]
        ]);
    }

    public function log_class_delete($user_id, $class_id, $class_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => session()->get('user_type'),
            'school_id' => $class_data['school_id'],
            'action_type' => 'delete',
            'entity_type' => 'class',
            'entity_id' => $class_id,
            'action_details' => [
                'class_name' => $class_data['name'] ?? '',
                'price' => $class_data['price'] ?? 0,
                'status' => $class_data['status'] ?? 'inactive'
            ]
        ]);
    }

    public function get_class_logs($school_id = null, $limit = 100, $offset = 0)
    {
        $query = $this->where('entity_type', 'class');
        
        if ($school_id !== null) {
            $query = $query->where('school_id', $school_id);
        }
        
        return $query
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function get_school_class_logs($school_id, $limit = 50)
    {
        return $this
            ->where('school_id', $school_id)
            ->where('entity_type', 'class')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function log_event_create($user_id, $event_id, $event_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => session()->get('user_type'),
            'school_id' => $event_data['school_id'],
            'action_type' => 'create',
            'entity_type' => 'event',
            'entity_id' => $event_id,
            'action_details' => [
                'title' => $event_data['title'] ?? '',
                'starting_date' => $event_data['starting_date'] ?? null,
                'starting_time' => $event_data['starting_time'] ?? null,
                'ending_date' => $event_data['ending_date'] ?? null,
                'ending_time' => $event_data['ending_time'] ?? null,
                'description' => $event_data['description'] ?? '',
                'recurrence_type' => $event_data['recurrence_type'] ?? 'does_not_repeat',
                'recurrence_end_date' => $event_data['recurrence_end_date'] ?? null,
                'visio' => $event_data['visio'] ?? 0,
                'participants_count' => $event_data['participants_count'] ?? 0
            ]
        ]);
    }

    public function log_event_update($user_id, $event_id, $event_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => session()->get('user_type'),
            'school_id' => $event_data['school_id'],
            'action_type' => 'update',
            'entity_type' => 'event',
            'entity_id' => $event_id,
            'action_details' => [
                'title' => $event_data['title'] ?? '',
                'starting_date' => $event_data['starting_date'] ?? null,
                'starting_time' => $event_data['starting_time'] ?? null,
                'ending_date' => $event_data['ending_date'] ?? null,
                'ending_time' => $event_data['ending_time'] ?? null,
                'description' => $event_data['description'] ?? '',
                'recurrence_type' => $event_data['recurrence_type'] ?? 'does_not_repeat',
                'recurrence_end_date' => $event_data['recurrence_end_date'] ?? null,
                'visio' => $event_data['visio'] ?? 0,
                'participants_count' => $event_data['participants_count'] ?? 0
            ]
        ]);
    }

    public function log_event_delete($user_id, $event_id, $event_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => session()->get('user_type'),
            'school_id' => $event_data['school_id'],
            'action_type' => 'delete',
            'entity_type' => 'event',
            'entity_id' => $event_id,
            'action_details' => [
                'title' => $event_data['title'] ?? '',
                'starting_date' => $event_data['starting_date'] ?? null,
                'visio' => $event_data['visio'] ?? 0
            ]
        ]);
    }

    public function get_event_logs($school_id = null, $limit = 100, $offset = 0)
    {
        $query = $this->where('entity_type', 'event');
        
        if ($school_id !== null) {
            $query = $query->where('school_id', $school_id);
        }
        
        return $query
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function get_school_event_logs($school_id, $limit = 50)
    {
        return $this
            ->where('school_id', $school_id)
            ->where('entity_type', 'event')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function log_attendance_take($user_id, $attendance_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => session()->get('user_type'),
            'school_id' => $attendance_data['school_id'],
            'action_type' => 'create',
            'entity_type' => 'attendance',
            'entity_id' => 0,
            'action_details' => [
                'date' => $attendance_data['date'] ?? null,
                'class_id' => $attendance_data['class_id'] ?? null,
                'session_id' => $attendance_data['session_id'] ?? null,
                'total_students' => $attendance_data['total_students'] ?? 0,
                'present_count' => $attendance_data['present_count'] ?? 0,
                'absent_count' => $attendance_data['absent_count'] ?? 0,
                'is_update' => $attendance_data['is_update'] ?? false
            ]
        ]);
    }

    public function log_attendance_update($user_id, $attendance_data)
    {
        return $this->log_action([
            'user_id' => $user_id,
            'user_type' => session()->get('user_type'),
            'school_id' => $attendance_data['school_id'],
            'action_type' => 'update',
            'entity_type' => 'attendance',
            'entity_id' => 0,
            'action_details' => [
                'date' => $attendance_data['date'] ?? null,
                'class_id' => $attendance_data['class_id'] ?? null,
                'session_id' => $attendance_data['session_id'] ?? null,
                'total_students' => $attendance_data['total_students'] ?? 0,
                'present_count' => $attendance_data['present_count'] ?? 0,
                'absent_count' => $attendance_data['absent_count'] ?? 0
            ]
        ]);
    }

    public function get_attendance_logs($school_id = null, $limit = 100, $offset = 0)
    {
        $query = $this->where('entity_type', 'attendance');
        
        if ($school_id !== null) {
            $query = $query->where('school_id', $school_id);
        }
        
        return $query
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function get_school_attendance_logs($school_id, $limit = 50)
    {
        return $this
            ->where('school_id', $school_id)
            ->where('entity_type', 'attendance')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function log_mark_create($user_id, $user_type, $school_id, $class_id, $exam_id, $student_id = null, $mark_obtained = 0, $comment = '', $ip_address = null, $user_agent = null)
    {
        $ip_address = $ip_address ?? $this->get_client_ip();
        $user_agent = $user_agent ?? $this->get_user_agent();

        $details = [
            'class_id' => $class_id,
            'exam_id' => $exam_id,
            'mark_obtained' => $mark_obtained,
            'comment' => $comment
        ];

        if ($student_id) {
            $details['student_id'] = $student_id;
        }

        return $this->insert([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'entity_type' => 'mark',
            'entity_id' => $student_id ?? $class_id,
            'action_type' => 'create',
            'action_details' => json_encode($details),
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function log_mark_update($user_id, $user_type, $school_id, $student_id, $exam_id, $old_mark = null, $new_mark = null, $old_comment = null, $new_comment = null, $ip_address = null, $user_agent = null)
    {
        $ip_address = $ip_address ?? $this->get_client_ip();
        $user_agent = $user_agent ?? $this->get_user_agent();

        $details = [
            'student_id' => $student_id,
            'exam_id' => $exam_id,
            'old_mark' => $old_mark,
            'new_mark' => $new_mark,
            'old_comment' => $old_comment,
            'new_comment' => $new_comment
        ];

        return $this->insert([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'entity_type' => 'mark',
            'entity_id' => $student_id,
            'action_type' => 'update',
            'action_details' => json_encode($details),
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function log_grade_create($user_id, $user_type, $school_id, $grade_name, $grade_point, $mark_from, $mark_upto, $ip_address = null, $user_agent = null)
    {
        $ip_address = $ip_address ?? $this->get_client_ip();
        $user_agent = $user_agent ?? $this->get_user_agent();

        $details = [
            'grade_name' => $grade_name,
            'grade_point' => $grade_point,
            'mark_from' => $mark_from,
            'mark_upto' => $mark_upto
        ];

        return $this->insert([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'entity_type' => 'grade',
            'entity_id' => 0,
            'action_type' => 'create',
            'action_details' => json_encode($details),
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function log_grade_update($user_id, $user_type, $school_id, $grade_id, $old_data, $new_data, $ip_address = null, $user_agent = null)
    {
        $ip_address = $ip_address ?? $this->get_client_ip();
        $user_agent = $user_agent ?? $this->get_user_agent();

        $details = [
            'grade_id' => $grade_id,
            'old_data' => $old_data,
            'new_data' => $new_data
        ];

        return $this->insert([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'entity_type' => 'grade',
            'entity_id' => $grade_id,
            'action_type' => 'update',
            'action_details' => json_encode($details),
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function log_grade_delete($user_id, $user_type, $school_id, $grade_id, $grade_data, $ip_address = null, $user_agent = null)
    {
        $ip_address = $ip_address ?? $this->get_client_ip();
        $user_agent = $user_agent ?? $this->get_user_agent();

        $details = [
            'grade_id' => $grade_id,
            'deleted_data' => $grade_data
        ];

        return $this->insert([
            'user_id' => $user_id,
            'user_type' => $user_type,
            'school_id' => $school_id,
            'entity_type' => 'grade',
            'entity_id' => $grade_id,
            'action_type' => 'delete',
            'action_details' => json_encode($details),
            'ip_address' => $ip_address,
            'user_agent' => $user_agent,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_mark_audit_logs($school_id, $limit = 100)
    {
        return $this
            ->where('school_id', $school_id)
            ->where('entity_type', 'mark')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function get_grade_audit_logs($school_id, $limit = 100)
    {
        return $this
            ->where('school_id', $school_id)
            ->where('entity_type', 'grade')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function log_invoice_create($invoice_id, $invoice_data)
    {
        $details = [
            'invoice_id' => $invoice_id,
            'title' => $invoice_data['title'] ?? null,
            'total_amount' => $invoice_data['total_amount'] ?? null,
            'paid_amount' => $invoice_data['paid_amount'] ?? 0,
            'status' => $invoice_data['status'] ?? null,
            'class_id' => $invoice_data['class_id'] ?? null,
            'student_id' => $invoice_data['student_id'] ?? null,
            'session' => $invoice_data['session'] ?? null
        ];

        return $this->log_action([
            'user_id' => get_loggedin_user_id(),
            'user_type' => loggedin_user_type(),
            'school_id' => school_id(),
            'action_type' => 'create',
            'entity_type' => 'invoice',
            'entity_id' => $invoice_id,
            'action_details' => $details
        ]);
    }

    public function log_invoice_update($invoice_id, $previous_data, $new_data)
    {
        $changes = [];

        if (isset($previous_data['status']) && isset($new_data['status']) && $previous_data['status'] != $new_data['status']) {
            $changes['status'] = [
                'from' => $previous_data['status'],
                'to' => $new_data['status']
            ];
        }

        if (isset($previous_data['paid_amount']) && isset($new_data['paid_amount']) && $previous_data['paid_amount'] != $new_data['paid_amount']) {
            $changes['paid_amount'] = [
                'from' => $previous_data['paid_amount'],
                'to' => $new_data['paid_amount']
            ];
        }

        if (isset($previous_data['total_amount']) && isset($new_data['total_amount']) && $previous_data['total_amount'] != $new_data['total_amount']) {
            $changes['total_amount'] = [
                'from' => $previous_data['total_amount'],
                'to' => $new_data['total_amount']
            ];
        }

        $details = [
            'invoice_id' => $invoice_id,
            'changes' => $changes,
            'previous_data' => $previous_data,
            'new_data' => $new_data
        ];

        return $this->log_action([
            'user_id' => get_loggedin_user_id(),
            'user_type' => loggedin_user_type(),
            'school_id' => school_id(),
            'action_type' => 'update',
            'entity_type' => 'invoice',
            'entity_id' => $invoice_id,
            'action_details' => $details
        ]);
    }

    public function log_invoice_delete($invoice_id, $invoice_data)
    {
        $details = [
            'invoice_id' => $invoice_id,
            'title' => $invoice_data['title'] ?? null,
            'total_amount' => $invoice_data['total_amount'] ?? null,
            'paid_amount' => $invoice_data['paid_amount'] ?? 0,
            'status' => $invoice_data['status'] ?? null,
            'class_id' => $invoice_data['class_id'] ?? null,
            'student_id' => $invoice_data['student_id'] ?? null,
            'session' => $invoice_data['session'] ?? null
        ];

        return $this->log_action([
            'user_id' => get_loggedin_user_id(),
            'user_type' => loggedin_user_type(),
            'school_id' => school_id(),
            'action_type' => 'delete',
            'entity_type' => 'invoice',
            'entity_id' => $invoice_id,
            'action_details' => $details
        ]);
    }

    public function get_invoice_audit_logs($school_id, $limit = 100)
    {
        return $this
            ->where('school_id', $school_id)
            ->where('entity_type', 'invoice')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    private function get_client_ip()
    {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    private function get_user_agent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : 'Unknown';
    }
}
