<?php

namespace core;

class Db
{
    /** @var  \PDO */
    public $db;
    private $condition = '';
    private $database = [];
    private $table_name = '';
    private $fields = '*';
    private $limit = '';
    private $order = '';
    private static $instance;

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
        if (is_null($this->db)) {
            $this->db = new \PDO("mysql:host={$database['hostname']};dbname={$database['database']};port={$database['port']}", $database['username'], $database['password']);
            $this->db->query("SET NAMES " . $database['charset']);
        }
        $this->database = $database;
    }

    /**
     * @param array $database
     * @return Db
     * @throws \Exception
     */
    public static function instance($database = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($database);
        }
        return self::$instance;
    }

    /**
     * @param $table
     * @return Db
     * @throws \Exception
     */
    public static function table($table)
    {
        $instance = Db::instance();
        $instance->clear();
        $table = $instance->database['prefix'] . trim(preg_replace('/([A-Z])/', '_$1', $table), '_');
        $instance->table_name = strtolower($table);
        return $instance;
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
     * @throws \Exception
     */
    public function find($sql = '')
    {
        if (!$sql) {
            $sql = $this->getSql();
        }
        $stat = $this->db->query($sql);
        if ($stat) {
            $data = $stat->fetch(\PDO::FETCH_ASSOC);
        } else {
            $error = $this->db->errorInfo();
            throw new \Exception($error[2]);
        }
        return $data;
    }

    /**
     * @param string $sql
     * @return mixed
     * @throws \Exception
     */
    public function findAll($sql = '')
    {
        if (!$sql) {
            $sql = $this->getSql();
        }
        $stat = $this->db->query($sql);
        if ($stat) {
            $list = $stat->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $error = $this->db->errorInfo();
            throw new \Exception($error[2]);
        }
        return $list;
    }

    /**
     * @param string $sql
     * @return mixed
     * @throws \Exception
     */
    public function count($sql = '')
    {
        if (!$sql) {
            $sql = $this->getSql('count(*) as count');
        }
        $stat = $this->db->query($sql);
        if ($stat) {
            $count = $stat->fetch(\PDO::FETCH_ASSOC);
            return $count['count'];
        } else {
            $error = $this->db->errorInfo();
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

    /**
     * @throws \Exception
     */
    public static function startTrans()
    {
        Db::instance()->db->beginTransaction();
    }

    /**
     * @throws \Exception
     */
    public static function rollback()
    {
        Db::instance()->db->rollBack();
    }

    /**
     * @throws \Exception
     */
    public static function commit()
    {
        Db::instance()->db->commit();
    }

    /**
     * @param $data
     */
    public function update($data)
    {
        if (!isset($data['update_time'])) {
            $fields = $this->getFields();
            if (in_array('update_time', $fields)) {
                $data['update_time'] = time();
            }
        }
        $sql = $this->array2sql($this->table_name, $data, 'update', $this->condition);
        $this->db->exec($sql);
    }

    /**
     * @param $data
     * @return string
     */
    public function insert($data)
    {
        if (!isset($data['create_time'])) {
            $fields = $this->getFields();
            if (in_array('create_time', $fields)) {
                $data['create_time'] = time();
            }
        }
        $sql = $this->array2sql($this->table_name, $data);
        $this->db->exec($sql);
        return $this->db->lastInsertId();
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function multiInsert($list)
    {
        if (!count($list)) {
            return;
        }
        if (!isset($list[0]['create_time'])) {
            $fields = $this->getFields();
            if (in_array('create_time', $fields)) {
                foreach ($list as $k => $data) {
                    $list[$k]['create_time'] = time();
                }
            }
        }
        $dataSql = [];
        foreach ($list as $data) {
            foreach ($data as $k => $v) {
                $data[$k] = str_replace('"', '\\"', str_replace('\\', '\\\\', $v));
            }
            $dataSql [] = '("' . implode('","', array_values($data)) . '")';
            $sql = 'insert ignore into ' . $this->table_name . ' (`' . implode('`,`', array_keys($data)) . '`) values ';
        }
        $sql .= implode(',', $dataSql);
        if ($this->db->exec($sql) === false) {
            $error = $this->db->errorInfo();
            throw new \Exception($error[2]);
        }
    }

    /**
     * @param array $where
     * @throws \Exception
     */
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
        if ($this->db->exec($sql) === false) {
            $error = $this->db->errorInfo();
            throw new \Exception($error[2]);
        }
    }

    protected function getFields()
    {
        $sql = 'show columns  from ' . $this->table_name;
        $fields = $this->findAll($sql);
        return array_column($fields, 'Field');
    }

    /**
     * @param $tableName
     * @param $data
     * @param string $type
     * @param string $condition
     * @return string
     */
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

    protected function clear()
    {
        $this->condition = '';
        $this->fields = '*';
        $this->limit = '';
        $this->order = '';
    }

}