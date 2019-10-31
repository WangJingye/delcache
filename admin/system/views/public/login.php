<link rel="stylesheet" href="/static/css/login.css">
<div class="container">
    <div class="login-head"><h3>后台管理系统</h3></div>
    <div class="login-box">
        <div class="login-title">用户登录</div>
        <form id="login-form" action="<?= \App::$urlManager->createUrl('system/public/login') ?>" method="post">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="glyphicon glyphicon-user"></i></div>
                    </div>
                    <input type="text" class="form-control" name="username" placeholder="用户名">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="glyphicon glyphicon-lock"></i></div>
                    </div>
                    <input type="password" class="form-control" name="password" placeholder="密码">
                </div>
            </div>
            <div class="form-group captcha-box">
                <input type="text" class="form-control captcha" name="captcha" placeholder="验证码" maxlength="5">
                <img src="<?= \App::$urlManager->createUrl('system/public/captcha') ?>"
                     data-src="<?= \App::$urlManager->createUrl('system/public/captcha') ?>">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width: 100%">登录</button>
            </div>
        </form>
    </div>
</div>
<?php $this->appendScript('public.js') ?>