<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>后台管理系统</title>
    <?php foreach ($this->cssList as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
</head>
<body>
<?php include $view ?>
</body>
<?php foreach ($this->scriptList as $script): ?>
    <script src="<?= $script ?>"></script>
<?php endforeach; ?>
</html>
