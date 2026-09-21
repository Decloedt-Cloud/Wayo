<?php

namespace App\Models;

use CodeIgniter\Model;

class Superadmin_model extends Model {
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

    protected $DBGroup = 'default';

    public function __construct() {
        parent::__construct();
    }

    public function get_teacher_by_user_id($user_id) {
        return \db()->table('teachers')->where('user_id', $user_id)->get()->getRowArray();
    }

    public function get_teacher_id_by_user_id($user_id) {
        return \db()->table('teachers')->where('user_id', $user_id)->get()->getRow('id');
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
        return \db()->table('schools')->where('id', $school_id)->get()->getRow() !== null;
    }

    public function get_student_by_id($student_id) {
        return \db()->table('students')->where('id', $student_id)->get()->getRowArray();
    }

    public function get_student_by_id_and_school($student_id, $school_id) {
        return \db()->table('students')->where('id', $student_id)->where('school_id', $school_id)->get()->getRowArray();
    }

    public function get_student_school_id($student_id) {
        return \db()->table('students')->where('id', $student_id)->get()->getRow('school_id');
    }

    public function get_student_user_id($student_id) {
        return \db()->table('students')->where('id', $student_id)->get()->getRow('user_id');
    }

    public function get_student_code($student_id, $school_id) {
        return \db()->table('students')->where('id', $student_id)->where('school_id', $school_id)->get()->getRow('code');
    }

    public function get_pending_applications($school_id) {
        return \db()->table('students')->where('school_id', $school_id)->where('status', 0)->get()->getResultArray();
    }

    public function get_pending_users($school_id) {
        return \db()->table('users')->where('school_id', $school_id)->where('status', 0)->get()->getResultArray();
    }

    public function get_inactive_schools() {
        // Pending school admissions are stored as status=0 while still active in table (Etat=1).
        return \db()->table('schools')
            ->where('status', 0)
            ->where('Etat', 1)
            ->get()
            ->getResultArray();
    }

    public function get_class_by_id($class_id) {
        return \db()->table('classes')->where('id', $class_id)->get()->getRowArray();
    }

    public function get_classes_by_school($school_id) {
        return \db()->table('classes')->where('school_id', $school_id)->get()->getResultArray();
    }

    public function class_exists($class_id, $school_id) {
        return \db()->table('classes')->where('id', $class_id)->where('school_id', $school_id)->get()->getRow() !== null;
    }

    public function user_exists($user_id) {
        return \db()->table('users')->where('id', $user_id)->get()->getRow() !== null;
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
        return \db()->table('event_calendars')->where('id', $event_id)->get()->getRow() !== null;
    }

    public function get_appointment_by_event_id($event_id) {
        return \db()->table('appointments')->where('event_id', $event_id)->get()->getResultArray();
    }

    public function get_meeting_by_appointment($appointment_id) {
        return \db()->table('sessions_meetings')->where('appointment_id', $appointment_id)->get()->getRowArray();
    }

    public function get_recording_by_id($recording_id) {
        return \db()->table('recordings')->where('id', $recording_id)->get()->getRowArray();
    }

    public function get_active_session($school_id) {
        return \db()->table('sessions')->where('school_id', $school_id)->where('status', 1)->get()->getRow('name');
    }

    public function insert_event($data) {
        \db()->table('event_calendars')->insert($data);
        return \db()->insertID();
    }

    public function update_event($event_id, $data) {
        return \db()->table('event_calendars')->where('id', $event_id)->update($data);
    }

    public function delete_event($event_id) {
        return \db()->table('event_calendars')->where('id', $event_id)->delete();
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

    public function insert_table($table, $data) {
        \db()->table($table)->insert($data);
        return \db()->insertID();
    }

    public function get_recordings_by_recording_id($recording_id) {
        return \db()->table('recordings')->where('id', $recording_id)->get()->getRowArray();
    }

    public function get_student_school_id_by_student_id($student_id) {
        return \db()->table('students')->where('id', $student_id)->get()->getRow('school_id');
    }

    public function get_student_user_id_by_student_id($student_id) {
        return \db()->table('students')->where('id', $student_id)->get()->getRow('user_id');
    }

    public function get_student_code_by_id($student_id, $school_id) {
        return \db()->table('students')->where('id', $student_id)->where('school_id', $school_id)->get()->getRow('code');
    }

    public function get_session_by_school_and_status($school_id, $status = 1) {
        return \db()->table('sessions')->where('school_id', $school_id)->where('status', $status)->get()->getRow('name');
    }

    public function get_students_by_status_and_school($status, $school_id) {
        return \db()->table('students')->where('status', $status)->where('school_id', $school_id)->get()->getResultArray();
    }

    public function get_users_by_status_and_school($status, $school_id) {
        return \db()->table('users')->where('status', $status)->where('school_id', $school_id)->get()->getResultArray();
    }

    public function get_appointments_by_event($event_id) {
        return \db()->table('appointments')->where('event_id', $event_id)->get()->getResultArray();
    }

    public function get_session_meeting_by_appointment($appointment_id) {
        return \db()->table('sessions_meetings')->where('appointment_id', $appointment_id)->get()->getRowArray();
    }

    public function get_participants_by_event($event_id) {
        return \db()->table('participants')->where('event_id', $event_id)->get()->getResultArray();
    }

    /** @param list<int|string> $eventIds @return list<array<string,mixed>> */
    public function get_participants_for_events_batch(array $eventIds): array
    {
        $eventIds = array_values(array_unique(array_filter(array_map('intval', $eventIds), static fn ($id) => $id > 0)));
        if ($eventIds === []) {
            return [];
        }

        return \db()->table('participants')->whereIn('event_id', $eventIds)->get()->getResultArray();
    }

    /** @param list<int|string> $classIds @return array<int, array<string,mixed>> */
    public function get_classes_map_by_ids(array $classIds): array
    {
        $classIds = array_values(array_unique(array_filter(array_map('intval', $classIds), static fn ($id) => $id > 0)));
        if ($classIds === []) {
            return [];
        }
        $rows = \db()->table('classes')->whereIn('id', $classIds)->get()->getResultArray();
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['id']] = $row;
        }

        return $map;
    }

    /** @param list<int|string> $userIds @return array<int, array<string,mixed>> */
    public function get_users_map_by_ids(array $userIds): array
    {
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds), static fn ($id) => $id > 0)));
        if ($userIds === []) {
            return [];
        }
        $rows = \db()->table('users')->select('id, name')->whereIn('id', $userIds)->get()->getResultArray();
        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['id']] = $row;
        }

        return $map;
    }

    public function get_session_meeting($meeting_id, $appointment_id) {
        return \db()->table('sessions_meetings')->where('id', $meeting_id)->where('appointment_id', $appointment_id)->get()->getRowArray();
    }

    public function get_by_query($sql, $params = []) {
        if (!empty($params)) {
            return \db()->query($sql, $params)->getResultArray();
        }
        return \db()->query($sql)->getResultArray();
    }

    public function get_row_by_query($sql, $params = []) {
        if (!empty($params)) {
            return \db()->query($sql, $params)->getRowArray();
        }
        return \db()->query($sql)->getRowArray();
    }

    public function get_row($table, $where = []) {
        return \db()->table($table)->where($where)->get()->getRowArray();
    }

    public function get_where_result($table, $where = []) {
        return \db()->table($table)->where($where)->get()->getResultArray();
    }

    public function update_table($table, $where, $data) {
        return \db()->table($table)->where($where)->update($data);
    }

    public function delete_table($table, $where) {
        $result = \db()->table($table)->where($where)->delete();
        return [
            'success' => $result,
            'affected_rows' => \db()->affectedRows(),
            'error' => \db()->error()
        ];
    }
}
