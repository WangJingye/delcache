<?php

namespace admin\extend\input;

class SelectInput extends \ObjectAccess
{
    public $name = 'key';
    public $list = [];
    public $type = 'radio';
    public $checked;
    public $multi;

    public static $instance;

    public function __construct($list, $checked, $name, $type)
    {
        $this->name = $name;
        $this->list = $list;
        $this->checked = $checked;
        $this->type = $type;
        $this->multi = is_array($checked) ? true : false;
    }

    public static function instance($list, $checked, $name, $type = 'radio')
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($list, $checked, $name, $type);
        } else {
            self::$instance->list = $list;
            self::$instance->name = $name;
            self::$instance->checked = $checked;
            self::$instance->type = $type;
            self::$instance->multi = is_array($checked) ? true : false;;
        }
        return self::$instance;
    }

    public function show()
    {

        if (in_array($this->type, ['radio', 'checkbox'])) {
            $html = '<div class="form-radio-group" style="margin-top: 0.45rem;">';
            foreach ($this->list as $k => $v) {
                $checked = $this->multi ? in_array($k, $this->checked) : $this->checked == $k;
                $html .= '<div class="custom-control custom-' . $this->type . ' custom-control-inline">' .
                    '<input class="custom-control-input" type="' . $this->type . '" name="' . $this->name . '" id="' . $this->name . $k . '"' .
                    'value="' . $k . '" ' . ($checked ? 'checked' : '') . '>' .
                    '<label class="custom-control-label" for="' . $this->name . $k . '">' . $v . '</label>' .
                    '</div>';
            }
            $html .= '</div>';
        } else if (in_array($this->type, ['select', 'select2'])) {
            $html = '<select name="' . $this->name . '" class="form-control ' . $this->type . '" ' . ($this->multi ? 'multiple' : '') . '>';
            if (!$this->multi) {
                $html .= '<option value="">请选择</option>';
            }
            foreach ($this->list as $k => $v) {
                $checked = $this->multi ? in_array($k, $this->checked) : $this->checked == $k;
                $html .= '<option value="' . $k . '" ' . ($checked ? 'selected' : '') . '>' . $v . '</option>';
            }
            $html .= '</select>';
        } else {

        }

        return $html;
    }
}