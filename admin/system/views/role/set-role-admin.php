<form id="save-role-admin-form" action="<?= \App::$urlManager->createUrl('system/role/set-role-admin') ?>"
      method="post">
    <input type="hidden" name="id" value="<?= $this->model['id']?>">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">角色名称</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" readonly
                   value="<?= $this->model['name']?>" placeholder="请输入角色名称">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">所选用户</label>
        <div class="col-sm-10">
            <?= \admin\extend\input\SelectInput::instance(array_column($this->adminList, 'realname', 'admin_id'), $this->adminIdList, 'admin_id[]', 'select2')->show(); ?>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-2 col-sm-10">
            <input class="btn btn-primary" type="submit" value="保存"/>
        </div>
    </div>
</form>
<?php $this->appendScript('role.js') ?>