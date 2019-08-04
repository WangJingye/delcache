<form class="install-form" action="<?= \App::$urlManager->createUrl('install/index/complete') ?>" method="post">
    <div class="form-group row">
        <label class="col-sm-2 col-form-label install-table-header">数据库信息</label>
        <div class="col-sm-10">

        </div>
    </div>
    <div class="form-group row">
        <label for="host" class="col-sm-2 col-form-label">数据库服务器</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="host" id="host" placeholder="127.0.0.1">
        </div>
    </div>
    <div class="form-group row">
        <label for="port" class="col-sm-2 col-form-label">数据库端口</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="port" id="port" placeholder="3306">
        </div>
    </div>
    <div class="form-group row">
        <label for="username" class="col-sm-2 col-form-label">数据库用户名</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="username" id="username" placeholder="root">
        </div>
    </div>
    <div class="form-group row">
        <label for="password" class="col-sm-2 col-form-label">数据库密码</label>
        <div class="col-sm-10">
            <input type="password" name="password" class="form-control" id="password">
        </div>
    </div>
    <div class="form-group row">
        <label for="dbname" class="col-sm-2 col-form-label">数据库名</label>
        <div class="col-sm-10">
            <input type="text" name="dbname" class="form-control" id="dbname">
        </div>
    </div>
    <div class="form-group row">
        <label for="prefix" class="col-sm-2 col-form-label">数据库表前缀</label>
        <div class="col-sm-10">
            <input type="text" name="prefix" class="form-control" id="prefix" placeholder="tbl_">
        </div>
    </div>
    <div class="form-group row">
        <label for="charset" class="col-sm-2 col-form-label">数据库编码</label>
        <div class="col-sm-10">
            <select class="form-control" name="charset" id="charset">
                <option value="utf8mb4">utf8mb4</option>
                <option value="utf8">utf8</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label install-table-header">管理员信息</label>
        <div class="col-sm-10">

        </div>
    </div>
    <div class="form-group row">
        <label for="login_name" class="col-sm-2 col-form-label">管理员账号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="login_name" name="login_name" placeholder="admin">
        </div>
    </div>
    <div class="form-group row">
        <label for="login_password" class="col-sm-2 col-form-label">登录密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="login_password" id="login_password">
        </div>
    </div>
    <div class="form-group row">
        <label for="rePassword" class="col-sm-2 col-form-label">重复密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="login_re_password" id="rePassword">
        </div>
    </div>
    <div class="form-group row">
        <label for="email" class="col-sm-2 col-form-label">邮箱</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" name="email" id="email">
        </div>
    </div>
    <div class="bottom text-center">
        <a href="<?= \App::$urlManager->createUrl('install/index/check') ?>" class="btn btn-primary">上一步</a>
        <input type="submit" class="btn btn-primary" value="创建数据">
    </div>
</form>
<?php $this->appendScript('install.js')?>