<form class="form-box col-12 col-sm-8 col-md-6" id="save-form" action="<?= \App::$urlManager->createUrl('system/admin/edit-admin') ?>" method="post">
    <input type="hidden" name="admin_id" value="<?= isset($this->model['admin_id']) ? $this->model['admin_id'] : '' ?>">
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">用户名</label>
        <div class="col-sm-8">
            <input type="text" name="username" class="form-control"
                   value="<?= isset($this->model['username']) ? $this->model['username'] : '' ?>" placeholder="请输入用户名">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">真实姓名</label>
        <div class="col-sm-8">
            <input type="text" name="realname" class="form-control"
                   value="<?= isset($this->model['realname']) ? $this->model['realname'] : '' ?>" placeholder="请输入真实姓名">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">邮箱</label>
        <div class="col-sm-8">
            <input type="text" name="email" class="form-control"
                   value="<?= isset($this->model['email']) ? $this->model['email'] : '' ?>" placeholder="请输入邮箱">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">手机号</label>
        <div class="col-sm-8">
            <input type="text" name="mobile" class="form-control"
                   value="<?= isset($this->model['mobile']) ? $this->model['mobile'] : '' ?>" placeholder="请输入手机号">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-4 text-nowrap col-form-label form-label">头像</label>
        <div class="col-sm-8">
            <?= \admin\extend\image\ImageInput::instance($this->model['avatar'], 'avatar')->show(); ?>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-4 col-sm-8">
            <input class="btn btn-primary" type="submit" value="保存"/>
        </div>
    </div>
</form>
<?php $this->appendScript('admin.js') ?>