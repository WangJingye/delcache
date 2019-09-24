<?php

namespace component;

class WebService extends \Service
{
    public $page = 1;

    public $pageSize = 10;

    public $total;

    public $totalPage;

    public $list;

    /**
     * @param \Db $selector
     * @param $params
     */
    public function pagination($selector, $params)
    {
        if (isset($params['page']) && (int)$params['page']) {
            $this->page = $params['page'];
        }
        if (isset($params['pageSize']) && (int)$params['pageSize']) {
            $this->pageSize = $params['pageSize'];
        }
        $limit = ($this->page - 1) * $this->pageSize . ',' . $this->pageSize;
        $this->total = $selector->count();
        $this->totalPage = (int)ceil($this->total / $this->pageSize);
        $this->list = $selector->limit($limit)->findAll();
        return $this;
    }
}