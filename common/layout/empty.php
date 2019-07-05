<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<link rel="stylesheet" href="/static/plugin/bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="/static/plugin/bootstrap/css/fonts.css">
<link rel="stylesheet" href="/static/css/main.css">
<body>
<?php include $view ?>
</body>
<script src="/static/js/jquery.js"></script>
<script src="/static/plugin/bootstrap/js/bootstrap.js"></script>
<script src="/static/js/jquery.validate.js"></script>
<script src="/static/js/toastr.js"></script>
<?php foreach ($this->scriptList as $script): ?>
    <script src="<?= $script ?>"></script>
<?php endforeach; ?>
</html>
