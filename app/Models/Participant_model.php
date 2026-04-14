<?php

namespace App\Models;

use CodeIgniter\Model;

class Participant_model extends Model {
    protected $table            = 'participants';
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

    public function get_meeting_participants($meetingID) {
        return \db()->table('participants')->where('meeting_id', $meetingID)->get()->getResultArray();
    }

    public function assign_to_breakout($userID, $roomID) {
        $data = [
            'user_id' => $userID,
            'breakout_room_id' => $roomID,
            'assigned_at' => date("Y-m-d H:i:s")
        ];
        return \db()->table('breakout_assignments')->insert($data);
    }
}

