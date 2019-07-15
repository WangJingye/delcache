<form class="form-box col-12 col-sm-8 col-md-6" id="save-form" action="<?= $this->createUrl('system/menu/editMenu') ?>" method="post">
    <input type="hidden" name="id" value="<?= isset($this->model['id']) ? $this->model['id'] : '' ?>">
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">功能类型</label>
        <div class="col-sm-8">
            <?php foreach (['0' => '头部导航', '1' => '左部菜单', '2' => '左部功能', '3' => '列表功能'] as $key => $v): ?>
                <div class="form-check form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="type"
                               value="<?= $key ?>" <?= isset($this->model['type']) && $this->model['type'] == $key ? 'checked' : '' ?>>
                        <?= $v ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">父级功能</label>
        <div class="col-sm-8">
            <select class="form-control select2" name="parent_id">
                <?php foreach ($this->childList as $v): ?>
                    <option value="<?= $v['id'] ?>" <?= isset($this->model['parent_id']) && $this->model['parent_id'] == $v['id'] ? 'selected' : '' ?>><?= $v['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">链接地址</label>
        <div class="col-sm-8">
            <select name="url" class="form-control">
                <option value="">请选择</option>
                <?php foreach ($this->methodList as $v): ?>
                    <option value="<?= $v ?>" <?= isset($this->model['url']) && $this->model['url'] == $v ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">标题</label>
        <div class="col-sm-8">
            <input type="text" name="name" class="form-control"
                   value="<?= isset($this->model['name']) ? $this->model['name'] : '' ?>" placeholder="请输入标题">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">排序</label>
        <div class="col-sm-8">
            <input type="text" name="sort" class="form-control"
                   value="<?= isset($this->model['sort']) ? $this->model['sort'] : '0' ?>" placeholder="请输入排序数字">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">图标样式</label>
        <div class="col-sm-8">
            <input type="text" name="icon" class="form-control"
                   value="<?= isset($this->model['icon']) ? $this->model['icon'] : '' ?>"
                   placeholder="glyphicon glyphicon-bookmark">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">菜单描述</label>
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
<?php $this->appendScript('admin/menu.js') ?>