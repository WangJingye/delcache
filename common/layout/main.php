<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="/static/plugin/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/static/plugin/bootstrap/css/fonts.css">
    <link rel="stylesheet" href="/static/css/main.css">
    <link rel="stylesheet" href="/static/css/select2.css">
    <link rel="stylesheet" href="/static/css/ztree.css">
    <script src="/static/js/jquery.js"></script>
    <script src="/static/plugin/bootstrap/js/bootstrap.js"></script>
    <script src="/static/js/jquery.validate.js"></script>
    <script src="/static/js/select2.min.js"></script>
    <script src="/static/js/toastr.js"></script>
    <script src="/static/js/ztree.core.js"></script>
    <script src="/static/js/ztree.excheck.js"></script>
</head>
<body>
<header class="navbar navbar-expand navbar-dark bg-primary bd-navbar">
    <a class="navbar-brand" href="/">
        <img src="https://v4.bootcss.com/assets/brand/bootstrap-solid.svg" width="30" height="30"
             class="d-inline-block align-top" alt="">
        Bootstrap
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav">
            <?php foreach ($this->menus['topList'] as $v): ?>
                <li class="nav-item <?= $v['id'] == $this->menus['active']['topId'] ? 'active' : '' ?>">
                    <a class="nav-link"
                       href="<?= $v['url'] != '' ? $this->createUrl($v['url']) : 'javascript:void(0)' ?>"><i
                                class="<?= $v['icon'] ?>"></i> <?= $v['name'] ?></span></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <ul class="navbar-nav">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="javascript:void(0)" role="button" data-toggle="dropdown">
                <img class="border border-info rounded-circle" src="<?= $this->user['avatar'] ?>" style="width:30px;height:30px">
                <span><?= $this->user['realname'] ?></span>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?=$this->createUrl('system/public/logout')?>">登出</a>
            </div>
        </li>
    </ul>
</header>
<div class="row flex-xl-nowrap" style="margin:0">
    <div class="col-12 col-md-3 col-xl-2 bd-sidebar" style="padding: 0">
        <ul class="list-group list-group-flush bd-links">
            <?php foreach ($this->menus['leftList'][$this->menus['active']['topId']] as $v): ?>
                <li class="list-group-item main-item <?= $v['id'] == $this->menus['active']['leftId'] ? 'active' : '' ?>">
                    <div><i class="<?= $v['icon'] ?>"></i> <?= $v['name'] ?></div>
                </li>
                <?php if (isset($this->menus['childList'][$v['id']]) && count($this->menus['childList'][$v['id']])): ?>
                    <li class="list-group-item sub-item collapse <?= $v['id'] == $this->menus['active']['leftId'] ? 'show' : '' ?>">
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
<script src="/static/js/admin/main.js"></script>
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