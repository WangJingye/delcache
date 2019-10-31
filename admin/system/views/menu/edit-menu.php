<form class="form-box col-12 col-sm-8 col-md-6" id="save-form"
      action="<?= \App::$urlManager->createUrl('system/menu/edit-menu') ?>" method="post">
    <input type="hidden" name="id" value="<?= isset($this->model['id']) ? $this->model['id'] : '' ?>">
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">父级功能</label>
        <div class="col-sm-8">
            <?= \admin\extend\input\SelectInput::instance(array_column($this->childList, 'name', 'id'), $this->model['parent_id'], 'parent_id', 'select2')->show(); ?>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">链接地址</label>
        <div class="col-sm-8">
            <?= \admin\extend\input\SelectInput::instance($this->methodList, $this->model['url'], 'url', 'select')->show(); ?>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">标题</label>
        <div class="col-sm-8">
            <input type="text" name="name" class="form-control" value="<?= $this->model['name'] ?>" placeholder="请输入标题">
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
                   value="<?= isset($this->model['icon']) ? $this->model['icon'] : 'glyphicon glyphicon-bookmark' ?>"
                   placeholder="glyphicon glyphicon-bookmark">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">菜单描述</label>
        <div class="col-sm-8">
            <textarea name="desc" class="form-control"><?= $this->model['desc'] ?></textarea>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-4 col-sm-8">
            <input class="btn btn-primary" type="submit" value="保存"/>
        </div>
    </div>
</form>
<?php $this->appendScript('menu.js') ?>