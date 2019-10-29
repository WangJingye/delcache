<?php

namespace component;

class RestController extends \Controller
{

    public $table = null;

    public $actions = [
        'index',
        'create',
        'update',
        'delete'
    ];

    /**
     * $filter有值时使用当前值进行数据过滤，否则不进行
     * @var bool
     */
    public $filter = false;

    public function init()
    {
        if ($this->filter) {
            \App::$request->params->load([$this->filter => \App::$user['id']]);
        }
        if (in_array(\App::$request->action, self::getActions()) && !in_array(\App::$request->action, $this->actions)) {
            throw new \Exception('404 NOT FOUND', 404);
        }
        parent::init();
    }

    protected function getActions()
    {
        return [
            'index',
            'create',
            'update',
            'delete'
        ];
    }

    //列表
    public function indexAction()
    {
        try {
            if (!\App::$request->isGet()) {
                throw new \Exception('bad request');
            }
            $params = \App::$request->params;
            $selector = \Db::table($this->table);
            $primaryKey = $selector->getPrimaryKey();
            $fields = $selector->getFields();
            foreach ($params as $key => $value) {
                if (isset($fields[$key])) {
                    $selector->where([$key => $value]);
                }
            }
            $res = $selector->order($primaryKey . ' desc')
                ->limit($this->getLimit())->findAll();
            $this->success('获取成功', $res);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    //创建
    public function createAction()
    {
        try {
            if (!\App::$request->isPost()) {
                throw new \Exception('bad request');
            }
            $post = \App::$request->params;
            $id = \Db::table($this->table)->insert($post);
            $this->success('创建成功', ['id' => $id]);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    //更新
    public function updateAction()
    {
        try {
            if (!\App::$request->isPost()) {
                throw new \Exception('bad request');
            }
            $params = \App::$request->params;
            if (!isset($params['id']) || !(int)$params['id']) {
                throw new \Exception('bad request');
            }
            $id = $params['id'];
            unset($params['id']);
            $primaryKey = \Db::table($this->table)->getPrimaryKey();
            \Db::table($this->table)->where([$primaryKey => $id])->update($params);
            $this->success('更新成功');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    //详情
    public function viewAction()
    {
        try {
            if (!\App::$request->isGet()) {
                throw new \Exception('bad request');
            }
            $model = $this->findModel(\App::$request->params['id']);
            if ($this->filter) {
                if ($model[$this->filter] != \App::$user['id']) {
                    throw new \Exception('数据有误');
                }
            }
            $this->success('获取成功', $model);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    //删除
    public function deleteAction()
    {
        try {
            if (!\App::$request->isPost()) {
                throw new \Exception('bad request');
            }
            $params = \App::$request->params;
            if (!isset($params['id']) || !(int)$params['id']) {
                throw new \Exception('bad request');
            }
            $primaryKey = \Db::table($this->table)->getPrimaryKey();
            $model = $this->findModel(\App::$request->params['id']);
            if ($this->filter) {
                if ($model[$this->filter] != \App::$user['id']) {
                    throw new \Exception('数据有误');
                }
            }
            \Db::table($this->table)->delete([$model[$primaryKey] => $params['id']]);
            $this->success();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function findModel($id)
    {
        $primaryKey = \Db::table($this->table)->getPrimaryKey();
        $model = \Db::table($this->table)->where([$primaryKey => $id])->find();
        if (!$model) {
            throw new \Exception('bad request');
        }
        return $model;
    }

    /**
     * 获取分页
     * @return string
     */
    public function getLimit()
    {
        $get = \App::$request->params;
        $page = 1;
        if (isset($get['page']) && $get['page'] != '') {
            $page = $get['page'];
        }
        $perPage = 10;
        if (isset($get['per-page']) && $get['per-page'] != '') {
            $perPage = $get['per-page'];
        }
        return ($page - 1) * $perPage . ',' . $perPage;
    }
}