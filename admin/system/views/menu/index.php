<h3>菜单列表</h3>
<hr>
<div class="btn-box clearfix">
    <a href="<?= $this->createUrl('system/menu/editMenu') ?>">
        <div class="btn btn-success pull-right"><i class="glyphicon glyphicon-plus"></i> 创建</div>
    </a>
</div>
<form class="search-form" action="<?= $this->createUrl('system/menu/index') ?>" method="get">
    <div class="form-content">
        <span class="col-form-label search-label">菜单状态</span>
        <select class="form-control search-input" name="status">
            <option value="">请选择</option>
            <option value="0" <?= $this->params['status'] == '0' ? 'selected' : '' ?>>禁用</option>
            <option value="1" <?= $this->params['status'] == '1' ? 'selected' : '' ?>>可用</option>
        </select>
    </div>
    <?php $searchList = ['name' => '菜单名称', 'url' => 'url']; ?>
    <div class="form-content">
        <span class="col-form-label search-label">查询条件</span>
        <div class="clearfix" style="display: inline-flex;">
            <select class="form-control search-type" name="search_type">
                <option value="">请选择</option>
                <?php foreach ($searchList as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $this->params['search_type'] == $k ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" class="form-control search-value" name="search_value" placeholder="关键词"
                   value="<?= $this->params['search_value'] ?>">
            <div class="btn btn-primary search-btn text-nowrap"><i class="glyphicon glyphicon-search"></i> 搜索</div>
        </div>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-bordered list-table text-nowrap">
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
        <?php if (!count($this->list)): ?>
            <tr>
                <td colspan="12" class="list-table-nodata">暂无相关数据</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->pagination ?>
