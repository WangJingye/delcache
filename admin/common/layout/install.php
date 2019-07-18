<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0, initial-scale=1, user-scalable=0">
    <title>后台管理系统</title>
    <?php foreach ($this->cssList as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
</head>
<body style="background-color: #e9ecef">
<div class="install-box">
    <div class="header bg-dark">
        <div class="header-title">
            <span class="header-title-text">安装向导</span>
            <span class="header-title-version"><?= APP_VERSION ?></span>
        </div>
    </div>
    <div class="install-content">
        <?php include $view ?>
    </div>
</div>
</body>
<?php foreach ($this->scriptList as $script): ?>
    <script src="<?= $script ?>"></script>
<?php endforeach; ?>
</html>
