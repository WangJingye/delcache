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
<body style="background-color: #f7f7f9;">
<?php
$menuService = new \admin\system\service\MenuService();
$currentMenu = $menuService->getCurrentMenu();
$activeMenuList = $menuService->getActiveMenu();
$topList = $menuService->getTopList();
$leftList = $menuService->getLeftList();
$breadcrumbs = [];
$arr = array_reverse($activeMenuList);
foreach ($arr as $v) {
    $tmp = ['name' => $v['name']];
    $tmp['url'] = $v['url'] != '' ? $v['url'] : '';
    $breadcrumbs[] = $tmp;
}
?>
<header class="navbar navbar-expand-lg navbar-dark bg-primary bd-navbar">
    <div class="col-3 col-md-3 col-xl-2">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-toggle="dropdown">
                    <?php if (\App::$user['avatar']): ?>
                        <img class="rounded-circle" src="<?= \App::$user['avatar'] ?>" style="width:30px;height:30px">
                    <?php endif; ?>
                    <span><?= \App::$user['realname'] ?></span>
                </a>
                <div class="dropdown-menu" style="position: absolute">
                    <a class="dropdown-item" href="<?= \App::$urlManager->createUrl('system/admin/profile') ?>">个人信息</a>
                    <a class="dropdown-item" href="<?= \App::$urlManager->createUrl('system/public/logout') ?>">登出</a>
                </div>
            </li>
        </ul>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#top-menu-list"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="top-menu-list">
        <div class="navbar-nav">
            <?php foreach ($topList as $v): ?>
                <a class="nav-item nav-link <?= isset($activeMenuList[$v['id']]) ? 'active' : '' ?>"
                   href="<?= $v['url'] != '' ? \App::$urlManager->createUrl($v['url']) : 'javascript:void(0)' ?>">
                    <span><i class="<?= $v['icon'] ?>"></i> <?= $v['name'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</header>
<div class="row flex-xl-nowrap" style="margin:0">
    <div class="col-12 col-md-3 col-xl-2 bd-sidebar" style="padding: 0">
        <ul class="list-group list-group-flush bd-links">
            <?php foreach ($leftList as $v): ?>
                <li class="list-group-item main-item <?= isset($activeMenuList[$v['item']['id']]) ? 'active' : '' ?>">
                    <div><i class="<?= $v['item']['icon'] ?>"></i> <?= $v['item']['name'] ?></div>
                </li>
                <?php if (isset($v['list']) && count($v['list'])): ?>
                    <li class="list-group-item sub-item collapse <?= isset($activeMenuList[$v['item']['id']]) ? 'show' : '' ?>"
                        style="border-top: 0">
                        <ul class="list-sub-item">
                            <?php foreach ($v['list'] as $child): ?>
                                <li class="list-group-item <?= isset($activeMenuList[$child['id']]) ? 'active' : '' ?>"
                                    data-url="<?= \App::$urlManager->createUrl($child['url']) ?>"><?= $child['name'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-12 col-md-9 col-xl-10 bd-content" style="padding: 0">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="border-radius: 0;margin-bottom: 0;">
                <li class="breadcrumb-item">
                    <a href="<?= \App::$urlManager->createUrl('/') ?>">
                        <i class="glyphicon glyphicon-home"></i> 主页</a>
                </li>
                <?php foreach ($breadcrumbs as $v): ?>
                    <li class="breadcrumb-item <?= $v['url'] == '' ? 'active' : '' ?>">
                        <a <?= $v['url'] != '' ? 'href="' . \App::$urlManager->createUrl($v['url']) . '"' : ''; ?>><?= $v['name'] ?></a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <div class="bd-container">
            <h3><?= $currentMenu['name'] ?></h3>
            <hr>
            <?php include $view ?>
        </div>
    </div>
</div>
</body>
<?php foreach ($this->scriptList as $script): ?>
    <script src="<?= $script ?>"></script>
<?php endforeach; ?>
<script>
    $(function () {
        $('.main-item').click(function () {
            $('.sub-item').collapse('hide');
            $(this).next('.sub-item').collapse('toggle')
        });
        $('.list-sub-item .list-group-item').click(function () {
            location.href = $(this).data('url');
        });
        $('.search-form').on('click', '.search-btn', function () {
            $(this).parents('form').submit();
        });
        $('#page-size').change(function () {
            var form = $('.search-form');
            if (!form.find('[name=pageSize]').get(0)) {
                form.append('<input type="hidden" name="pageSize" value="' + $(this).val() + '">');
            } else {
                form.find('[name=pageSize]').val($(this).val());
            }
            form.submit();
        });
    });
</script>
</html>