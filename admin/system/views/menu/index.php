<h3>菜单列表</h3>
<hr>
<div class="btn-box clearfix">
    <a href="<?= $this->createUrl('system/menu/editMenu') ?>">
        <div class="btn btn-success pull-right"><i class="glyphicon glyphicon-plus"></i> 创建</div>
    </a>
</div>
<form class="form-inline search-form" action="<?= $this->createUrl('system/menu/index') ?>" method="get">
    <div class="form-group">
        <label>菜单状态</label>
        <select class="form-control mx-sm-3" name="status">
            <option value="">请选择</option>
            <option value="0" <?= $this->params['status'] == '0' ? 'selected' : '' ?>>禁用</option>
            <option value="1" <?= $this->params['status'] == '1' ? 'selected' : '' ?>>可用</option>
        </select>
    </div>
    <?php $searchList = ['name' => '菜单名称', 'url' => 'url']; ?>
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
        <th>菜单名称</th>
        <th>url</th>
        <th>菜单状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($this->list as $v): ?>
        <tr>
            <td><?= $v['id'] ?></td>
            <td><?= $v['name'] ?></td>
            <td><?= $v['url'] ?></td>
            <td><?= $v['status'] == '1' ? '可用' : '禁用' ?></td>
            <td><?= date('Y-m-d H:i:s', $v['create_time']) ?></td>
            <td>
                <a href="<?= $this->createUrl('system/menu/editMenu', ['id' => $v['id']]) ?>">
                    <div class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-pencil"></i> 编辑</div>
                </a></td>
        </tr>
    <?php endforeach; ?>
</table>
<?= $this->pagination ?>
