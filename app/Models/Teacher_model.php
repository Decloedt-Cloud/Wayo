<?php

namespace App\Models;

use CodeIgniter\Model;

class Teacher_model extends Model {
    protected $table            = 'teachers';
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

    public function get_teacher_by_user_id($user_id, $school_id = null) {
        $resolvedSchoolId = (int) ($school_id ?? 0);
        if ($resolvedSchoolId <= 0) {
            $session = \Config\Services::session();
            $resolvedSchoolId = (int) ($session->get('school_id') ?? $session->get('active_school_id') ?? 0);
        }

        $builder = \db()->table('teachers')->where('user_id', $user_id);
        if ($resolvedSchoolId > 0) {
            $row = (clone $builder)->where('school_id', $resolvedSchoolId)->get()->getRowArray();
            if (!empty($row)) {
                return $row;
            }
        }

        return $builder->get()->getRowArray();
    }

    public function get_user_by_id($user_id) {
        return \db()->table('users')->where('id', $user_id)->get()->getRowArray();
    }

    public function get_school_by_id($school_id) {
        return \db()->table('schools')->where('id', $school_id)->get()->getRowArray();
    }

    public function get_active_school($school_id) {
        return \db()->table('schools')->where('id', $school_id)->where('status', 1)->get()->getRowArray();
    }

    public function school_exists($school_id) {
        return \db()->table('schools')->where('id', $school_id)->countAllResults() > 0;
    }

    public function get_class_by_id($class_id) {
        return \db()->table('classes')->where('id', $class_id)->get()->getRowArray();
    }

    public function class_exists($class_id, $school_id) {
        return \db()->table('classes')->where('id', $class_id)->where('school_id', $school_id)->countAllResults() > 0;
    }

    public function user_exists($user_id) {
        return \db()->table('users')->where('id', $user_id)->countAllResults() > 0;
    }

    public function get_section_by_id($section_id) {
        return \db()->table('sections')->where('id', $section_id)->get()->getRowArray();
    }

    public function get_section_name($section_id) {
        $row = \db()->table('sections')->select('name')->where('id', $section_id)->get()->getRowArray();
        return $row['name'] ?? null;
    }

    public function get_teacher_id_by_user_id($user_id) {
        $row = \db()->table('teachers')->select('id')->where('user_id', $user_id)->get()->getRowArray();
        return $row['id'] ?? null;
    }

    public function get_teacher_permissions($teacher_id) {
        return \db()->table('teacher_permissions')->where('teacher_id', $teacher_id)->get()->getResultArray();
    }

    public function get_teacher_classes($teacher_id) {
        return \db()->table('teacher_permissions')
            ->select('class_id')
            ->where('teacher_id', $teacher_id)
            ->where('attendance', 1)
            ->get()
            ->getResultArray();
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

    public function get_meeting_by_appointment($meeting_id, $appointment_id) {
        return \db()->table('sessions_meetings')
            ->where('meeting_id', $meeting_id)
            ->where('appointment_id', $appointment_id)
            ->get()
            ->getRowArray();
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

    /** @param list<int|string> $classIds @return array<int, array<string,mixed>> id => row */
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

    /** @param list<int|string> $userIds @return array<int, array<string,mixed>> id => row */
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

    public function get_recording_by_id($recording_id) {
        return \db()->table('recordings')->where('id', $recording_id)->get()->getRowArray();
    }

    public function insert_appointment($data) {
        \db()->table('appointments')->insert($data);
        return \db()->insertID();
    }

    public function update_appointment($id, $data) {
        return \db()->table('appointments')->where('id', $id)->update($data);
    }

    public function delete_appointment($id) {
        return \db()->table('appointments')->where('id', $id)->delete();
    }

    public function insert_recording($data) {
        \db()->table('recordings')->insert($data);
        return \db()->insertID();
    }

    public function update_recording($recording_id, $data) {
        return \db()->table('recordings')->where('recording_id', $recording_id)->update($data);
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

    public function insert_participant($data) {
        return \db()->table('participants')->insert($data);
    }

    public function delete_participants_by_event($event_id) {
        return \db()->table('participants')->where('event_id', $event_id)->delete();
    }

    public function insert_session_meeting($data) {
        \db()->table('sessions_meetings')->insert($data);
        return \db()->insertID();
    }

    public function insert_appointment_participant($data) {
        return \db()->table('appointment_participants')->insert($data);
    }

    public function get_recordings_by_school($school_id, $filters = []) {
        $builder = \db()->table('recordings r');
        $builder->select('r.*, c.name as class_name');
        $builder->join('classes c', 'r.class_id = c.id', 'left');
        $builder->join('appointments a', 'r.appointment_id = a.id', 'inner');
        $builder->join('appointment_participants ap', 'a.id = ap.appointment_id', 'left');
        $builder->where('r.school_id', $school_id);

        if (!empty($filters['meeting_name'])) {
            $builder->like('r.name', $filters['meeting_name'], 'both');
        }

        if (!empty($filters['date'])) {
            $builder->where('DATE(r.start_time)', $filters['date']);
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $builder->where('r.start_time >=', $filters['date_from']);
            $builder->where('r.start_time <=', $filters['date_to']);
        }

        $builder->groupBy('r.id');
        $builder->orderBy('r.created_at', 'DESC');
        return $builder->get()->getResultArray();
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

    public function get_appointments_by_event($event_id) {
        return \db()->table('appointments')->where('event_id', $event_id)->get()->getResultArray();
    }

    public function get_session_meeting_by_appointment($appointment_id) {
        return \db()->table('sessions_meetings')->where('appointment_id', $appointment_id)->get()->getRowArray();
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

    public function count_all($table, $where = []) {
        $builder = \db()->table($table);
        if (!empty($where)) {
            $builder->where($where);
        }
        return $builder->countAllResults();
    }
}
