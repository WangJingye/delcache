<?php

namespace core;

class Db
{
    /** @var  \PDO */
    public static $db;
    private $condition = '';
    private $database = [];
    private $table_name = '';
    private $fields = '*';
    private $limit = '';
    private $order = '';


    /**
     * Db constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($database = [])
    {
        if (empty($database)) {
            $database = require COMMON_PATH . 'config/db.php';
        }
        if (empty($database)) {
            throw new \Exception('database config is missing！', 500);
        }
        if (static::$db == null) {
            $db = new \PDO("mysql:host={$database['hostname']};dbname={$database['database']};port={$database['port']}", $database['username'], $database['password']);
            $db->query("SET NAMES " . $database['charset']);
            static::$db = $db;
        }
        $this->database = $database;
    }

    /**
     * @param $table
     * @return Db
     */
    public static function table($table)
    {
        $static = new static();
        $table = $static->database['prefix'] . trim(preg_replace('/([A-Z])/', '_$1', $table), '_');
        $static->table_name = strtolower($table);
        return $static;
    }

    /**
     * @param array $fields
     * @return Db
     */
    public function field($fields = [])
    {
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return string
     */
    public function getSql($field = '')
    {
        if (!$field) {
            $field = $this->fields;
        }
        $sql = 'select ' . $field . ' from ' . $this->table_name;
        if ($this->condition) {
            $sql .= ' where ' . $this->condition;
        }
        if ($this->limit) {
            $sql .= ' limit ' . $this->limit;
        }
        if ($this->order) {
            $sql .= ' order by ' . $this->order;
        }
        return $sql;
    }

    /**
     * @param $size
     * @return Db
     */
    public function limit($size)
    {
        $this->limit = $size;
        return $this;
    }

    /**
     * @param $order
     * @return Db
     */
    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param string $sql
     * @return mixed
     */
    public function find($sql = '')
    {
        if (!$sql) {
            $sql = $this->getSql();
        }
        $stat = static::$db->query($sql);
        if ($stat) {
            $data = $stat->fetch(\PDO::FETCH_ASSOC);
        } else {
            $error = static::$db->errorInfo();
            throw new \Exception($error[2]);
        }
        return $data;
    }

    /**
     * @param $sql
     * @return array
     */
    public function findAll($sql = '')
    {
        if (!$sql) {
            $sql = $this->getSql();
        }
        $stat = static::$db->query($sql);
        if ($stat) {
            $list = $stat->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $error = static::$db->errorInfo();
            throw new \Exception($error[2]);
        }
        return $list;
    }

    public function count($sql = '')
    {
        if (!$sql) {
            $sql = $this->getSql('count(*) as count');
        }
        $stat = static::$db->query($sql);
        if ($stat) {
            $count = $stat->fetch(\PDO::FETCH_ASSOC);
            return $count['count'];
        } else {
            $error = static::$db->errorInfo();
            throw new \Exception($error[2]);
        }
    }

    /**
     * @param array $conditions
     * @return Db
     */
    public function where($conditions = [])
    {
        if (is_array($conditions)) {
            $fields = [];
            foreach ($conditions as $key => $value) {
                $fields[] = '`' . $key . '`="' . str_replace('"', '\\"', str_replace('\\', '\\\\', $value)) . '"';
            }
            if (count($fields)) {
                if ($this->condition) {
                    $this->condition .= ' and ';
                }
                $this->condition .= implode(' and ', $fields);
            }
        } else {
            if ($this->condition) {
                $this->condition .= ' and ' . $conditions;
            } else {
                $this->condition .= $conditions;
            }
        }
        return $this;
    }

    public function rename($rename)
    {
        $this->rename = $rename;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public static function startTrans()
    {
        static::$db->beginTransaction();
    }

    /**
     * @throws \Exception
     */
    public static function rollback()
    {
        static::$db->rollBack();
    }

    /**
     * @throws \Exception
     */
    public static function commit()
    {
        static::$db->commit();
    }

    /**
     * @param $data
     */
    public function update($data)
    {
        $sql = $this->array2sql($this->table_name, $data, 'update', $this->condition);
        static::$db->exec($sql);
    }

    /**
     * @param $data
     * @return string
     */
    public function insert($data)
    {
        $sql = $this->array2sql($this->table_name, $data);
        static::$db->exec($sql);
        return static::$db->lastInsertId();
    }

    /**
     * @param array $data
     * @return string
     */
    public function multiInsert($list)
    {
        $dataSql = [];
        foreach ($list as $data) {
            foreach ($data as $k => $v) {
                $data[$k] = str_replace('"', '\\"', str_replace('\\', '\\\\', $v));
            }
            $dataSql [] = '("' . implode('","', array_values($data)) . '")';
            $sql = 'insert ignore into ' . $this->table_name . ' (`' . implode('`,`', array_keys($data)) . '`) values ';
        }
        $sql .= implode(',', $dataSql);
        if (static::$db->exec($sql) === false) {
            $error = static::$db->errorInfo();
            throw new \Exception($error[2]);
        }
    }

    public function delete($where = [])
    {
        if (!empty($where)) {
            $this->where($where);
        }
        $sql = 'delete from ' . $this->table_name;
        if ($this->condition) {
            $sql .= ' where ' . $this->condition;
        }
        if ($this->limit) {
            $sql .= ' limit ' . $this->limit;
        }
        if (static::$db->exec($sql) === false) {
            $error = static::$db->errorInfo();
            throw new \Exception($error[2]);
        }
    }

    public static function array2sql($tableName, $data, $type = 'insert', $condition = '')
    {
        if (empty($data)) {
            return '';
        }
        if ($type == 'insert') {
            foreach ($data as $k => $v) {
                $data[$k] = str_replace('"', '\\"', str_replace('\\', '\\\\', $v));
            }
            $sql = 'insert into ' . $tableName . ' (`' . implode('`,`', array_keys($data)) . '`) values ("' . implode('","', array_values($data)) . '")';
        } else {
            $fields = [];
            foreach ($data as $key => $value) {
                $fields[] = '`' . $key . '`="' . str_replace('"', '\\"', str_replace('\\', '\\\\', $value)) . '"';
            }
            if ($condition) {
                $condition = ' where ' . $condition;
            }
            $sql = 'update ' . $tableName . ' set ' . implode(',', $fields) . $condition;
        }
        return $sql;
    }

}