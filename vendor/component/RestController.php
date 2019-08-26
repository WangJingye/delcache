<?php

namespace component;

class RestController extends \Controller
{

    public $table = null;

    public function indexAction()
    {
        try {
            if (!\App::$request->isGet()) {
                throw new \Exception('bad request');
            }
            if (isset(\App::$request->params['id']) && \App::$request->params['id']) {
                $res = $this->findModel(\App::$request->params['id']);
            } else {
                $primaryKey = \Db::table($this->table)->getPrimaryKey();
                $res = \Db::table($this->table)->order($primaryKey . ' desc')->limit($this->getLimit())->findAll();
            }
            $this->success('获取成功', $res);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

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

    public function updateAction()
    {
        try {
            if (!\App::$request->isPut()) {
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

    public function deleteAction()
    {
        try {
            if (!\App::$request->isDelete()) {
                throw new \Exception('bad request');
            }
            $params = \App::$request->params;
            if (!isset($params['id']) || !(int)$params['id']) {
                throw new \Exception('bad request');
            }
            $this->findModel($params['id']);
            \Db::table($this->table)->delete(['id' => $params['id']]);
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