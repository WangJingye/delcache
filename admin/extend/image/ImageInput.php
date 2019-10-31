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
        if ($count > 1) {
            $name = $name . '[]';
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
            if ($count > 1) {
                $name = $name . '[]';
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
        foreach ($this->images as $image) {
            $html .= ' <div class="fileinput-box">' .
                '<img src="' . $image . '">' .
                '<input type="hidden" name="' . $this->name . '" value="' . $image . '">' .
                '<div class="fileinput-button">' .
                '<div class="plus-symbol" style="display: none">+</div>' .
                '<input class="fileinput-input" type="file" name="' . $this->name . '">' .
                '</div>' .
                '<div class="file-remove-btn">' .
                '<div class="btn btn-sm btn-outline-danger" style="font-size: 0.5rem;">删除</div>' .
                '</div></div>';
        }
        if (count($this->images) < $this->count) {
            $html .= ' <div class="fileinput-box">' .
                '<div class="fileinput-button">' .
                '<div class="plus-symbol" > +</div >' .
                '<input class="fileinput-input add-new" type = "file" name = "'.$this->name.'" >' .
                '</div ></div >';
        }
        $html .= '</div>';
        return $html;
    }
}