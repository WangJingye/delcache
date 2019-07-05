<form id="save-role-admin-form" action="<?= $this->createUrl('system/role/setRoleAdmin') ?>" method="post">
    <input type="hidden" name="id" value="<?= isset($this->model['id']) ? $this->model['id'] : '' ?>">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">角色名称</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" readonly
                   value="<?= isset($this->model['name']) ? $this->model['name'] : '' ?>" placeholder="请输入角色名称">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">所选用户</label>
        <div class="col-sm-10">
            <select name="admin_id[]" class="form-control select2" multiple>
                <?php foreach ($this->adminList as $v): ?>
                    <option value="<?= $v['admin_id'] ?>" <?= in_array($v['admin_id'], $this->adminIdList) ? 'selected' : '' ?>><?= $v['realname'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-2 col-sm-10">
            <input class="btn btn-primary" type="submit" value="保存"/>
        </div>
    </div>
</form>
<?php $this->appendScript('admin/role.js') ?>