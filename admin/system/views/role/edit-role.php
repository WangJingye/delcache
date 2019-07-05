<form id="save-form" action="<?= $this->createUrl('system/role/editRole') ?>" method="post">
    <input type="hidden" name="id" value="<?= isset($this->model['id']) ? $this->model['id'] : '' ?>">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">角色名称</label>
        <div class="col-sm-10">
            <input type="text" name="name" class="form-control"
                   value="<?= isset($this->model['name']) ? $this->model['name'] : '' ?>" placeholder="请输入角色名称">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">描述</label>
        <div class="col-sm-10">
            <textarea name="desc"
                      class="form-control"><?= isset($this->model['desc']) ? $this->model['desc'] : '' ?></textarea>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-2 col-sm-10">
            <input class="btn btn-primary" type="submit" value="保存"/>
        </div>
    </div>
</form>
<?php $this->appendScript('admin/role.js') ?>