<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0, initial-scale=1">
    <title>后台管理系统</title>
    <?php foreach ($this->cssList as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
</head>
<body>

<header class="navbar navbar-expand-lg navbar-dark bg-primary bd-navbar">
    <div class="col-3 col-md-3 col-xl-2">
        <ul class="navbar-nav">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-toggle="dropdown">
                    <img class="rounded-circle" src="<?= $this->user['avatar'] ?>"
                         style="width:30px;height:30px">
                    <span><?= $this->user['realname'] ?></span>
                </a>
                <div class="dropdown-menu" style="position: absolute">
                    <a class="dropdown-item" href="<?= $this->createUrl('system/public/logout') ?>">登出</a>
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
            <?php foreach ($this->menus['topList'] as $v): ?>
                <a class="nav-item nav-link <?= $v['id'] == $this->menus['active']['topId'] ? 'active' : '' ?>"
                   href="<?= $v['url'] != '' ? $this->createUrl($v['url']) : 'javascript:void(0)' ?>">
                    <span><i class="<?= $v['icon'] ?>"></i> <?= $v['name'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</header>
<div class="row flex-xl-nowrap" style="margin:0">
    <div class="col-12 col-md-3 col-xl-2 bd-sidebar" style="padding: 0">
        <ul class="list-group list-group-flush bd-links">
            <?php foreach ($this->menus['leftList'][$this->menus['active']['topId']] as $v): ?>
                <li class="list-group-item main-item <?= $v['id'] == $this->menus['active']['leftId'] ? 'active' : '' ?>">
                    <div><i class="<?= $v['icon'] ?>"></i> <?= $v['name'] ?></div>
                </li>
                <?php if (isset($this->menus['childList'][$v['id']]) && count($this->menus['childList'][$v['id']])): ?>
                    <li class="list-group-item sub-item collapse <?= $v['id'] == $this->menus['active']['leftId'] ? 'show' : '' ?>"
                        style="border-top: 0">
                        <ul class="list-sub-item">
                            <?php foreach ($this->menus['childList'][$v['id']] as $child): ?>
                                <li class="list-group-item <?= $this->menus['active']['childId'] == $child['id'] ? 'active' : '' ?>"
                                    data-url="<?= $this->createUrl($child['url']) ?>"><?= $child['name'] ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-12 col-md-9 col-xl-10 bd-content" style="padding: 0">
        <nav>
            <ol class="breadcrumb" style="border-radius: 0;">
                <li class="breadcrumb-item"><a href="/"><i class="glyphicon glyphicon-home"></i> 主页</a></li>
                <li class="breadcrumb-item"><a
                            href="<?= $this->createUrl($this->menus['menuList'][$this->menus['active']['topId']]['url']) ?>"><?= $this->menus['menuList'][$this->menus['active']['topId']]['name'] ?></a>
                </li>
                <li class="breadcrumb-item"><a
                            href="<?= $this->createUrl($this->menus['menuList'][$this->menus['active']['leftId']]['url']) ?>"><?= $this->menus['menuList'][$this->menus['active']['leftId']]['name'] ?></a>
                </li>
                <?php if (isset($this->menus['active']['endId'])): ?>
                    <li class="breadcrumb-item"><a
                                href="<?= $this->createUrl($this->menus['menuList'][$this->menus['active']['childId']]['url']) ?>"><?= $this->menus['menuList'][$this->menus['active']['childId']]['name'] ?></a>
                    </li>
                    <li class="breadcrumb-item active"><?= $this->menus['menuList'][$this->menus['active']['endId']]['name'] ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item active"><?= $this->menus['menuList'][$this->menus['active']['childId']]['name'] ?></li>
                <?php endif; ?>
            </ol>
        </nav>
        <div class="container">
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