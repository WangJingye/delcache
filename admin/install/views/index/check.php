<?php $symbolClass = [
    0 => '<span style="color:#e95d4e"><i class="glyphicon glyphicon-remove"></i></span> ',
    1 => '<span style="color:#1dccaa"><i class="glyphicon glyphicon-ok"></i></span> '
];
?>
<table class="table install-check-table">
    <tbody>
    <tr class="install-table-header">
        <td width="25%">环境检测</td>
        <td width="25%">推荐配置</td>
        <td width="25%">当前状态</td>
        <td width="25%">最低要求</td>
    </tr>
    <?php foreach ($this->data['system'] as $key => $v): ?>
        <tr>
            <td><?= $v['name'] ?></td>
            <td><?= $v['require'] ?></td>
            <td><?= $symbolClass[$v['is_ok']] ?><?= $v['value'] ?></td>
            <td><?= $v['min'] ?></td>
        </tr>
    <?php endforeach; ?>
    <!-- 模块检测 -->
    <tr class="install-table-header">
        <td colspan="4">
            模块检测
        </td>
    </tr>
    <?php foreach ($this->data['module'] as $v): ?>
        <tr>
            <td><?= $v['name'] ?></td>
            <td><?= $v['require'] ?></td>
            <td><?= $symbolClass[$v['is_ok']] ?><?= $v['value'] ?></td>
            <td><?= $v['min'] ?></td>
        </tr>
    <?php endforeach; ?>
    <!-- 大小限制检测 -->
    <tr class="install-table-header">
        <td colspan="4">
            大小限制检测
        </td>
    </tr>
    <?php foreach ($this->data['size'] as $v): ?>
        <tr>
            <td><?= $v['name'] ?></td>
            <td><?= $v['require'] ?></td>
            <td><?= $symbolClass[$v['is_ok']] ?><?= $v['value'] ?></td>
            <td><?= $v['min'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="bottom text-center">
    <a href="<?= $this->createUrl('install/index/check') ?>" class="btn btn-primary">重新检测</a>
    <a href="<?= $this->createUrl('install/index/setting') ?>" class="btn btn-primary">下一步</a>
</div>
