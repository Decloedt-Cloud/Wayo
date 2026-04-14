<?php

namespace App\Models;

use CodeIgniter\Model;

class Admin_model extends Model {
    protected $DBGroup = 'default';
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField    = 'deleted_at';
    protected $dateFormat       = 'datetime';

    public function __construct() {
        parent::__construct();
    }

    public function get_user_by_id($user_id) {
        return \db()->table('users')->where(['id' => $user_id])->get()->getRowArray();
    }

    public function get_student_by_id($student_id) {
        return \db()->table('students')->where(['id' => $student_id])->get()->getRowArray();
    }

    public function get_student_by_user_id($user_id) {
        return \db()->table('students')->where(['user_id' => $user_id])->get()->getRowArray();
    }

    public function get_teacher_by_id($teacher_id) {
        return \db()->table('teachers')->where(['id' => $teacher_id])->get()->getRowArray();
    }

    public function get_teacher_by_user_id($user_id) {
        return \db()->table('teachers')->where(['user_id' => $user_id])->get()->getRowArray();
    }

    public function get_school_by_id($school_id) {
        return \db()->table('schools')->where(['id' => $school_id])->get()->getRowArray();
    }

    public function get_active_school($school_id) {
        return \db()->table('schools')->where(['id' => $school_id])->get()->getRowArray();
    }

    public function school_exists($school_id) {
        return \db()->table('schools')->where(['id' => $school_id])->get()->getRow() !== null;
    }

    public function get_class_by_id($class_id) {
        return \db()->table('classes')->where(['id' => $class_id])->get()->getRowArray();
    }

    public function get_classes_by_school($school_id) {
        return \db()->table('classes')->where(['school_id' => $school_id])->get()->getResultArray();
    }

    public function class_exists($class_id, $school_id) {
        return \db()->table('classes')->where(['id' => $class_id, 'school_id' => $school_id])->get()->getRow() !== null;
    }

    public function get_section_by_id($section_id) {
        return \db()->table('sections')->where(['id' => $section_id])->get()->getRowArray();
    }

    public function get_section_name($section_id) {
        return \db()->table('sections')->where(['id' => $section_id])->get()->getRow('name');
    }

    public function get_sections_by_class($class_id) {
        return \db()->table('sections')->where(['class_id' => $class_id])->get()->getResultArray();
    }

    public function get_exam_by_id($exam_id) {
        return \db()->table('exams')->where(['id' => $exam_id])->get()->getRowArray();
    }

    public function get_exams_by_school($school_id) {
        return \db()->table('exams')->where(['school_id' => $school_id])->get()->getResultArray();
    }

    public function get_subject_by_id($subject_id) {
        return \db()->table('subjects')->where(['id' => $subject_id])->get()->getRowArray();
    }

    public function get_subjects_by_class($class_id) {
        return \db()->table('subjects')->where(['class_id' => $class_id])->get()->getResultArray();
    }

    public function user_exists($user_id) {
        return \db()->table('users')->where(['id' => $user_id])->get()->getRow() !== null;
    }

    public function student_exists($student_id) {
        return \db()->table('students')->where(['id' => $student_id])->get()->getRow() !== null;
    }

    public function teacher_exists($teacher_id) {
        return \db()->table('teachers')->where(['id' => $teacher_id])->get()->getRow() !== null;
    }

    public function get_enroll_by_student_class($student_id, $class_id) {
        return \db()->table('enroll')->where(['student_id' => $student_id, 'class_id' => $class_id])->get()->getRowArray();
    }

    public function get_enrollments_by_student($student_id) {
        return \db()->table('enroll')->where(['student_id' => $student_id])->get()->getResultArray();
    }

    public function custom_insert($table, $data) {
        \db()->table($table)->insert($data);
        return \db()->insertID();
    }

    public function custom_update($table, $where, $data) {
        return \db()->table($table)->where($where)->update($data);
    }

    public function custom_delete($table, $where) {
        return \db()->table($table)->where($where)->delete();
    }

    public function count_all($table, $where = []) {
        $builder = \db()->table($table);
        if (!empty($where)) {
            $builder->where($where);
        }
        return $builder->countAllResults();
    }

    public function get_where($table, $where) {
        return \db()->table($table)->where($where)->get()->getRowArray();
    }

    public function get_all($table) {
        return \db()->table($table)->get()->getResultArray();
    }

    public function get_where_result($table, $where) {
        return \db()->table($table)->where($where)->get()->getResultArray();
    }

    public function get_row($table, $where) {
        return \db()->table($table)->where($where)->get()->getRow();
    }

    public function get_with_join($table, $joins = [], $where = [], $select = '*') {
        $builder = \db()->table($table)->select($select);
        
        foreach ($joins as $join) {
            $builder->join($join['table'], $join['condition'], isset($join['type']) ? $join['type'] : 'inner');
        }
        
        if (!empty($where)) {
            $builder->where($where);
        }
        
        return $builder->get()->getResultArray();
    }

    public function get_with_like($table, $like = [], $where = [], $select = '*') {
        $builder = \db()->table($table)->select($select);
        
        if (!empty($like)) {
            foreach ($like as $field => $value) {
                $builder->like($field, $value);
            }
        }
        
        if (!empty($where)) {
            $builder->where($where);
        }
        
        return $builder->get()->getResultArray();
    }

    public function get_ordered($table, $order_by, $order_dir = 'DESC', $where = [], $select = '*') {
        $builder = \db()->table($table)->select($select);
        
        if (!empty($where)) {
            $builder->where($where);
        }
        
        return $builder->orderBy($order_by, $order_dir)->get()->getResultArray();
    }

    public function get_limited($table, $limit, $offset = 0, $where = [], $select = '*') {
        $builder = \db()->table($table)->select($select);
        
        if (!empty($where)) {
            $builder->where($where);
        }
        
        return $builder->limit($limit, $offset)->get()->getResultArray();
    }

    public function count($table, $where = []) {
        return \db()->table($table)->where($where)->countAllResults();
    }

    public function get_by_query($sql, $params = []) {
        if (is_string($sql)) {
            return \db()->query($sql, $params)->getRowArray();
        }
        
        $config = $sql;
        $builder = \db()->table($config['from']);
        
        if (isset($config['select'])) {
            $builder->select($config['select']);
        }
        
        if (isset($config['where']) && !empty($config['where'])) {
            $builder->where($config['where']);
        }
        
        if (isset($config['like'])) {
            $builder->like($config['like']);
        }
        
        if (isset($config['join'])) {
            foreach ($config['join'] as $join) {
                $type = isset($join[2]) ? $join[2] : 'inner';
                $builder->join($join[0], $join[1], $type);
            }
        }
        
        if (isset($config['order_by'])) {
            $dir = isset($config['order_dir']) ? $config['order_dir'] : 'DESC';
            $builder->orderBy($config['order_by'], $dir);
        }
        
        if (isset($config['limit'])) {
            $offset = isset($config['offset']) ? $config['offset'] : 0;
            $builder->limit($config['limit'], $offset);
        }
        
        if (isset($config['group_by'])) {
            $builder->groupBy($config['group_by']);
        }
        
        $result = $builder->get();
        
        if (isset($config['return']) && $config['return'] === 'row') {
            return $result->getRow();
        } elseif (isset($config['return']) && $config['return'] === 'row_array') {
            return $result->getRowArray();
        } elseif (isset($config['return']) && $config['return'] === 'num_rows') {
            return $result->numRows();
        }
        return $result->getResultArray();
    }

    public function get_event_by_id($event_id) {
        return \db()->table('event_calendars')->where(['id' => $event_id])->get()->getRowArray();
    }

    public function get_events_by_school($school_id) {
        return \db()->table('event_calendars')->where(['school_id' => $school_id])->get()->getResultArray();
    }

    public function get_student_user_id($student_id) {
        return \db()->table('students')->where(['id' => $student_id])->get()->getRow('user_id');
    }

    public function get_driver_by_user_id($user_id) {
        return \db()->table('drivers')->where(['user_id' => $user_id])->get()->getRow('id');
    }

    public function get_settings_school($school_id) {
        return \db()->table('settings_school')->where(['school_id' => $school_id])->get()->getRow('system_currency');
    }

    public function get_expense_category($category_id) {
        return \db()->table('expense_categories')->where(['id' => $category_id])->get()->getRowArray();
    }

    public function get_appointments_by_event($event_id) {
        return \db()->table('appointments')->where(['event_id' => $event_id])->get()->getResultArray();
    }

    public function get_session_meeting_by_appointment($appointment_id) {
        return \db()->table('sessions_meetings')->where(['appointment_id' => $appointment_id])->get()->getRowArray();
    }

    public function get_session_meeting($meeting_id, $appointment_id) {
        return \db()->table('sessions_meetings')->where(['id' => $meeting_id, 'appointment_id' => $appointment_id])->get()->getRowArray();
    }

    public function get_participants_by_event($event_id) {
        return \db()->table('participants')->where(['event_id' => $event_id])->get()->getResultArray();
    }
    
    public function get_inactive_schools() {
        return \db()->table('schools')
            ->where('status', 0)
            ->where('Etat', 1)
            ->get()
            ->getResultArray();
    }
}
