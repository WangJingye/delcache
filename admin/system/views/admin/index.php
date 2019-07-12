<h3>账号列表</h3>
<hr>
<div class="btn-box clearfix">
    <a href="<?= $this->createUrl('system/admin/editAdmin') ?>">
        <div class="btn btn-success pull-right"><i class="glyphicon glyphicon-plus"></i> 创建</div>
    </a>
</div>
<form class="search-form" action="<?= $this->createUrl('system/admin/index') ?>" method="get">
    <div class="form-content">
        <span class="col-form-label search-label">用户状态</span>
        <select class="form-control search-input" name="status">
            <option value="">请选择</option>
            <option value="0" <?= $this->params['status'] == '0' ? 'selected' : '' ?>>禁用</option>
            <option value="1" <?= $this->params['status'] == '1' ? 'selected' : '' ?>>可用</option>
        </select>
    </div>
    <?php $searchList = ['user_id' => '用户ID', 'username' => '用户名', 'realname' => '真实姓名']; ?>
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
            <th>用户ID</th>
            <th>用户名称</th>
            <th>真实名称</th>
            <th>邮箱</th>
            <th>手机号</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->list as $v): ?>
            <tr>
                <td><?= $v['admin_id'] ?></td>
                <td><?= $v['username'] ?></td>
                <td><?= $v['realname'] ?></td>
                <td><?= $v['email'] ?></td>
                <td><?= $v['mobile'] ?></td>
                <td><?= $v['status'] == '1' ? '可用' : '禁用' ?></td>
                <td>
                    <a href="<?= $this->createUrl('system/admin/editAdmin', ['admin_id' => $v['admin_id']]) ?>">
                        <div class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-pencil"></i> 编辑</div>
                    </a>
                    <?php if($v['status']==1):?>
                        <div class="btn btn-danger btn-sm set-status-btn" data-id="<?= $v['admin_id'] ?>" data-status="0"><i class="glyphicon glyphicon-ban-circle"></i> 禁用</div>
                    <?php else:?>
                        <div class="btn btn-success btn-sm set-status-btn" data-id="<?= $v['admin_id'] ?>" data-status="1"><i class="glyphicon glyphicon-ok-circle"></i> 启用</div>
                    <?php endif;?>
                </td>
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
<?php $this->appendScript('admin/admin.js')?>