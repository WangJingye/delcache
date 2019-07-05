<form id="save-role-menu-form" action="<?= $this->createUrl('system/role/setRoleMenu') ?>" method="post">
    <input type="hidden" name="id" value="<?= isset($this->model['id']) ? $this->model['id'] : '' ?>">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">角色名称</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" readonly
                   value="<?= isset($this->model['name']) ? $this->model['name'] : '' ?>" placeholder="请输入角色名称">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">所选权限</label>
        <div class="col-sm-10">
            <ul id="menuTree" class="ztree"></ul>
            <input type="hidden" name="menu_ids">
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-2 col-sm-10">
            <input class="btn btn-primary" type="submit" value="保存"/>
        </div>
    </div>
</form>
<script>
    var menuList =<?=json_encode($this->menuList);?>;
</script>
<?php $this->appendScript('admin/role.js') ?>