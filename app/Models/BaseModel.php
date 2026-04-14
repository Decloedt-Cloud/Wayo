<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model {
    protected $table            = 'base';
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

    public function get_where($table, $conditions, $return_type = 'row') {
        $query = \db()->table($table)->where($conditions)->get();
        
        switch ($return_type) {
            case 'row':
                return $query->getRow();
            case 'row_array':
                return $query->getRowArray();
            case 'result':
                return $query->getResult();
            case 'result_array':
                return $query->getResultArray();
            case 'count':
                return $query->numRows();
            default:
                return $query->getRow();
        }
    }

    public function get_all($table, $order_by = null, $order_direction = 'ASC', $limit = null, $offset = null) {
        $builder = \db()->table($table);
        if ($order_by) {
            $builder->orderBy($order_by, $order_direction);
        }
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        return $builder->get()->getResultArray();
    }

    public function insert_table($table, $data) {
        \db()->table($table)->insert($data);
        return \db()->insertID();
    }

    public function update_table($table, $data, $conditions) {
        return \db()->table($table)->where($conditions)->update($data);
    }

    public function delete_table($table, $conditions) {
        return \db()->table($table)->where($conditions)->delete();
    }

    public function count_all_results($table, $conditions = null) {
        $builder = \db()->table($table);
        if ($conditions) {
            $builder->where($conditions);
        }
        return $builder->countAllResults();
    }

    public function exists($table, $conditions) {
        return \db()->table($table)->where($conditions)->countAllResults() > 0;
    }

    public function get_last_query() {
        return \db()->getLastQuery();
    }

    protected function _get_by_field($table, $field_name, $field_value, $return_type = 'row') {
        return $this->get_where($table, [$field_name => $field_value], $return_type);
    }

    protected function _get_by_id($table, $id, $return_type = 'row') {
        return $this->get_where($table, ['id' => $id], $return_type);
    }

    protected function _update_by_id($table, $id, $data) {
        return $this->update_table($table, $data, ['id' => $id]);
    }

    protected function _delete_by_id($table, $id) {
        return $this->delete_table($table, ['id' => $id]);
    }

    public function get_row($table, $conditions) {
        return $this->get_where($table, $conditions, 'row_array');
    }

    public function get_result($table, $conditions) {
        return $this->get_where($table, $conditions, 'result_array');
    }

    public function insert_batch($table, $data) {
        return \db()->table($table)->insertBatch($data);
    }

    public function update_batch($table, $data, $where_key) {
        return \db()->table($table)->updateBatch($data, $where_key);
    }

    public function delete_batch($table, $conditions) {
        return \db()->table($table)->where($conditions)->delete();
    }

    public function like($table, $column, $match, $type = 'both') {
        return \db()->table($table)->like($column, $match, $type)->get()->getResultArray();
    }

    public function or_like($table, $conditions) {
        $builder = \db()->table($table);
        $first = true;
        foreach ($conditions as $column => $match) {
            if ($first) {
                $builder->like($column, $match);
                $first = false;
            } else {
                $builder->orLike($column, $match);
            }
        }
        return $builder->get()->getResultArray();
    }

    public function get_with_join($table, $join_table, $join_condition, $select = '*', $conditions = [], $join_type = 'inner') {
        $builder = \db()->table($table);
        $builder->select($select);
        $builder->join($join_table, $join_condition, $join_type);
        
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        
        return $builder->get()->getResultArray();
    }

    public function get_with_multiple_joins($table, $joins, $select = '*', $conditions = [], $group_by = null) {
        $builder = \db()->table($table);
        $builder->select($select);
        
        foreach ($joins as $join) {
            $builder->join($join['table'], $join['condition'], $join['type'] ?? 'inner');
        }
        
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        
        if ($group_by) {
            $builder->groupBy($group_by);
        }
        
        return $builder->get()->getResultArray();
    }

    public function count($table, $conditions = []) {
        $builder = \db()->table($table);
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        return $builder->countAllResults();
    }

    public function max($table, $column, $conditions = []) {
        $builder = \db()->table($table);
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        $builder->selectMax($column);
        $query = $builder->get();
        $row = $query->getRow();
        return $row ? $row->$column : null;
    }

    public function sum($table, $column, $conditions = []) {
        $builder = \db()->table($table);
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        $builder->selectSum($column);
        $query = $builder->get();
        $row = $query->getRow();
        return $row ? ($row->$column ?? 0) : 0;
    }

    public function avg($table, $column, $conditions = []) {
        $builder = \db()->table($table);
        if (!empty($conditions)) {
            $builder->where($conditions);
        }
        $builder->selectAvg($column);
        $query = $builder->get();
        $row = $query->getRow();
        return $row ? ($row->$column ?? 0) : 0;
    }

    public function query($sql, $return_type = 'result_array') {
        $query = \db()->query($sql);
        if ($return_type === 'row') {
            return $query->getRow();
        } elseif ($return_type === 'row_array') {
            return $query->getRowArray();
        } elseif ($return_type === 'result') {
            return $query->getResult();
        }
        return $query->getResultArray();
    }

    public function transBegin() {
        \db()->transBegin();
    }

    public function transCommit() {
        if (\db()->transStatus() === false) {
            \db()->transRollback();
            return false;
        }
        \db()->transCommit();
        return true;
    }

    public function trans_status() {
        return \db()->transStatus();
    }
}
