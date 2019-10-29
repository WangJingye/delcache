<?php

class ObjectAccess implements ArrayAccess, Iterator
{

    /**
     * 定义一个数组用于保存数据
     *
     * @access private
     * @var array
     */
    private $data = [];

    /**
     * 以对象方式访问数组中的数据
     *
     * @access public
     * @param string 数组元素键名
     */
    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * 以对象方式添加一个数组元素
     *
     * @access public
     * @param string 数组元素键名
     * @param mixed数组元素值
     * @return mixed
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * 以对象方式判断数组元素是否设置
     *
     * @access public
     * @param 数组元素键名
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * 以对象方式删除一个数组元素
     *
     * @access public
     * @param 数组元素键名
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * 以数组方式向data数组添加一个元素
     *
     * @access public
     * @abstracting ArrayAccess
     * @param string 偏移位置
     * @param mixed元素值
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * 以数组方式获取data数组指定位置元素
     *
     * @access public
     * @abstracting ArrayAccess
     * @param 偏移位置
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }

    /**
     * 以数组方式判断偏移位置元素是否设置
     *
     * @access public
     * @abstracting ArrayAccess
     * @param 偏移位置
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * 以数组方式删除data数组指定位置元素
     *
     * @access public
     * @abstracting ArrayAccess
     * @param 偏移位置
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    public function current()
    {
        return current($this->data);
    }

    public function next()
    {
        return next($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function toArray()
    {
        return $this->data;
    }

    public function uuid($len, $radix)
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $uuid = [];

        for ($i = 0; $i < $len; $i++) {
            $uuid[$i] = $chars[rand(0, $radix)];
        }
        return implode('', $uuid);

    }

}