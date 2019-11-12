<?php

namespace admin\extend\image;

class ImageInput extends \ObjectAccess
{
    public $count = 1;
    public $name = 'file';
    public $images = [];

    public static $instance;

    public function __construct($images, $name = 'file', $count = 1)
    {
        if (!is_array($images)) {
            $images = $images ? explode(',', $images) : [];
        }
        $this->name = $name;
        $this->images = $images;
        $this->count = $count;
    }

    public static function instance($images, $name = 'file', $count = 1)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($images, $name, $count);
        } else {
            if (!is_array($images)) {
                $images = $images ? explode(',', $images) : [];
            }
            self::$instance->name = $name;
            self::$instance->images = $images;
            self::$instance->count = $count;
        }
        return self::$instance;
    }

    public function show()
    {
        $html = '<div class="fileinput-box-list" data-max="' . $this->count . '">';
        foreach ($this->images as $key => $image) {
            $name = $this->name;
            if ($this->count != 1) {
                $name = $name . '[' . $key . ']';
            }
            $html .= ' <div class="fileinput-box">' .
                '<img src="' . $image . '">' .
                '<input type="hidden" name="' . $name . '" value="' . $image . '">' .
                '<div class="fileinput-button">' .
                '<div class="plus-symbol" style="display: none">+</div>' .
                '<input class="fileinput-input" type="file" name="' . $name . '">' .
                '</div>' .
                '<div class="file-remove-btn">' .
                '<div class="btn btn-sm btn-outline-danger" style="font-size: 0.5rem;">删除</div>' .
                '</div></div>';
        }
        if (count($this->images) < $this->count) {
            $name = $this->name;
            if ($this->count != 1) {
                $name = $name . '[]';
            }
            $html .= ' <div class="fileinput-box">' .
                '<div class="fileinput-button">' .
                '<div class="plus-symbol" > +</div >' .
                '<input class="fileinput-input add-new" type = "file" name = "' . $name . '" >' .
                '</div ></div >';
        }
        $html .= '</div>';
        return $html;
    }
}