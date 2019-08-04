<form class="form-box col-12 col-sm-8 col-md-6" id="save-form" action="<?= \App::$urlManager->createUrl('system/role/edit-role') ?>" method="post">
    <input type="hidden" name="id" value="<?= isset($this->model['id']) ? $this->model['id'] : '' ?>">
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">角色名称</label>
        <div class="col-sm-8">
            <input type="text" name="name" class="form-control"
                   value="<?= isset($this->model['name']) ? $this->model['name'] : '' ?>" placeholder="请输入角色名称">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">描述</label>
        <div class="col-sm-8">
            <textarea name="desc"
                      class="form-control"><?= isset($this->model['desc']) ? $this->model['desc'] : '' ?></textarea>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-4 col-sm-8">
            <input class="btn btn-primary" type="submit" value="保存"/>
        </div>
    </div>
</form>
<?php $this->appendScript('role.js') ?>