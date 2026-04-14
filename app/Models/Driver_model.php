<?php

namespace App\Models;

use CodeIgniter\Model;

class Driver_model extends Model {
    protected $table            = 'drivers';
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

    public function __construct()
    {
        parent::__construct();
        $this->school_id = school_id();
        $this->active_session = active_session();
    }


}
