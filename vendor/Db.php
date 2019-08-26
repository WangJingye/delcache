<?php

use component\DataBase;

class Db
{
    private $db;
    private $condition = '';
    private $database = [];
    private $table_name = '';
    private $fields = '*';
    private $limit = '';
    private $order = '';
    private $rename = '';
    private $join = [];

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
        $this->database = $database;
        $this->db = DataBase::instance($database);
    }

    public function rename($as)
    {
        $this->rename = $as;
        return $this;
    }

    /**
     * @param $table
     * @return Db
     * @throws \Exception
     */
    public static function table($table)
    {
        $instance = new Db();
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
        if ($this->rename) {
            $sql .= ' as ' . $this->rename;
        }
        if (count($this->join)) {
            foreach ($this->join as $v) {
                $sql .= ' ' . $v['join_type'] . ' join ' . $v['table_name'] . ' as ' . $v['as'] . ' on ' . $v['on'];
            }
        }
        if ($this->condition) {
            $sql .= ' where ' . $this->condition;
        }

        if ($this->order) {
            $sql .= ' order by ' . $this->order;
        }
        if ($this->limit) {
            $sql .= ' limit ' . $this->limit;
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
                $k = $key;
                //没有别名
                if (strpos($key, '.') === false) {
                    $k = '`' . $key . '`';
                }
                $symbol = '=';
                $flag = 0;
                $v = $value;
                //value 为数组时,第一个为符号，第二个为值
                if (is_array($value)) {
                    $symbol = $value[0];
                    $v = $value[1];
                    //值为数组时，以逗号拼接成字符串
                    if (is_array($value[1])) {
                        $flag = 1;
                        $v = '("' . implode('","', $value[1]) . '")';
                    }
                }
                if ($flag == 0) {
                    $v = '"' . str_replace('"', '\\"', str_replace('\\', '\\\\', $v)) . '"';
                }
                $fields[] = $k . ' ' . $symbol . ' ' . $v;
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
        DataBase::instance()->beginTransaction();
    }

    /**
     * @throws \Exception
     */
    public static function rollback()
    {
        DataBase::instance()->rollback();
    }

    /**
     * @throws \Exception
     */
    public static function commit()
    {
        DataBase::instance()->commit();
    }

    /**
     * @param $data
     */
    public function update($data)
    {
        if (!isset($data['update_time']) || $data['update_time'] == '') {
            $fields = $this->getFields();
            if (isset($fields['update_time'])) {
                $data['update_time'] = time();
            }
        }
        $sql = $this->array2sql($this->table_name, $data, 'update', $this->condition);
        if ($this->db->exec($sql) === false) {
            $error = $this->db->errorInfo();
            throw new \Exception($error[2]);
        }
    }

    /**
     * @param $data
     * @return string
     */
    public function insert($data)
    {
        $fields = $this->getFields();
        //自动添加创建时间
        if ((!isset($data['create_time']) || $data['create_time'] == '') && isset($fields['create_time'])) {
            $data['create_time'] = time();
        }
        if ((!isset($data['update_time']) || $data['update_time'] == '') && isset($fields['update_time'])) {
            $data['update_time'] = time();
        }
        //未指定主键值时，主键字段不插入
        foreach ($fields as $field => $v) {
            //主键
            if ($v['Key'] == 'PRI') {
                if (isset($data[$field]) && $data[$field] == '') {
                    unset($data[$field]);
                }
                break;
            }
        }
        $sql = $this->array2sql($this->table_name, $data);
        if ($this->db->exec($sql) === false) {
            $error = $this->db->errorInfo();
            throw new \Exception($error[2]);
        }
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
        $fields = $this->getFields();
        //自动添加创建时间
        if ((!isset($list[0]['create_time']) || $list[0]['create_time'] == '') && isset($fields['create_time'])) {
            foreach ($list as $k => $data) {
                $list[$k]['create_time'] = time();
            }
        }
        if ((!isset($list[0]['update_time']) || $list[0]['update_time'] == '') && isset($fields['update_time'])) {
            foreach ($list as $k => $data) {
                $list[$k]['update_time'] = time();
            }
        }
        //未指定主键值时，主键字段不插入
        foreach ($fields as $field => $v) {
            //主键
            if ($v['Key'] == 'PRI') {
                foreach ($list as $f => $data) {
                    if (isset($data[$field]) && $data[$field] == '') {
                        unset($list[$f][$field]);
                    }
                }
                break;
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

    /**
     * @return array
     * @throws \Exception
     */
    public function getFields()
    {
        $sql = 'show columns from ' . $this->table_name . ';';
        $rows = $this->findAll($sql);
        return array_column($rows, null, 'Field');
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getKeys()
    {
        $sql = 'show keys from ' . $this->table_name . ';';
        $rows = $this->findAll($sql);
        return $rows;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getComments()
    {
        $sql = 'SELECT column_name, column_comment FROM information_schema.columns WHERE table_name = \'' . $this->table_name . '\' and table_schema=\'' . $this->database['database'] . '\';';
        $rows = $this->findAll($sql);
        return $rows;
    }

    public function getPrimaryKey()
    {
        $sql = 'SELECT column_name FROM INFORMATION_SCHEMA.`KEY_COLUMN_USAGE` WHERE table_name=\'' . $this->table_name . '\' AND CONSTRAINT_SCHEMA=\'' . $this->database['database'] . '\'AND constraint_name=\'PRIMARY\';';
        $row = $this->find($sql);
        return $row['column_name'];
    }

    /**
     * @param array|string|mixed $name
     * @param $on
     * @param string $join_type
     * @return $this
     */
    public function join($name, $on, $join_type = 'left')
    {
        $as = '';
        if (is_array($name)) {
            $table = $this->database['prefix'] . trim(preg_replace('/([A-Z])/', '_$1', array_values($name)[0]), '_');
            //如果未设置key
            if (array_keys($name)[0] != '0') {
                $as = array_keys($name)[0];
            }
        } else {
            $table = $this->database['prefix'] . trim(preg_replace('/([A-Z])/', '_$1', $name), '_');
        }
        $table = strtolower($table);
        $this->join[] = [
            'table_name' => $table,
            'as' => $as == '' ? $table : $as,
            'on' => $on,
            'join_type' => $join_type,
        ];
        return $this;
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
        $this->join = [];
    }

}