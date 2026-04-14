<?php

namespace App\Models;

use CodeIgniter\Model;

class Student_model extends Model {
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField    = 'deleted_at';
    protected $dateFormat       = 'datetime';

    protected $DBGroup = 'default';

    public function __construct() {
        parent::__construct();
    }

    public function get_student_by_id($student_id) {
        return \db()->table('students')->where('id', $student_id)->get()->getRowArray();
    }

    public function get_student_by_user_id($user_id) {
        return \db()->table('students')->where('user_id', $user_id)->get()->getRowArray();
    }

    public function get_student_by_user_id_and_school($user_id, $school_id) {
        return \db()->table('students')
            ->where('user_id', $user_id)
            ->where('school_id', $school_id)
            ->get()
            ->getRowArray();
    }

    public function get_user_by_id($user_id) {
        return \db()->table('users')->where('id', $user_id)->get()->getRowArray();
    }

    public function get_school_by_id($school_id) {
        return \db()->table('schools')->where('id', $school_id)->get()->getRowArray();
    }

    public function get_active_school($school_id) {
        return \db()->table('schools')->where('id', $school_id)->get()->getRowArray();
    }

    public function school_exists($school_id) {
        return \db()->table('schools')->where('id', $school_id)->countAllResults() > 0;
    }

    public function get_class_by_id($class_id) {
        return \db()->table('classes')->where('id', $class_id)->get()->getRowArray();
    }

    public function get_classes_by_school($school_id) {
        return \db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
    }

    public function class_exists($class_id, $school_id) {
        return \db()->table('classes')
            ->where('id', $class_id)
            ->where('school_id', $school_id)
            ->countAllResults() > 0;
    }

    public function get_section_by_id($section_id) {
        return \db()->table('sections')->where('id', $section_id)->get()->getRowArray();
    }

    public function get_section_name($section_id) {
        return \db()->table('sections')->where('id', $section_id)->get()->getRow('name');
    }

    public function get_sections_by_class($class_id) {
        return \db()->table('sections')->where('class_id', $class_id)->get()->getResultArray();
    }

    public function get_exam_by_id($exam_id) {
        return \db()->table('exams')->where('id', $exam_id)->get()->getRowArray();
    }

    public function get_event_by_id($event_id) {
        return \db()->table('event_calendars')->where('id', $event_id)->get()->getRowArray();
    }

    public function event_exists($event_id) {
        return \db()->table('event_calendars')->where('id', $event_id)->countAllResults() > 0;
    }

    public function get_appointment_by_event_id($event_id) {
        return \db()->table('appointments')->where('event_id', $event_id)->get()->getResultArray();
    }

    public function get_subject_by_id($subject_id) {
        return \db()->table('subjects')->where('id', $subject_id)->get()->getRowArray();
    }

    public function user_exists($user_id) {
        return \db()->table('users')->where('id', $user_id)->countAllResults() > 0;
    }

    public function insert_student($data) {
        \db()->table('students')->insert($data);
        return \db()->insertID();
    }

    public function update_student($student_id, $data) {
        return \db()->table('students')->where('id', $student_id)->update($data);
    }

    public function delete_student($student_id) {
        return \db()->table('students')->where('id', $student_id)->delete();
    }

    public function insert_event($data) {
        \db()->table('event_calendars')->insert($data);
        return \db()->insertID();
    }

    public function insert_table($table, $data) {
        \db()->table($table)->insert($data);
        return \db()->insertID();
    }

    public function update_table($table, $where, $data) {
        return \db()->table($table)->where($where)->update($data);
    }

    public function delete_table($table, $where) {
        return \db()->table($table)->where($where)->delete();
    }

    public function update_event($event_id, $data) {
        return \db()->table('event_calendars')->where('id', $event_id)->update($data);
    }

    public function update_appointment($id, $data) {
        return \db()->table('appointments')->where('id', $id)->update($data);
    }

    public function delete_event($event_id) {
        return \db()->table('event_calendars')->where('id', $event_id)->delete();
    }

    public function get_participants_by_event($event_id) {
        return \db()->table('participants')
            ->where('event_id', $event_id)
            ->get()
            ->getResultArray();
    }

    public function get_session_meeting($meeting_id, $appointment_id) {
        return \db()->table('sessions_meetings')
            ->where('meeting_id', $meeting_id)
            ->where('appointment_id', $appointment_id)
            ->get()
            ->getRowArray();
    }

    public function get_by_query($sql, $params = []) {
        if (!empty($params)) {
            return \db()->query($sql, $params)->getResultArray();
        }
        return \db()->query($sql)->getResultArray();
    }

    public function get_row($table, $where) {
        return \db()->table($table)->where($where)->get()->getRowArray();
    }

    public function count_all($table, $where = []) {
        return \db()->table($table)->where($where)->countAllResults();
    }

    public function get_where_result($table, $where = [], $select = '*') {
        return \db()->table($table)->select($select)->where($where)->get()->getResultArray();
    }

    public function get_where_in($table, $column, $values, $additional_where = [], $select = '*') {
        $builder = \db()->table($table)->select($select);
        if (!empty($values)) {
            $builder->whereIn($column, $values);
        }
        if (!empty($additional_where)) {
            $builder->where($additional_where);
        }
        return $builder->get()->getResultArray();
    }
}
