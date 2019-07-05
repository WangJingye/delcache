<h3>角色列表</h3>
<hr>
<div class="btn-box clearfix">
    <a href="<?= $this->createUrl('system/role/editRole') ?>">
        <div class="btn btn-success pull-right"><i class="glyphicon glyphicon-plus"></i> 创建</div>
    </a>
</div>
<form class="form-inline search-form" action="<?= $this->createUrl('system/role/index') ?>" method="get">
    <div class="form-group">
        <label>角色状态</label>
        <select class="form-control mx-sm-3" name="status">
            <option value="">请选择</option>
            <option value="0" <?= $this->params['status'] == '0' ? 'selected' : '' ?>>禁用</option>
            <option value="1" <?= $this->params['status'] == '1' ? 'selected' : '' ?>>可用</option>
        </select>
    </div>
    <?php $searchList = ['id' => 'ID', 'name' => '角色名称']; ?>
    <div class="form-group search-group">
        <label>查询条件</label>
        <select class="form-control  mx-sm-3 search-type" name="search_type">
            <option value="">请选择</option>
            <?php foreach ($searchList as $k => $v): ?>
                <option value="<?= $k ?>" <?= $this->params['search_type'] == $k ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" class="form-control search-value" name="search_value" placeholder="关键词"
               value="<?= $this->params['search_value'] ?>">
        <div class="btn btn-primary search-btn"><i class="glyphicon glyphicon-search"></i> 搜索</div>
    </div>
</form>
<table class="table table-bordered list-table">
    <thead>
    <tr>
        <th>ID</th>
        <th>角色名称</th>
        <th>角色描述</th>
        <th>角色状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->list as $v): ?>
        <tr>
            <td><?= $v['id'] ?></td>
            <td><?= $v['name'] ?></td>
            <td><?= $v['desc'] ?></td>
            <td><?= $v['status'] == '1' ? '可用' : '禁用' ?></td>
            <td><?= date('Y-m-d H:i:s', $v['create_time']) ?></td>
            <td>
                <a href="<?= $this->createUrl('system/role/editRole', ['id' => $v['id']]) ?>">
                    <div class="btn btn-outline-primary btn-sm"><i class="glyphicon glyphicon-pencil"></i> 编辑</div>
                </a>
                <a href="<?= $this->createUrl('system/role/setRoleMenu', ['id' => $v['id']]) ?>">
                    <div class="btn btn-outline-primary btn-sm"><i class="glyphicon glyphicon-align-justify"></i> 设置角色权限</div>
                </a>
                <a href="<?= $this->createUrl('system/role/setRoleAdmin', ['id' => $v['id']]) ?>">
                    <div class="btn btn-outline-primary btn-sm"><i class="glyphicon glyphicon-user"></i> 设置角色用户</div>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?= $this->pagination ?>
