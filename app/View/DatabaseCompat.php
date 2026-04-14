<?php

namespace App\View;

/**
 * CI3-compatible wrapper around CI4's database Connection.
 *
 * Maps old CI3 method names (get_where, insert_id, etc.)
 * to their CI4 equivalents and passes everything else through.
 * Result objects are also wrapped so that ->result_array(),
 * ->row_array(), ->row(), ->num_rows() etc. keep working.
 */
class DatabaseCompat
{
    private $db;
    private $builder;
    private bool $builderUsesDummy = false;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function initBuilder()
    {
        if ($this->builder === null) {
            $this->builder = $this->db->table('__dummy__');
            $this->builderUsesDummy = true;
        }
        return $this->builder;
    }

    /**
     * CI3: $this->db->get_where('table', ['col' => 'val'])
     */
    public function get_where(string $table, $where = null, $limit = null, $offset = null)
    {
        $builder = $this->db->table($table);

        if (is_array($where) && !empty($where)) {
            $builder->where($where);
        }

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return new ResultCompat($builder->get());
    }

    /**
     * CI3: $this->db->get('table')
     */
    public function get(string $table = null, $limit = null, $offset = null)
    {
        if ($table === null) {
            $builder = $this->initBuilder();
            $result = ($limit !== null) ? $builder->get($limit, $offset) : $builder->get();
            $this->builder = null; // CI3 get() resets active builder state
            $this->builderUsesDummy = false;
            return new ResultCompat($result);
        }

        $builder = $this->db->table($table);

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        $result = new ResultCompat($builder->get());
        $this->builder = null; // keep CI3-like reset behavior
        $this->builderUsesDummy = false;
        return $result;
    }

    /**
     * CI3: $this->db->query('SELECT ...')
     */
    public function query(string $sql, $binds = null, bool $setEscapeFlags = true, string $queryClass = '')
    {
        $result = $this->db->query($sql, $binds, $setEscapeFlags, $queryClass);

        if ($result === true || $result === false) {
            return $result;
        }

        return new ResultCompat($result);
    }

    /**
     * CI3: $this->db->insert_id()
     */
    public function insert_id()
    {
        return $this->db->insertID();
    }

    /**
     * CI3: $this->db->affected_rows()
     */
    public function affected_rows()
    {
        return $this->db->affectedRows();
    }

    /**
     * CI3: $this->db->last_query()
     */
    public function last_query()
    {
        return $this->db->getLastQuery()->getQuery();
    }

    /**
     * CI4 method: $this->db->table('name')
     * Returns a CI4 Builder wrapped so its results are also compat.
     */
    public function table(string $tableName)
    {
        return new BuilderCompat($this->db->table($tableName));
    }

    /**
     * CI3: $this->db->select()
     */
    public function select(string $select, bool $escape = null)
    {
        $this->initBuilder()->select($select, $escape);
        return $this;
    }

    /**
     * CI3: $this->db->from()
     */
    public function from(string $from, bool $overwrite = false)
    {
        // When the internal builder starts from '__dummy__', force overwrite
        // on first real FROM to avoid querying a non-existent dummy table.
        if ($this->builderUsesDummy) {
            $overwrite = true;
            $this->builderUsesDummy = false;
        }
        $this->initBuilder()->from($from, $overwrite);
        return $this;
    }

    /**
     * CI3: $this->db->join()
     */
    public function join(string $table, string $cond, string $type = '', bool $escape = null)
    {
        $this->initBuilder()->join($table, $cond, $type, $escape);
        return $this;
    }

    /**
     * CI3: $this->db->where()
     */
    public function where($key, $value = null, bool $escape = null)
    {
        $this->initBuilder()->where($key, $value, $escape);
        return $this;
    }

    /**
     * CI3: $this->db->like()
     */
    public function like($field, string $match = '', string $side = 'both')
    {
        $this->initBuilder()->like($field, $match, $side);
        return $this;
    }

    /**
     * CI3: $this->db->order_by()
     */
    public function order_by(string $order, string $direction = '', bool $escape = null)
    {
        $this->initBuilder()->orderBy($order, $direction, $escape);
        return $this;
    }

    /**
     * CI3: $this->db->limit()
     */
    public function limit(?int $value, ?int $offset = 0)
    {
        $this->initBuilder()->limit($value, $offset);
        return $this;
    }

    /**
     * CI3: $this->db->group_by()
     */
    public function group_by(string $by, bool $escape = null)
    {
        $this->initBuilder()->groupBy($by, $escape);
        return $this;
    }

    /**
     * CI3: $this->db->having()
     */
    public function having($key, $value = null, bool $escape = null)
    {
        $this->initBuilder()->having($key, $value, $escape);
        return $this;
    }

    /**
     * CI3: $this->db->or_where()
     */
    public function or_where($key, $value = null, bool $escape = null)
    {
        $this->initBuilder()->orWhere($key, $value, $escape);
        return $this;
    }

    /**
     * CI3: $this->db->insert()
     */
    public function insert(string $table, $data = null, bool $escape = null)
    {
        return $this->db->table($table)->insert($data, $escape);
    }

    /**
     * CI3: $this->db->update()
     */
    public function update(string $table, $data = null, $where = null, int $limit = null, bool $escape = null)
    {
        $builder = $this->db->table($table);
        if ($where !== null) {
            $builder->where($where);
        }
        return $builder->update($data, $escape);
    }

    /**
     * CI3: $this->db->delete()
     */
    public function delete(string $table, $where = '', int $limit = null, bool $reset = true)
    {
        return $this->db->table($table)->where($where)->delete($limit);
    }

    /**
     * Pass any other method through to the real CI4 connection.
     */
    public function __call($method, $args)
    {
        // CI3-style snake_case builder methods (e.g. where_in, group_start)
        // should run on the active query builder, not on the DB connection.
        $builder = $this->initBuilder();
        $camelMethod = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $method))));

        if (method_exists($builder, $camelMethod)) {
            $result = $builder->$camelMethod(...$args);

            if ($result === $builder) {
                return $this;
            }
            if ($result instanceof \CodeIgniter\Database\BaseResult || $result instanceof \CodeIgniter\Database\ResultInterface) {
                return new ResultCompat($result);
            }
            return $result;
        }

        if (method_exists($this->db, $method)) {
            return $this->db->$method(...$args);
        }

        throw new \BadMethodCallException('Call to undefined method ' . get_class($this->db) . '::' . $method . '()');
    }

    /**
     * Pass property access through to the real CI4 connection.
     */
    public function __get($name)
    {
        return $this->db->$name;
    }
}

/**
 * Wrapper around CI4's Query Builder that returns ResultCompat objects.
 */
class BuilderCompat
{
    private $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * Override get() to return a ResultCompat.
     */
    public function get($limit = null, $offset = null, $reset = true)
    {
        // CI3 compatibility: get('table', $limit, $offset)
        if (is_string($limit)) {
            $table = $limit;
            $ci3Limit = is_int($offset) ? $offset : null;
            $ci3Offset = is_int($reset) ? $reset : null;

            // Replace dummy table used by compatibility builder.
            $this->builder->from($table, true);
            if ($ci3Limit !== null && $ci3Offset !== null) {
                $result = $this->builder->get($ci3Limit, $ci3Offset);
            } elseif ($ci3Limit !== null) {
                $result = $this->builder->get($ci3Limit);
            } else {
                $result = $this->builder->get();
            }

            return new ResultCompat($result);
        }

        if ($limit !== null && $offset !== null) {
            $result = $this->builder->get($limit, $offset, $reset);
        } elseif ($limit !== null) {
            $result = $this->builder->get($limit, 0, $reset);
        } else {
            $result = $this->builder->get();
        }
        return new ResultCompat($result);
    }

    /**
     * CI3: $this->db->table(...)->getWhere(...)
     */
    public function getWhere($where = null, $limit = null, $offset = null)
    {
        $result = $this->builder->getWhere($where, $limit, $offset);
        return new ResultCompat($result);
    }

    /**
     * CI3: get_where on builder
     */
    public function get_where($where = null, $limit = null, $offset = null)
    {
        return $this->getWhere($where, $limit, $offset);
    }

    /**
     * All other builder methods return $this (for chaining)
     * unless they return a result.
     */
    public function __call($method, $args)
    {
        $result = $this->builder->$method(...$args);

        if ($result === $this->builder) {
            return $this;
        }

        if ($result instanceof \CodeIgniter\Database\BaseResult || $result instanceof \CodeIgniter\Database\ResultInterface) {
            return new ResultCompat($result);
        }

        return $result;
    }
}

/**
 * CI3-compatible wrapper around CI4 result objects.
 *
 * Adds result_array(), row_array(), row(), num_rows() etc.
 */
class ResultCompat
{
    private $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function result_array()
    {
        return $this->result->getResultArray();
    }

    public function result($type = 'object')
    {
        if ($type === 'array') {
            return $this->result->getResultArray();
        }
        return $this->result->getResult();
    }

    public function row_array(int $n = 0)
    {
        $rows = $this->result->getResultArray();
        return $rows[$n] ?? null;
    }

    public function row($n = 0, string $type = 'object')
    {
        // CI3 compatibility: row('column_name')
        if (is_string($n)) {
            $column = $n;
            if ($type === 'array') {
                $row = $this->result->getRowArray();
                return $row[$column] ?? null;
            }
            $row = $this->result->getRow();
            return is_object($row) ? ($row->{$column} ?? null) : null;
        }

        if ($n === 0) {
            return ($type === 'array') ? $this->result->getRowArray() : $this->result->getRow();
        }

        if ($type === 'array') {
            $rows = $this->result->getResultArray();
            return $rows[$n] ?? null;
        }
        $rows = $this->result->getResult();
        return $rows[$n] ?? null;
    }

    public function num_rows(): int
    {
        return $this->result->getNumRows();
    }

    public function first_row(string $type = 'object')
    {
        return $this->result->getFirstRow($type);
    }

    public function last_row(string $type = 'object')
    {
        return $this->result->getLastRow($type);
    }

    public function __call($method, $args)
    {
        return $this->result->$method(...$args);
    }

    public function __get($name)
    {
        return $this->result->$name;
    }
}
