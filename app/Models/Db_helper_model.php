<?php

namespace App\Models;

use CodeIgniter\Model;

class Db_helper_model extends Model
{
    protected $table            = '';
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

    public function get_by_id($table, $id)
    {
        return \db()->table($table)->where('id', $id)->get()->getRowArray();
    }

    /**
     * CI3-style generic getter used across legacy controllers.
     *
     * @param string $table
     * @param array  $where
     * @param string $returnType row|row_array|result|result_array
     * @return mixed
     */
    public function get($table, $where = [], $returnType = 'result_array')
    {
        $builder = \db()->table($table);
        if (!empty($where) && is_array($where)) {
            $builder->where($where);
        }

        $query = $builder->get();
        switch ($returnType) {
            case 'row':
                return $query->getRow();
            case 'row_array':
                return $query->getRowArray();
            case 'result':
                return $query->getResult();
            case 'result_array':
            default:
                return $query->getResultArray();
        }
    }

    public function get_where($table, $where)
    {
        return \db()->table($table)->where($where)->get()->getRowArray();
    }

    public function custom_insert($table, $data)
    {
        \db()->table($table)->insert($data);
        return \db()->insertID();
    }

    public function custom_update($table, $where, $data)
    {
        return \db()->table($table)->where($where)->update($data);
    }

    public function custom_delete($table, $where)
    {
        return \db()->table($table)->where($where)->delete();
    }
}
