<?php

namespace App\Models;

use CodeIgniter\Model;

class Crud_model extends Model
{
    protected $school_id;
    protected $active_session;
    protected $request;
    protected $table = 'classes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'school_id', 'statut', 'price', 'date_debut', 'date_fin', 'nombre_max_membre'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    public function __construct()
    {
        parent::__construct();
        $this->request = service('request');
        
        if (function_exists('school_id')) {
            $this->school_id = school_id();
        }
        if (function_exists('active_session')) {
            $this->active_session = active_session();
        }
    }

    public function get_classes($id = "")
    {
        $this->where('school_id', $this->school_id);
        if ($id > 0) {
            $this->where('id', $id);
        }
        return $this->findAll();
    }

    public function get_school_classes_count($school_id)
    {
        return $this->where('school_id', $school_id)
                    ->where('statut', 'active')
                    ->countAllResults();
    }

    public function get_school_classes($school_id)
    {
        return $this->where('school_id', $school_id)
                    ->where('statut', 'active')
                    ->findAll();
    }

    public function class_create()
    {
        $name = html_escape((string) $this->request->getPost('name'));
        if ($name === '') {
            return [
                'status' => false,
                'notification' => get_phrase('please_provide_class_name'),
            ];
        }

        $priceInput = (string) $this->request->getPost('price');
        $isFree = (string) $this->request->getPost('is_free') === '1';
        $price = $isFree ? 0 : (is_numeric($priceInput) ? (float) $priceInput : 0);

        $status = html_escape((string) $this->request->getPost('status'));
        if ($status !== 'active' && $status !== 'inactive') {
            $status = 'inactive';
        }

        $startDate = html_escape((string) $this->request->getPost('start_date'));
        $endDate = html_escape((string) $this->request->getPost('end_date'));
        $maxMembersInput = (string) $this->request->getPost('max_members');
        $maxMembers = is_numeric($maxMembersInput) ? (int) $maxMembersInput : null;

        $data = [
            'name' => $name,
            'school_id' => $this->school_id ?? school_id(),
            'price' => $price,
            'date_debut' => $startDate !== '' ? $startDate : null,
            'date_fin' => $endDate !== '' ? $endDate : null,
            'statut' => $status,
            'nombre_max_membre' => $maxMembers,
        ];

        $inserted = \db()->table('classes')->insert($data);
        if (!$inserted) {
            return [
                'status' => false,
                'notification' => get_phrase('an_error_occurred_while_saving_data'),
            ];
        }

        $class_id = \db()->insertID();
        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_class_create(
            session()->get('user_id'),
            $class_id,
            [
                'name' => $name,
                'price' => $price,
                'start_date' => $startDate !== '' ? $startDate : null,
                'end_date' => $endDate !== '' ? $endDate : null,
                'status' => $status,
                'max_members' => $maxMembers,
                'school_id' => $this->school_id ?? school_id()
            ]
        );

        return [
            'status' => true,
            'notification' => get_phrase('class_added_successfully'),
        ];
    }

    // START EVENT CALENDAR section
    public function event_calendar_create()
    {
        $data = [
            'title' => html_escape((string) $this->request->getPost('title')),
            'starting_date' => $this->request->getPost('starting_date'),
            'ending_date' => $this->request->getPost('ending_date'),
            'content' => $this->request->getPost('content'),
            'school_id' => $this->school_id ?? school_id(),
            'session' => $this->active_session ?? active_session(),
        ];

        \db()->table('announcement')->insert($data);

        return [
            'status' => true,
            'notification' => get_phrase('event_has_been_added_successfully'),
        ];
    }

    public function take_attendance()
    {
        $students = $this->request->getPost('student_id');
        if (!is_array($students) || empty($students)) {
            return [
                'status' => false,
                'notification' => get_phrase('no_student_selected'),
            ];
        }

        $date = (string) $this->request->getPost('date');
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            $timestamp = strtotime(date('Y-m-d'));
        }

        $class_id = (int) $this->request->getPost('class_id');
        $school_id = $this->school_id ?? school_id();
        $session_id = $this->active_session ?? active_session();
        $current_user_id = (int) (session()->get('user_id') ?? 0);
        $is_teacher = (session()->get('teacher_login') == 1) || strtolower((string) (session()->get('user_type') ?? session()->get('role') ?? '')) === 'teacher';

        if ($class_id <= 0 || (int) $school_id <= 0 || (int) $session_id <= 0) {
            return [
                'status' => false,
                'notification' => get_phrase('invalid_request'),
            ];
        }

        // Ensure class belongs to active school.
        $class_exists = \db()->table('classes')
            ->where('id', $class_id)
            ->where('school_id', (int) $school_id)
            ->countAllResults() > 0;
        if (!$class_exists) {
            return [
                'status' => false,
                'notification' => get_phrase('invalid_request'),
            ];
        }

        // Strict teacher permission check on server side.
        if ($is_teacher) {
            $teacher_row = \db()->table('teachers')
                ->select('id')
                ->where('user_id', $current_user_id)
                ->where('school_id', (int) $school_id)
                ->get()
                ->getRowArray();
            $teacher_id = (int) ($teacher_row['id'] ?? 0);
            if ($teacher_id <= 0) {
                return [
                    'status' => false,
                    'notification' => get_phrase('action_not_allowed'),
                ];
            }

            $has_attendance_permission = \db()->table('teacher_permissions')
                ->where('teacher_id', $teacher_id)
                ->where('class_id', $class_id)
                ->where('attendance', 1)
                ->countAllResults() > 0;
            if (!$has_attendance_permission) {
                return [
                    'status' => false,
                    'notification' => get_phrase('action_not_allowed'),
                ];
            }
        }

        $students = array_values(array_unique(array_map('intval', $students)));
        $students = array_values(array_filter($students, static fn ($id) => $id > 0));
        if (empty($students)) {
            return [
                'status' => false,
                'notification' => get_phrase('no_student_selected'),
            ];
        }

        // Verify every posted student belongs to class/school/session.
        $valid_student_rows = \db()->table('enrols')
            ->select('student_id')
            ->where('class_id', $class_id)
            ->where('school_id', (int) $school_id)
            ->where('session', (int) $session_id)
            ->whereIn('student_id', $students)
            ->get()
            ->getResultArray();
        $valid_students = array_values(array_unique(array_map('intval', array_column($valid_student_rows, 'student_id'))));
        if (count($valid_students) !== count($students)) {
            return [
                'status' => false,
                'notification' => get_phrase('invalid_request'),
            ];
        }

        $data = [
            'timestamp' => $timestamp,
            'class_id' => $class_id,
            'school_id' => $school_id,
            'session_id' => $session_id,
        ];

        $existingCount = \db()->table('daily_attendances')
            ->where('timestamp', $timestamp)
            ->where('class_id', $class_id)
            ->where('session_id', $session_id)
            ->where('school_id', $school_id)
            ->countAllResults();

        $attendance_ids = $this->request->getPost('attendance_id');
        $present_count = 0;
        $absent_count = 0;
        $total_students = 0;
        
        foreach ($students as $key => $student) {
            $student_id = (int) $student;
            if ($student_id <= 0) {
                continue;
            }

            $row = $data;
            $row['status'] = (int) ($this->request->getPost('status-' . $student_id) ?? 0);
            $row['student_id'] = $student_id;

            if ($existingCount > 0) {
                $attendance_id = is_array($attendance_ids) ? (int) ($attendance_ids[$key] ?? 0) : 0;
                if ($attendance_id > 0) {
                    \db()->table('daily_attendances')
                        ->where('id', $attendance_id)
                        ->where('class_id', $class_id)
                        ->where('school_id', (int) $school_id)
                        ->where('session_id', (int) $session_id)
                        ->where('student_id', $student_id)
                        ->update($row);
                } else {
                    \db()->table('daily_attendances')
                        ->where('timestamp', $timestamp)
                        ->where('class_id', $class_id)
                        ->where('session_id', $session_id)
                        ->where('school_id', $school_id)
                        ->where('student_id', $student_id)
                        ->update($row);
                }
            } else {
                \db()->table('daily_attendances')->insert($row);
            }
            
            $total_students++;
            if ($row['status'] == 1) {
                $present_count++;
            } else {
                $absent_count++;
            }
        }

        $audit_log_model = new \App\Models\Audit_log_model();
        if ($existingCount > 0) {
            $audit_log_model->log_attendance_update(
                session()->get('user_id'),
                [
                    'date' => date('Y-m-d', $timestamp),
                    'class_id' => $class_id,
                    'session_id' => $session_id,
                    'school_id' => $school_id,
                    'total_students' => $total_students,
                    'present_count' => $present_count,
                    'absent_count' => $absent_count
                ]
            );
        } else {
            $audit_log_model->log_attendance_take(
                session()->get('user_id'),
                [
                    'date' => date('Y-m-d', $timestamp),
                    'class_id' => $class_id,
                    'session_id' => $session_id,
                    'school_id' => $school_id,
                    'total_students' => $total_students,
                    'present_count' => $present_count,
                    'absent_count' => $absent_count,
                    'is_update' => false
                ]
            );
        }

        try {
            $settingsModel = model('Settings_model');
            if ($settingsModel && method_exists($settingsModel, 'last_updated_attendance_data')) {
                $settingsModel->last_updated_attendance_data();
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to update attendance metadata: ' . $e->getMessage());
        }

        return [
            'status' => true,
            'notification' => get_phrase('attendance_updated_successfully'),
        ];
    }

    public function event_calendar_update($param1 = '')
    {
        $data = [
            'title' => html_escape((string) $this->request->getPost('title')),
            'starting_date' => $this->request->getPost('starting_date'),
            'ending_date' => $this->request->getPost('ending_date'),
            'content' => $this->request->getPost('content'),
        ];

        \db()->table('announcement')->where('id', $param1)->update($data);

        return json_encode([
            'status' => true,
            'notification' => get_phrase('event_has_been_updated_successfully'),
        ]);
    }

    public function event_calendar_delete($param1 = '')
    {
        \db()->table('announcement')->where('id', $param1)->delete();

        return json_encode([
            'status' => true,
            'notification' => get_phrase('event_has_been_deleted_successfully'),
        ]);
    }

    public function all_events()
    {
        $user_id = (int) session()->get('user_id');
        if ($user_id <= 0) {
            return json_encode([]);
        }

        $user = \db()->table('users')
            ->select('id, role, school_id')
            ->where('id', $user_id)
            ->get()
            ->getRowArray();

        if (empty($user)) {
            return json_encode([]);
        }

        $role = (string) ($user['role'] ?? '');
        $school_id = (int) ($user['school_id'] ?? 0);
        $active_session = $this->active_session ?? active_session();

        if ($role === 'student') {
            $school_id = (int) ($this->school_id ?? school_id());
            $student = \db()->table('students')
                ->select('id')
                ->where('user_id', $user_id)
                ->where('school_id', $school_id)
                ->get()
                ->getRowArray();

            if (empty($student)) {
                return json_encode([]);
            }

            $enrols_count = \db()->table('enrols')
                ->where('student_id', (int) $student['id'])
                ->where('school_id', $school_id)
                ->countAllResults();

            if ($enrols_count <= 0) {
                return json_encode([]);
            }
        }

        $event_calendars = \db()->table('announcement')
            ->where('school_id', $school_id)
            ->where('session', $active_session)
            ->get()
            ->getResultArray();

        return json_encode($event_calendars);
    }
    // END EVENT CALENDAR section
    
    public function get_current_month_events()
    {
        $school_id = $this->school_id ?? school_id();
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        
        try {
            $query = \db()->table('event_calendars')
                ->where('school_id', $school_id)
                ->where('starting_date <=', $end_date)
                ->groupStart()
                    ->where('ending_date >=', $start_date)
                    ->orWhere('ending_date', null)
                ->groupEnd()
                ->get();

            if ($query === false) {
                return [];
            }

            return $query->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
    
    public function get_todays_attendance()
    {
        $school_id = $this->school_id ?? school_id();
        $timestamp = strtotime(date('Y-m-d'));
        
        try {
            return (int) \db()->table('daily_attendances')
                ->where('school_id', $school_id)
                ->where('timestamp', $timestamp)
                ->countAllResults();
        } catch (\Throwable $e) {
            return 0;
        }
    }
    
    public function get_invoice_by_date_range($date_from, $date_to, $payment_status = 'all', $class_id = 'all')
    {
        $school_id = $this->school_id ?? school_id();
        
        try {
            $builder = \db()->table('invoices');
            
            if ($date_from && $date_to) {
                $builder->where("created_at >=", $date_from);
                $builder->where("created_at <=", $date_to);
            }
            
            if ($payment_status && $payment_status != 'all') {
                $builder->where('status', $payment_status);
            }
            
            if ($class_id && $class_id != 'all') {
                $builder->where('class_id', $class_id);
            }
            
            $query = $builder->where('school_id', $school_id)->get();
            if ($query === false) {
                return [];
            }

            return $query->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
    
    public function get_expense($date_from = null, $date_to = null, $expense_category_id = null)
    {
        $school_id = $this->school_id ?? school_id();
        $active_session = $this->active_session ?? active_session();

        try {
            $builder = \db()->table('expenses');

            if ($date_from && $date_to) {
                $builder->where('date >=', (int) $date_from);
                $builder->where('date <=', (int) $date_to);
            }

            if (!empty($expense_category_id) && $expense_category_id !== 'all') {
                $builder->where('expense_category_id', (int) $expense_category_id);
            }

            $builder->where('school_id', $school_id);
            $builder->where('session', $active_session);

            $query = $builder->get();
            if ($query === false) {
                return [];
            }

            return $query->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Expense query error: ' . $e->getMessage());
            return [];
        }
    }
    
    public function get_invoice_by_date_range_superadmin($date_from, $date_to, $class_id = 'all', $payment_status = 'all')
    {
        try {
            $builder = \db()->table('invoices');
            
            if ($date_from && $date_to) {
                $builder->where("created_at >=", $date_from);
                $builder->where("created_at <=", $date_to);
            }
            
            if ($payment_status && $payment_status != 'all') {
                $builder->where('status', $payment_status);
            }
            
            if ($class_id && $class_id != 'all') {
                $builder->where('class_id', $class_id);
            }
            
            $result = $builder->get()->getResultArray();
            
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public function get_school_details_by_id($school_id)
    {
        try {
            $result = \db()->table('schools')
                ->where('id', $school_id)
                ->get()
                ->getRowArray();
            
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function class_update($param1 = '')
    {
        $data['name'] = html_escape($this->request->getPost('name'));
        $data['price'] = html_escape($this->request->getPost('price'));
        $start_date = html_escape($this->request->getPost('start_date'));
        $end_date = html_escape($this->request->getPost('end_date'));
        $data['date_debut'] = !empty($start_date) ? $start_date : null;
        $data['date_fin'] = !empty($end_date) ? $end_date : null;
        $data['statut'] = html_escape($this->request->getPost('status'));

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $dir = 'uploads/class/';
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
            $file_base = md5(rand(10000000, 20000000));
            $destination = $dir . $file_base;
            $result = $this->compress_and_save_image($_FILES['photo']['tmp_name'], $destination, 800, 800, 90);
            if ($result === false) {
                return json_encode(['status' => false, 'notification' => get_phrase('image_processing_failed_please_try_another_image')]);
            }
            $data['photo'] = $file_base . '.jpg';
        }

        $data['nombre_max_membre'] = html_escape($this->request->getPost('max_members'));
        \db()->table('classes')->where('id', $param1)->update($data);

        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_class_update(
            session()->get('user_id'),
            $param1,
            [
                'name' => $data['name'],
                'price' => $data['price'],
                'start_date' => $data['date_debut'],
                'end_date' => $data['date_fin'],
                'status' => $data['statut'],
                'max_members' => $data['nombre_max_membre'],
                'school_id' => $this->school_id ?? school_id()
            ]
        );

        return [
            'status' => true,
            'notification' => get_phrase('class_updated_successfully')
        ];
    }

    public function class_delete($param1 = '')
    {
        if (empty($param1)) {
            return [
                'status' => false,
                'notification' => get_phrase('invalid_class_id')
            ];
        }

        $class = \db()->table('classes')->where('id', $param1)->get()->getRowArray();
        if (!$class) {
            return [
                'status' => false,
                'notification' => get_phrase('class_not_found')
            ];
        }

        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_class_delete(
            session()->get('user_id'),
            $param1,
            [
                'name' => $class['name'],
                'price' => $class['price'],
                'status' => $class['statut'],
                'school_id' => $class['school_id']
            ]
        );

        \db()->transStart();

        $tablesToDelete = [
            'rooms',
            'enrols',
            'subjects',
            'class_routine',
            'teacher_permissions'
        ];

        foreach ($tablesToDelete as $table) {
            if (\db()->tableExists($table)) {
                \db()->table($table)->where('class_id', $param1)->delete();
            }
        }

        \db()->table('classes')->where('id', $param1)->delete();

        \db()->transComplete();

        if (\db()->transStatus() === false) {
            return [
                'status' => false,
                'notification' => get_phrase('error_deleting_class')
            ];
        }

        return [
            'status' => true,
            'notification' => get_phrase('class_deleted_successfully')
        ];
    }

    public function get_class_details_by_id($id)
    {
        try {
            $result = \db()->table('classes')
                ->where('id', $id)
                ->get();
            return $result;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function get_schools()
    {
        try {
            return \db()->table('schools')
                ->where('id !=', 1)
                ->orderBy('name', 'ASC')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function session_create()
    {
        $data = [
            'name' => html_escape($this->request->getPost('name')),
            'school_id' => $this->school_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        \db()->table('sessions')->insert($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('session_created')
        ];
    }

    public function session_update($param1 = '')
    {
        $data = [
            'name' => html_escape($this->request->getPost('name'))
        ];
        
        \db()->table('sessions')->where('id', $param1)->update($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('session_updated')
        ];
    }

    public function session_delete($param1 = '')
    {
        \db()->table('sessions')->where('id', $param1)->delete();
        
        return [
            'status' => true,
            'notification' => get_phrase('session_deleted')
        ];
    }

    public function active_session($param1 = '')
    {
        if ($param1 > 0) {
            \db()->table('schools')->where('id', $this->school_id)->update(['active_session' => $param1]);
            return json_encode(['status' => true]);
        } else {
            $session = \db()->table('sessions')
                ->where('school_id', $this->school_id)
                ->where('status', 1)
                ->get()->getRowArray();
            
            if (!empty($session)) {
                return $session['id'];
            }
            return null;
        }
    }

    public function create_notice()
    {
        $data = [
            'notice_title' => html_escape($this->request->getPost('notice_title')),
            'notice' => html_escape($this->request->getPost('notice')),
            'start_date' => html_escape($this->request->getPost('start_date')),
            'end_date' => html_escape($this->request->getPost('end_date')),
            'show_on_website' => $this->request->getPost('show_on_website') ? 1 : 0,
            'school_id' => $this->school_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        \db()->table('noticeboard')->insert($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('notice_created')
        ];
    }

    public function update_notice($notice_id)
    {
        $data = [
            'notice_title' => html_escape($this->request->getPost('notice_title')),
            'notice' => html_escape($this->request->getPost('notice')),
            'start_date' => html_escape($this->request->getPost('start_date')),
            'end_date' => html_escape($this->request->getPost('end_date')),
            'show_on_website' => $this->request->getPost('show_on_website') ? 1 : 0
        ];
        
        \db()->table('noticeboard')->where('id', $notice_id)->update($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('notice_updated')
        ];
    }

    public function delete_notice($notice_id)
    {
        \db()->table('noticeboard')->where('id', $notice_id)->delete();
        
        return [
            'status' => true,
            'notification' => get_phrase('notice_deleted')
        ];
    }

    public function get_all_the_notices()
    {
        try {
            return \db()->table('noticeboard')
                ->where('school_id', $this->school_id)
                ->orderBy('create_timestamp', 'DESC')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function get_noticeboard_image($image)
    {
        return \db()->table('noticeboard')
            ->where('id', $image)
            ->get()->getRow('image');
    }

    public function exam_create()
    {
        $name = trim((string) ($this->request->getPost('exam_name') ?? $this->request->getPost('name') ?? ''));
        $startingDate = trim((string) ($this->request->getPost('starting_date') ?? $this->request->getPost('date') ?? ''));
        $classId = (int) ($this->request->getPost('class_id') ?? 0);

        if ($name === '') {
            return [
                'status' => false,
                'notification' => get_phrase('certification_title_is_required')
            ];
        }
        if ($startingDate === '') {
            return [
                'status' => false,
                'notification' => get_phrase('date_and_time_is_required')
            ];
        }

        $data = [
            'name' => html_escape($name),
            'starting_date' => html_escape($startingDate),
            'class_id' => $classId,
            'school_id' => $this->school_id,
            'session' => active_session()
        ];
        
        \db()->table('exams')->insert($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('exam_created')
        ];
    }

    public function exam_update($param1 = '')
    {
        $name = trim((string) ($this->request->getPost('exam_name') ?? $this->request->getPost('name') ?? ''));
        $startingDate = trim((string) ($this->request->getPost('starting_date') ?? $this->request->getPost('date') ?? ''));
        $classId = (int) ($this->request->getPost('class_id') ?? 0);

        if ($name === '' || $startingDate === '') {
            return [
                'status' => false,
                'notification' => get_phrase('required_fields_are_missing')
            ];
        }

        $data = [
            'name' => html_escape($name),
            'starting_date' => html_escape($startingDate),
            'class_id' => $classId
        ];
        
        \db()->table('exams')->where('id', $param1)->update($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('exam_updated')
        ];
    }

    public function exam_delete($param1 = '')
    {
        \db()->table('exams')->where('id', $param1)->delete();
        
        return [
            'status' => true,
            'notification' => get_phrase('exam_deleted')
        ];
    }

    public function get_exam_by_id($exam_id = "")
    {
        try {
            return \db()->table('exams')
                ->where('id', $exam_id)
                ->get()->getRowArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function get_marks($class_id = "", $exam_id = "", $school_id = "")
    {
        $builder = \db()->table('marks');

        if ($class_id > 0) {
            $builder->where('class_id', $class_id);
        }
        if ($exam_id > 0) {
            $builder->where('exam_id', $exam_id);
        }
        if ($school_id > 0) {
            $builder->where('school_id', $school_id);
        }

        // Keep CI3-like behavior expected by controllers: return query result object.
        return $builder->get();
    }

    public function get_total_questions($exam_id)
    {
        return (int) \db()->table('exam_questions')
            ->where('exam_id', $exam_id)
            ->countAllResults();
    }

    public function grade_create()
    {
        $data = [
            'name' => html_escape($this->request->getPost('name')),
            'grade_point' => html_escape($this->request->getPost('grade_point')),
            'mark_from' => html_escape($this->request->getPost('mark_from')),
            'mark_upto' => html_escape($this->request->getPost('mark_upto')),
            'school_id' => $this->school_id
        ];
        
        \db()->table('grades')->insert($data);
        
        $user_id = get_user_id();
        $user_type = get_user_type();
        $school_id = $this->school_id;
        
        $audit_model = new \App\Models\Audit_log_model();
        $audit_model->log_grade_create(
            $user_id,
            $user_type,
            $school_id,
            $data['name'],
            $data['grade_point'],
            $data['mark_from'],
            $data['mark_upto'],
            null,
            null
        );
        
        return [
            'status' => true,
            'notification' => get_phrase('grade_created')
        ];
    }

    public function grade_update($id = "")
    {
        $old_grade = \db()->table('grades')->where('id', $id)->get()->getRow();
        
        $data = [
            'name' => html_escape($this->request->getPost('name')),
            'grade_point' => html_escape($this->request->getPost('grade_point')),
            'mark_from' => html_escape($this->request->getPost('mark_from')),
            'mark_upto' => html_escape($this->request->getPost('mark_upto'))
        ];
        
        \db()->table('grades')->where('id', $id)->update($data);
        
        $user_id = get_user_id();
        $user_type = get_user_type();
        $school_id = school_id();
        
        $audit_model = new \App\Models\Audit_log_model();
        $audit_model->log_grade_update(
            $user_id,
            $user_type,
            $school_id,
            $id,
            $old_grade ? [
                'name' => $old_grade->name,
                'grade_point' => $old_grade->grade_point,
                'mark_from' => $old_grade->mark_from,
                'mark_upto' => $old_grade->mark_upto
            ] : [],
            $data,
            null,
            null
        );
        
        return [
            'status' => true,
            'notification' => get_phrase('grade_updated')
        ];
    }

    public function grade_delete($id = '')
    {
        $grade_data = \db()->table('grades')->where('id', $id)->get()->getRow();
        
        \db()->table('grades')->where('id', $id)->delete();
        
        $user_id = get_user_id();
        $user_type = get_user_type();
        $school_id = school_id();
        
        $audit_model = new \App\Models\Audit_log_model();
        $audit_model->log_grade_delete(
            $user_id,
            $user_type,
            $school_id,
            $id,
            $grade_data ? [
                'name' => $grade_data->name,
                'grade_point' => $grade_data->grade_point,
                'mark_from' => $grade_data->mark_from,
                'mark_upto' => $grade_data->mark_upto
            ] : [],
            null,
            null
        );
        
        return [
            'status' => true,
            'notification' => get_phrase('grade_deleted')
        ];
    }

    public function mark_insert($class_id = "", $section_id = "", $exam_id = "")
    {
        $session_id = active_session();
        $school_id = school_id();
        $user_id = get_user_id();
        $user_type = get_user_type();
        
        $students = \db()->table('enrols')
            ->where('class_id', $class_id)
            ->where('school_id', $school_id)
            ->get()
            ->getResultArray();
        
        $marks_created = 0;
        
        foreach ($students as $student) {
            $existing_mark = \db()->table('marks')
                ->where('student_id', $student['student_id'])
                ->where('exam_id', $exam_id)
                ->get()
                ->getRow();
            
            if (!$existing_mark) {
                $mark_data = [
                    'student_id' => $student['student_id'],
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'exam_id' => $exam_id,
                    'session' => $session_id,
                    'school_id' => $school_id,
                    'mark_obtained' => 0,
                    'comment' => ''
                ];
                
                \db()->table('marks')->insert($mark_data);
                $marks_created++;
            }
        }
        
        if ($marks_created > 0) {
            $audit_model = new \App\Models\Audit_log_model();
            $audit_model->log_mark_create(
                $user_id,
                $user_type,
                $school_id,
                $class_id,
                $exam_id,
                null,
                0,
                'Bulk mark creation for class',
                null,
                null
            );
        }
    }

    public function mark_update()
    {
        $student_id = $this->request->getPost('student_id');
        $exam_id = $this->request->getPost('exam_id');
        $class_id = $this->request->getPost('class_id');
        $mark_value = $this->request->getPost('mark');
        $comment = $this->request->getPost('comment');
        
        $mark_on_100 = ($mark_value / 20) * 100;
        $mark_on_100 = round($mark_on_100, 2);
        
        $existing_mark = \db()->table('marks')
            ->where('student_id', $student_id)
            ->where('exam_id', $exam_id)
            ->get()
            ->getRow();
        
        $user_id = get_user_id();
        $user_type = get_user_type();
        $school_id = school_id();
        
        if ($existing_mark) {
            $old_mark = $existing_mark->mark_obtained;
            $old_comment = $existing_mark->comment;
            
            \db()->table('marks')
                ->where('student_id', $student_id)
                ->where('exam_id', $exam_id)
                ->update([
                    'mark_obtained' => $mark_on_100,
                    'comment' => $comment
                ]);
            
            $audit_model = new \App\Models\Audit_log_model();
            $audit_model->log_mark_update(
                $user_id,
                $user_type,
                $school_id,
                $student_id,
                $exam_id,
                $old_mark,
                $mark_on_100,
                $old_comment,
                $comment,
                null,
                null
            );
        } else {
            \db()->table('marks')->insert([
                'student_id' => $student_id,
                'class_id' => $class_id,
                'exam_id' => $exam_id,
                'mark_obtained' => $mark_on_100,
                'comment' => $comment,
                'session' => active_session(),
                'school_id' => $school_id
            ]);
            
            $audit_model = new \App\Models\Audit_log_model();
            $audit_model->log_mark_create(
                $user_id,
                $user_type,
                $school_id,
                $class_id,
                $exam_id,
                $student_id,
                $mark_on_100,
                $comment,
                null,
                null
            );
        }
        
        return [
            'status' => true,
            'notification' => get_phrase('mark_updated_successfully')
        ];
    }

    public function get_invoice_by_id($id = "")
    {
        try {
            return \db()->table('invoices')
                ->where('id', $id)
                ->get()->getRowArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function get_invoice_by_student_id($user_id = "", $limit = null, $offset = null, $filter = 'all', $search = '')
    {
        try {
            $builder = \db()->table('invoices')
                ->where('student_id', $user_id);
            
            if ($filter && $filter != 'all') {
                $builder->where('status', $filter);
            }
            
            if ($search) {
                $builder->like('total_amount', $search);
            }
            
            if ($limit) {
                $builder->limit($limit, $offset);
            }
            
            return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function count_invoices_by_student($user_id = "", $filter = 'all', $search = '')
    {
        try {
            $builder = \db()->table('invoices')
                ->where('student_id', $user_id);
            
            if ($filter && $filter != 'all') {
                $builder->where('status', $filter);
            }
            
            if ($search) {
                $builder->like('total_amount', $search);
            }
            
            return $builder->countAllResults();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function create_single_invoice()
    {
        $data['title'] = html_escape($this->request->getPost('title'));
        $data['total_amount'] = html_escape($this->request->getPost('total_amount'));
        $data['class_id'] = html_escape($this->request->getPost('class_id'));
        $data['student_id'] = html_escape($this->request->getPost('student_id'));
        $data['paid_amount'] = html_escape($this->request->getPost('paid_amount'));
        $data['status'] = html_escape($this->request->getPost('status'));
        $data['school_id'] = school_id();
        $data['session'] = active_session();
        $data['created_at'] = strtotime(date('d-M-Y'));

        if ($data['paid_amount'] > $data['total_amount']) {
            return [
                'status' => false,
                'notification' => get_phrase('paid_amount_greater_than_total')
            ];
        }

        if ($data['total_amount'] == $data['paid_amount']) {
            $data['status'] = 'paid';
        }

        if ($data['paid_amount'] > 0) {
            $data['updated_at'] = strtotime(date('d-M-Y'));
        }

        \db()->table('invoices')->insert($data);
        $invoice_id = \db()->insertID();

        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_invoice_create($invoice_id, $data);

        return [
            'status' => true,
            'notification' => get_phrase('invoice_created_successfully')
        ];
    }

    public function create_mass_invoice()
    {
        $data['title'] = html_escape($this->request->getPost('title'));
        $data['total_amount'] = html_escape($this->request->getPost('total_amount'));
        $data['class_id'] = html_escape($this->request->getPost('class_id'));
        $data['section_id'] = html_escape($this->request->getPost('section_id'));
        $data['paid_amount'] = html_escape($this->request->getPost('paid_amount'));
        $data['status'] = html_escape($this->request->getPost('status'));
        $data['school_id'] = school_id();
        $data['session'] = active_session();
        $data['created_at'] = strtotime(date('d-M-Y'));

        if ($data['paid_amount'] > $data['total_amount']) {
            return [
                'status' => false,
                'notification' => get_phrase('paid_amount_greater_than_total')
            ];
        }

        if ($data['total_amount'] == $data['paid_amount']) {
            $data['status'] = 'paid';
        }

        $students = \db()->table('enrols')
            ->where('class_id', $data['class_id'])
            ->where('section_id', $data['section_id'])
            ->where('school_id', $data['school_id'])
            ->get()->getResultArray();

        if (empty($students)) {
            return [
                'status' => false,
                'notification' => get_phrase('no_students_found')
            ];
        }

        $audit_log_model = new \App\Models\Audit_log_model();

        foreach ($students as $student) {
            $invoice_data = $data;
            $invoice_data['student_id'] = $student['student_id'];
            
            if ($invoice_data['paid_amount'] > 0) {
                $invoice_data['updated_at'] = strtotime(date('d-M-Y'));
            }
            
            \db()->table('invoices')->insert($invoice_data);
            $invoice_id = \db()->insertID();
            
            $audit_log_model->log_invoice_create($invoice_id, $invoice_data);
        }

        return [
            'status' => true,
            'notification' => get_phrase('invoices_created_successfully')
        ];
    }

    public function update_invoice($id = "")
    {
        $previous_invoice = \db()->table('invoices')->where('id', $id)->get()->getRowArray();
        
        $data = [
            'status' => html_escape($this->request->getPost('status'))
        ];
        
        \db()->table('invoices')->where('id', $id)->update($data);
        
        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_invoice_update($id, $previous_invoice, $data);
        
        return [
            'status' => true,
            'notification' => get_phrase('invoice_updated')
        ];
    }

    public function delete_invoice($id = "")
    {
        $invoice = \db()->table('invoices')->where('id', $id)->get()->getRowArray();
        
        \db()->table('invoice_details')->where('invoice_id', $id)->delete();
        \db()->table('invoices')->where('id', $id)->delete();
        
        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_invoice_delete($id, $invoice);
        
        return [
            'status' => true,
            'notification' => get_phrase('invoice_deleted')
        ];
    }

    public function get_expense_categories($id = "")
    {
        try {
            $builder = \db()->table('expense_categories');
            $school_id = $this->school_id ?? school_id();
            $active_session = $this->active_session ?? active_session();
            
            if ($id > 0) {
                $builder->where('id', $id);
            }
            
            $builder->where('school_id', $school_id);
            if (!empty($active_session)) {
                $builder->where('session', $active_session);
            }

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function create_expense_category()
    {
        $data = [
            'name' => html_escape($this->request->getPost('name')),
            'school_id' => $this->school_id
        ];
        
        \db()->table('expense_categories')->insert($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('expense_category_created')
        ];
    }

    public function update_expense_category($id)
    {
        $data = [
            'name' => html_escape($this->request->getPost('name')),
            'cost_center' => html_escape($this->request->getPost('cost_center')),
            'department' => html_escape($this->request->getPost('department'))
        ];

        \db()->table('expense_categories')->where('id', $id)->update($data);

        return [
            'status' => true,
            'notification' => get_phrase('expense_category_updated')
        ];
    }

    public function delete_expense_category($id)
    {
        \db()->table('expense_categories')->where('id', $id)->delete();
        
        return [
            'status' => true,
            'notification' => get_phrase('expense_category_deleted')
        ];
    }

    public function get_expense_by_id($id = "")
    {
        try {
            return \db()->table('expenses')
                ->where('id', $id)
                ->get()->getRowArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function create_expense()
    {
        $date_value = (string) $this->request->getPost('date');
        $data = [
            'expense_category_id' => html_escape($this->request->getPost('expense_category_id')),
            'amount' => html_escape($this->request->getPost('amount')),
            'date' => strtotime($date_value),
            'school_id' => $this->school_id ?? school_id(),
            'session' => $this->active_session ?? active_session(),
            'created_at' => strtotime(date('d-M-Y'))
        ];
        
        \db()->table('expenses')->insert($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('expense_added_successfully')
        ];
    }

    public function update_expense($id = "")
    {
        $date_value = (string) $this->request->getPost('date');
        $data = [
            'expense_category_id' => html_escape($this->request->getPost('expense_category_id')),
            'amount' => html_escape($this->request->getPost('amount')),
            'date' => strtotime($date_value),
            'school_id' => $this->school_id ?? school_id(),
            'session' => $this->active_session ?? active_session(),
        ];

        \db()->table('expenses')->where('id', $id)->update($data);

        return [
            'status' => true,
            'notification' => get_phrase('expense_updated_successfully')
        ];
    }

    public function delete_expense($id = "")
    {
        \db()->table('expenses')->where('id', $id)->delete();

        return [
            'status' => true,
            'notification' => get_phrase('expense_deleted_successfully')
        ];
    }

    public function payment_success($data = array())
    {
        $invoice_id = (int) ($data['invoice_id'] ?? 0);
        if ($invoice_id <= 0) {
            return ['status' => 'failed'];
        }

        $invoice = \db()->table('invoices')->where('id', $invoice_id)->get()->getRowArray();
        if (empty($invoice)) {
            return ['status' => 'failed'];
        }

        $already_paid = (float) ($invoice['paid_amount'] ?? 0);
        $invoice_total = (float) ($invoice['total_amount'] ?? 0);
        $due_amount = $invoice_total - $already_paid;

        $amount_to_compare = (float) ($data['amount_paid'] ?? ($data['amount'] ?? 0));
        if (!empty($data['conversion_applied']) && isset($data['original_amount'])) {
            $amount_to_compare = (float) $data['original_amount'];
        }

        $is_full_payment = abs($due_amount - $amount_to_compare) < 0.01;
        log_message(
            'debug',
            'Payment check: due_amount=' . $due_amount . ', amount_to_compare=' . $amount_to_compare . ', is_full_payment=' . ($is_full_payment ? 'true' : 'false')
        );

        if (!$is_full_payment) {
            return ['status' => 'failed'];
        }

        $updater = [
            'status' => 'paid',
            'payment_method' => $data['payment_method'] ?? 'online',
            'paid_amount' => $invoice_total,
            'updated_at' => strtotime(date('d-M-Y')),
        ];

        if (!empty($data['conversion_applied'])) {
            $updater['payment_currency'] = $data['currency'] ?? null;
            $updater['payment_amount_converted'] = $data['amount_paid'] ?? null;
            $updater['fx_rate'] = $data['fx_rate'] ?? null;
            $updater['fx_rate_date'] = $data['fx_rate_date'] ?? null;
            $updater['conversion_applied'] = 1;
        }

        static $hasDueAmountColumn = null;
        if ($hasDueAmountColumn === null) {
            $hasDueAmountColumn = in_array('due_amount', \db()->getFieldNames('invoices'), true);
        }
        if ($hasDueAmountColumn) {
            $updater['due_amount'] = 0;
        }

        \db()->table('invoices')->where('id', $invoice_id)->update($updater);
        log_message('info', 'Invoice #' . $invoice_id . ' marked as paid');

        $enrolment_data = session()->get('enrolment_data') ?? [];
        $student_identifier = $enrolment_data['student_id'] ?? ($invoice['student_id'] ?? null);
        $school_id = (int) ($enrolment_data['school_id'] ?? ($invoice['school_id'] ?? 0));
        $class_id = (int) ($enrolment_data['class_id'] ?? ($invoice['class_id'] ?? 0));
        $session_id = (int) ($enrolment_data['session'] ?? ($invoice['session'] ?? ($this->active_session ?? active_session())));
        $section_id = isset($enrolment_data['section_id']) ? (int) $enrolment_data['section_id'] : null;

        $student_row = null;
        if (!empty($student_identifier)) {
            $student_row = \db()->table('students')->where('id', $student_identifier)->get()->getRowArray();
            if (!$student_row) {
                $builder = \db()->table('students')->where('user_id', $student_identifier);
                if ($school_id > 0) {
                    $builder->where('school_id', $school_id);
                }
                $student_row = $builder->get()->getRowArray();
            }
        }

        if ($student_row && $class_id > 0 && $school_id > 0 && $session_id > 0) {
            $exists = \db()->table('enrols')
                ->where('student_id', (int) $student_row['id'])
                ->where('class_id', $class_id)
                ->where('school_id', $school_id)
                ->where('session', $session_id)
                ->countAllResults();

            if ((int) $exists === 0) {
                $enrol_insert = [
                    'student_id' => (int) $student_row['id'],
                    'class_id' => $class_id,
                    'school_id' => $school_id,
                    'session' => $session_id,
                ];
                if ($section_id !== null && $section_id > 0) {
                    $enrol_insert['section_id'] = $section_id;
                }
                \db()->table('enrols')->insert($enrol_insert);
            }
        }

        return ['status' => 'success'];
    }

    public function get_session($id = "")
    {
        try {
            if ($id > 0) {
                return \db()->table('sessions')
                    ->where('id', $id)
                    ->get()->getRowArray();
            }
            
            return \db()->table('sessions')
                ->where('school_id', $this->school_id)
                ->orderBy('name', 'ASC')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            return $id > 0 ? null : [];
        }
    }

    public function get_books()
    {
        try {
            return \db()->table('books')
                ->where('school_id', $this->school_id)
                ->get()->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function get_book_by_id($id = "")
    {
        try {
            return \db()->table('books')
                ->where('id', $id)
                ->get()->getRowArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function create_book()
    {
        $data = [
            'name' => html_escape($this->request->getPost('name')),
            'author' => html_escape($this->request->getPost('author')),
            'class_id' => html_escape($this->request->getPost('class_id')),
            'school_id' => $this->school_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        \db()->table('books')->insert($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('book_created')
        ];
    }

    public function update_book($id = "")
    {
        $data = [
            'name' => html_escape($this->request->getPost('name')),
            'author' => html_escape($this->request->getPost('author')),
            'class_id' => html_escape($this->request->getPost('class_id'))
        ];
        
        \db()->table('books')->where('id', $id)->update($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('book_updated')
        ];
    }

    public function delete_book($id = "")
    {
        \db()->table('books')->where('id', $id)->delete();
        
        return [
            'status' => true,
            'notification' => get_phrase('book_deleted')
        ];
    }

    public function get_book_issues($date_from = "", $date_to = "")
    {
        try {
            $builder = \db()->table('book_issues');
            
            if ($date_from && $date_to) {
                $builder->where("issue_date >=", $date_from);
                $builder->where("issue_date <=", $date_to);
            }
            
            return $builder->where('school_id', $this->school_id)->get()->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function get_book_issues_by_student_id($student_id = "")
    {
        try {
            return \db()->table('book_issues')
                ->where('student_id', $student_id)
                ->where('return_status', 0)
                ->get()->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function create_book_issue()
    {
        $data = [
            'book_id' => html_escape($this->request->getPost('book_id')),
            'student_id' => html_escape($this->request->getPost('student_id')),
            'issue_date' => date('Y-m-d', strtotime($this->request->getPost('issue_date'))),
            'return_date' => date('Y-m-d', strtotime($this->request->getPost('return_date'))),
            'school_id' => $this->school_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        \db()->table('book_issues')->insert($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('book_issued')
        ];
    }

    public function update_book_issue($id = "")
    {
        $data = [
            'book_id' => html_escape($this->request->getPost('book_id')),
            'student_id' => html_escape($this->request->getPost('student_id')),
            'issue_date' => date('Y-m-d', strtotime($this->request->getPost('issue_date'))),
            'return_date' => date('Y-m-d', strtotime($this->request->getPost('return_date')))
        ];
        
        \db()->table('book_issues')->where('id', $id)->update($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('book_issue_updated')
        ];
    }

    public function return_issued_book($id = "")
    {
        $data = [
            'return_date' => date('Y-m-d'),
            'return_status' => 1
        ];
        
        \db()->table('book_issues')->where('id', $id)->update($data);
        
        return [
            'status' => true,
            'notification' => get_phrase('book_returned')
        ];
    }

    public function delete_book_issue($id = "")
    {
        \db()->table('book_issues')->where('id', $id)->delete();
        
        return [
            'status' => true,
            'notification' => get_phrase('book_issue_deleted')
        ];
    }

    public function get_community_details_by_id($school_id = "")
    {
        try {
            return \db()->table('schools')
                ->where('id', $school_id)
                ->get()->getRowArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function get_student_list()
    {
        try {
            return \db()->table('students')
                ->where('school_id', $this->school_id)
                ->get()->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function enroll_student_free(array $data): bool
    {
        $student_id = (int) ($data['student_id'] ?? 0);
        $class_id = (int) ($data['class_id'] ?? 0);
        $school_id = (int) ($data['school_id'] ?? 0);
        $session_id = (int) ($data['session'] ?? ($this->active_session ?? active_session()));

        if ($student_id <= 0 || $class_id <= 0 || $school_id <= 0 || $session_id <= 0) {
            return false;
        }

        $builder = \db()->table('enrols');
        $exists = $builder
            ->where('student_id', $student_id)
            ->where('class_id', $class_id)
            ->where('school_id', $school_id)
            ->where('session', $session_id)
            ->countAllResults();

        if ($exists > 0) {
            return true;
        }

        $insert_data = [
            'student_id' => $student_id,
            'class_id' => $class_id,
            'school_id' => $school_id,
            'session' => $session_id,
        ];

        if (isset($data['section_id']) && $data['section_id'] !== '' && $data['section_id'] !== null) {
            $insert_data['section_id'] = (int) $data['section_id'];
        }

        return (bool) \db()->table('enrols')->insert($insert_data);
    }

    private function compress_and_save_image($source_path, $destination_path, $max_width = 512, $max_height = 512, $quality = 90)
    {
        if (!file_exists($source_path)) {
            return false;
        }
        if (!extension_loaded('gd')) {
            return false;
        }
        $image_info = @getimagesize($source_path);
        if ($image_info === false) {
            return false;
        }
        $original_width = $image_info[0];
        $original_height = $image_info[1];
        $mime_type = $image_info['mime'];
        if ($original_width < 1 || $original_height < 1) {
            return false;
        }
        $source_image = $this->create_image_from_file($source_path, $mime_type);
        if ($source_image === false) {
            return false;
        }
        list($new_width, $new_height) = $this->calculate_dimensions($original_width, $original_height, $max_width, $max_height);
        $destination_image = $this->create_destination_image($source_image, $original_width, $original_height, $new_width, $new_height, $mime_type);
        if ($destination_image === false) {
            imagedestroy($source_image);
            return false;
        }
        $final_path = $destination_path . '.jpg';
        $save_result = $this->save_optimized_jpeg($destination_image, $final_path, $quality);
        imagedestroy($source_image);
        imagedestroy($destination_image);
        if (!$save_result) {
            if (file_exists($final_path)) {
                @unlink($final_path);
            }
            return false;
        }
        return $final_path;
    }

    private function create_image_from_file($source_path, $mime_type)
    {
        $source_image = false;
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $source_image = @imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $source_image = @imagecreatefrompng($source_path);
                break;
            case 'image/gif':
                $source_image = @imagecreatefromgif($source_path);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $source_image = @imagecreatefromwebp($source_path);
                }
                break;
        }
        return $source_image;
    }

    private function calculate_dimensions($original_width, $original_height, $max_width, $max_height)
    {
        $new_width = $original_width;
        $new_height = $original_height;
        if ($original_width > $max_width || $original_height > $max_height) {
            $ratio_width = $max_width / $original_width;
            $ratio_height = $max_height / $original_height;
            $ratio = min($ratio_width, $ratio_height);
            $new_width = max(1, (int) round($original_width * $ratio));
            $new_height = max(1, (int) round($original_height * $ratio));
        }
        return [$new_width, $new_height];
    }

    private function create_destination_image($source_image, $original_width, $original_height, $new_width, $new_height, $mime_type)
    {
        $destination_image = @imagecreatetruecolor($new_width, $new_height);
        if ($destination_image === false) {
            return false;
        }
        
        if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
            imagealphablending($destination_image, false);
            imagesavealpha($destination_image, true);
            $transparent = imagecolorallocatealpha($destination_image, 0, 0, 0, 127);
            imagefilledrectangle($destination_image, 0, 0, $new_width, $new_height, $transparent);
        }
        
        if (!imagecopyresampled($destination_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height)) {
            imagedestroy($destination_image);
            return false;
        }
        
        return $destination_image;
    }

    private function save_optimized_jpeg($image, $path, $quality)
    {
        return imagejpeg($image, $path, $quality);
    }

    public function get_quiz($quiz_id, $student_id)
    {
        $quiz_responses = db()->table('quiz_responses')
            ->where('quiz_id', $quiz_id)
            ->where('user_id', $student_id)
            ->get()
            ->getResultArray();
        
        if (empty($quiz_responses)) {
            return null;
        }
        
        $total_responses = count($quiz_responses);
        $correct_responses = 0;
        
        foreach ($quiz_responses as $response) {
            if ($response['submitted_answer_status'] == 1) {
                $correct_responses++;
            }
        }
        
        return [
            'total_responses' => $total_responses,
            'correct_responses' => $correct_responses,
            'responses' => $quiz_responses
        ];
    }

    public function syllabus_create()
    {
        try {
            $title = $this->request->getPost('title');
            $class_id = $this->request->getPost('class_id');
            $school_id = $this->request->getPost('school_id');
            $session_id = $this->request->getPost('session_id');
            $syllabus_file = $this->request->getFile('syllabus_file');
            
            if (empty($title) || empty($class_id)) {
                log_message('error', 'Syllabus creation failed: Missing required fields. Title: ' . var_export($title, true) . ', Class ID: ' . var_export($class_id, true));
                return [
                    'status' => false,
                    'notification' => get_phrase('required_fields_missing')
                ];
            }
            
            if (empty($syllabus_file) || !$syllabus_file->isValid()) {
                $error = $syllabus_file->getErrorString();
                log_message('error', 'Syllabus creation failed: Invalid file. Error: ' . $error);
                return [
                    'status' => false,
                    'notification' => get_phrase('invalid_file') . ' (' . $error . ')'
                ];
            }
            
            $max_size = 20 * 1024 * 1024;
            if ($syllabus_file->getSize() > $max_size) {
                $file_size_mb = round($syllabus_file->getSize() / (1024 * 1024), 2);
                log_message('error', 'Syllabus creation failed: File size exceeds limit. Size: ' . $file_size_mb . 'MB, Limit: 20MB');
                return [
                    'status' => false,
                    'notification' => get_phrase('file_size_exceeds_20mb') . ' (' . $file_size_mb . 'MB)'
                ];
            }
            
            $allowed_mimes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain'
            ];
            
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($syllabus_file->getTempName());
            
            if (!in_array($mimeType, $allowed_mimes)) {
                log_message('error', 'Syllabus creation failed: Invalid MIME type. Type: ' . $mimeType);
                return [
                    'status' => false,
                    'notification' => get_phrase('invalid_file_type') . ' (MIME: ' . $mimeType . ')'
                ];
            }
            
            $allowed_extensions = ['pdf', 'doc', 'docx', 'txt'];
            $file_extension = strtolower($syllabus_file->getExtension());
            
            if (!in_array($file_extension, $allowed_extensions)) {
                log_message('error', 'Syllabus creation failed: Invalid file extension. Extension: ' . $file_extension);
                return [
                    'status' => false,
                    'notification' => get_phrase('invalid_file_type') . ' (.pdf, .doc, .docx, .txt only)'
                ];
            }
            
            $upload_path = 'uploads/syllabus/';
            
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    log_message('error', 'Syllabus creation failed: Failed to create upload directory. Path: ' . $upload_path);
                    return [
                        'status' => false,
                        'notification' => get_phrase('failed_to_create_upload_directory')
                    ];
                }
            }
            
            if (!is_writable($upload_path)) {
                log_message('error', 'Syllabus creation failed: Upload directory is not writable. Path: ' . $upload_path);
                return [
                    'status' => false,
                    'notification' => get_phrase('upload_directory_not_writable')
                ];
            }
            
            $new_name = uniqid('syllabus_') . '.' . $file_extension;
            $file_path = '';
            
            if (!$syllabus_file->move($upload_path, $new_name)) {
                $error = $syllabus_file->getErrorString();
                log_message('error', 'Syllabus creation failed: File move failed. Error: ' . $error);
                return [
                    'status' => false,
                    'notification' => get_phrase('failed_to_upload_file') . ' (' . $error . ')'
                ];
            }
            
            $file_path = $upload_path . $new_name;
            
            if (!file_exists($file_path)) {
                log_message('error', 'Syllabus creation failed: File does not exist after upload. Path: ' . $file_path);
                return [
                    'status' => false,
                    'notification' => get_phrase('file_upload_verification_failed')
                ];
            }
            
            $data = [
                'title' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
                'class_id' => (int) $class_id,
                'school_id' => (int) $school_id,
                'session_id' => (int) $session_id,
                'file' => $file_path
            ];
            
            $insert_result = db()->table('syllabuses')->insert($data);
            
            if ($insert_result) {
                $syllabus_id = db()->insertID();
                log_message('info', 'Syllabus created successfully. ID: ' . $syllabus_id . ', File: ' . $file_path);
                
                $audit_log_model = new \App\Models\Audit_log_model();
                $user_id = session()->get('user_id');
                $user_type = session()->get('user_type');
                $audit_log_model->log_syllabus_create($user_id, $user_type, $school_id, $syllabus_id, $data);
                
                return [
                    'status' => true,
                    'notification' => get_phrase('syllabus_created_successfully')
                ];
            } else {
                log_message('error', 'Syllabus creation failed: Database insert failed. Data: ' . json_encode($data));
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }
                return [
                    'status' => false,
                    'notification' => get_phrase('failed_to_create_syllabus') . ' (Database error)'
                ];
            }
        } catch (\Exception $e) {
            log_message('critical', 'Syllabus creation exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            if (isset($file_path) && file_exists($file_path)) {
                @unlink($file_path);
            }
            return [
                'status' => false,
                'notification' => get_phrase('an_error_occurred') . ': ' . $e->getMessage()
            ];
        }
    }

    public function syllabus_delete($syllabus_id)
    {
        try {
            $syllabus = db()->table('syllabuses')->where('id', (int) $syllabus_id)->get()->getRowArray();
            
            if (!$syllabus) {
                log_message('warning', 'Syllabus delete failed: Syllabus not found. ID: ' . $syllabus_id);
                return false;
            }
            
            $user_school_id = school_id();
            if ($syllabus['school_id'] != $user_school_id) {
                log_message('warning', 'Syllabus delete failed: School mismatch. Syllabus school: ' . $syllabus['school_id'] . ', User school: ' . $user_school_id);
                return false;
            }
            
            $file_deleted = false;
            if (!empty($syllabus['file']) && file_exists($syllabus['file'])) {
                unlink($syllabus['file']);
                $file_deleted = true;
            }
            
            $delete_result = db()->table('syllabuses')->where('id', (int) $syllabus_id)->delete();
            
            if ($delete_result) {
                log_message('info', 'Syllabus deleted successfully. ID: ' . $syllabus_id);
                
                $audit_log_model = new \App\Models\Audit_log_model();
                $user_id = session()->get('user_id');
                $user_type = session()->get('user_type');
                $audit_log_model->log_syllabus_delete($user_id, $user_type, $user_school_id, $syllabus_id, array_merge($syllabus, ['file_deleted' => $file_deleted]));
            }
            
            return $delete_result;
        } catch (\Exception $e) {
            log_message('error', 'Syllabus delete exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            return false;
        }
    }
}
