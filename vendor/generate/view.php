<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0, initial-scale=1, user-scalable=0">
    <title>Generate</title>
    <link rel="stylesheet" href="/static/plugin/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/static/plugin/bootstrap/css/fonts.css">
</head>
<body>
<form style="width: 80%;margin: auto" method="post">
    <input type="hidden" name="type" value="save">
    <div class="form-group">
        <label for="app">项目名称</label>
        <input type="text" class="form-control" name="app" value="<?= isset($data['app']) ? $data['app'] : 'admin' ?>">
    </div>
    <div class="form-group">
        <label for="template">主题</label>
        <select name="template" class="form-control">
            <option value="web" <?= isset($data['template']) && $data['template'] == 'web' ? 'selected' : '' ?>>web
            </option>
            <option value="api" <?= isset($data['template']) && $data['template'] == 'api' ? 'selected' : '' ?>>api
            </option>
        </select>
    </div>
    <div class="form-group">
        <label for="module">模块名称</label>
        <input type="text" class="form-control" name="module"
               value="<?= isset($data['module']) ? $data['module'] : '' ?>">
    </div>
    <div class="form-group">
        <label for="name">表名</label>
        <input type="text" class="form-control" name="name"
               value="<?= isset($data['name']) ? $data['name'] : '' ?>">
    </div>
    <div class="form-group row">
        <label class="col-sm-12 col-form-label" for="table">数据表</label>
        <div class="col-md-8"><input type="text" class="form-control" name="table"
                                     value="<?= isset($data['table']) ? $data['table'] : '' ?>"></div>
        <div class="col-md-4">
            <div class="btn btn-danger show-table">填充字段数据</div>
        </div>
    </div>
    <?php if (!empty($data) && !isset($data['fcomment'])): ?>
        <div class="form-group">
            <div style="color:red">请填充字段数据</div>
        </div>
    <?php endif; ?>
    <?php if (isset($data['fcomment'])): ?>
        <div class="form-group" id="show-table-form-group">
            <div>选项输入格式variable,key:value，数据库格式table:key:value:variable:where</div>
            <table class="table table-bordered">
                <tr>
                    <td>字段</td>
                    <td>标题</td>
                    <td>界面类型</td>
                    <td>选项</td>
                    <td>列表搜索</td>
                    <td>查询条件搜索</td>
                    <td>列表显示</td>
                    <td>编辑显示</td>
                    <td>是否必填</td>
                </tr>
                <?php foreach ($data['fcomment'] as $field => $label): ?>
                    <tr>
                        <td class="fname"><?= $field ?></td>
                        <td class="fcomment"><input type="text" name="fcomment[<?= $field ?>]" class="form-control"
                                                    value="<?= $label ?>"></td>
                        <td class="ftype">
                            <?php $ftypeList = [
                                'input' => 'input', 'radio' => 'radio', 'checkbox' => 'checkbox',
                                'select' => 'select', 'textarea' => 'textarea', 'date' => 'date',
                                'date-normal' => 'date(文本)', 'datetime' => 'datetime', 'datetime-normal' => 'datetime(文本)'
                                , 'image',]; ?>
                            <select class="form-control" name="ftype[<?= $field ?>]">
                                <?php foreach ($ftypeList as $ftype): ?>
                                    <option value="<?= $ftype ?>" <?= $data['ftype'][$field] == $ftype ? 'selected' : '' ?>><?= $ftype ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="fchoice">
                            <label><input type="radio" name="fchoice[<?= $field ?>]"
                                          value="1" <?= isset($data['fchoice'][$field]) && $data['fchoice'][$field] == '1' ? 'checked' : '' ?>>
                                来源输入值</label>
                            <label><input type="radio" name="fchoice[<?= $field ?>]"
                                          value="2" <?= isset($data['fchoice'][$field]) && $data['fchoice'][$field] == '2' ? 'checked' : '' ?>>
                                来源数据库</label>
                            <input class="form-control" name="fchoicelist[<?= $field ?>]" type="text"
                                   value="<?= isset($data['fchoicelist'][$field]) ? $data['fchoicelist'][$field] : '' ?>"
                                   placeholder="">
                        </td>
                        <td class="fpagesearch1 fclick">
                            <input type="checkbox" name="fpagesearch1[<?= $field ?>]"
                                   value="1" <?= isset($data['fpagesearch1'][$field]) && $data['fpagesearch1'][$field] == '1' ? 'checked' : '' ?>>
                        </td>
                        <td class="fpagesearch2 fclick">
                            <input type="checkbox" name="fpagesearch2[<?= $field ?>]"
                                   value="1" <?= isset($data['fpagesearch2'][$field]) && $data['fpagesearch2'][$field] == '1' ? 'checked' : '' ?>>
                        </td>
                        <td class="fpageshow fclick">
                            <input type="checkbox" name="fpageshow[<?= $field ?>]"
                                   value="1" <?= isset($data['fpageshow'][$field]) && $data['fpageshow'][$field] == '1' ? 'checked' : '' ?>>
                        </td>
                        <td class="feditshow fclick">
                            <input type="checkbox" name="feditshow[<?= $field ?>]"
                                   value="1" <?= isset($data['feditshow'][$field]) && $data['feditshow'][$field] == '1' ? 'checked' : '' ?>>
                        </td>
                        <td class="frequire fclick">
                            <input type="checkbox" name="frequire[<?= $field ?>]"
                                   value="1" <?= isset($data['frequire'][$field]) && $data['frequire'][$field] == '1' ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">保存</button>
</form>
<div style="display:none;">
    <table class="table table-bordered">
        <thead id="table-header">
        <tr>
            <td>字段</td>
            <td>标题</td>
            <td>界面类型</td>
            <td>选项</td>
            <td>列表搜索</td>
            <td>查询条件搜索</td>
            <td>列表显示</td>
            <td>编辑显示</td>
            <td>是否必填</td>
        </tr>
        </thead>
        <tbody id="table-body">
        <tr>
            <td class="fname"></td>
            <td class="fcomment"><input type="text" class="form-control" value=""></td>
            <td class="ftype">
                <select class="form-control">
                    <option value="input">input</option>
                    <option value="radio">radio</option>
                    <option value="checkbox">checkbox</option>
                    <option value="select">select</option>
                    <option value="textarea">textarea</option>
                    <option value="date">date</option>
                    <option value="date-normal">date(文本)</option>
                    <option value="datetime">datetime</option>
                    <option value="datetime-normal">datetime(文本)</option>
                    <option value="image">image</option>
                </select>
            </td>
            <td class="fchoice">
                <label><input type="radio" value="1"> 来源输入值</label>
                <label><input type="radio" value="2"> 来源数据库</label>
                <input class="form-control" type="text" value="" placeholder="">
            </td>
            <td class="fpagesearch1 fclick">
                <input type="checkbox" value="1">
            </td>
            <td class="fpagesearch2 fclick">
                <input type="checkbox" value="1">
            </td>
            <td class="fpageshow fclick">
                <input type="checkbox" value="1">
            </td>
            <td class="feditshow fclick">
                <input type="checkbox" value="1">
            </td>
            <td class="frequire fclick">
                <input type="checkbox" value="1">
            </td>
        </tr>
        </tbody>

    </table>
</div>
</body>
<script src="/static/js/jquery.js"></script>
<script src="/static/plugin/bootstrap/js/popper.min.js"></script>
<script src="/static/plugin/bootstrap/js/bootstrap.js"></script>
<script src="/static/js/jquery.validate.js"></script>
<script src="/static/js/toastr.js"></script>
<script>
    $(function () {
        $('.show-table').click(function () {
            var $this = $(this);
            $.post('/generate', {type: 'show-table', table: $('input[name=table]').val()}, function (res) {
                if (res.code == '0') {
                    var html = ' <div>选项输入格式variable,key:value，数据库格式table:key:value:variable:where</div><table class="table table-bordered">';
                    html += $('#table-header').clone().html();
                    var data = res.data;
                    for (var i in data) {
                        var tr = $('#table-body').clone();
                        tr.find('.fname').html(data[i]);
                        tr.find('.ftype select').attr('name', 'ftype[' + data[i] + ']');
                        tr.find('.fchoice input[type=radio]').attr('name', 'fchoice[' + data[i] + ']');
                        tr.find('.fchoice input[type=text]').attr('name', 'fchoicelist[' + data[i] + ']');
                        tr.find('.fcomment input').attr('name', 'fcomment[' + data[i] + ']');
                        tr.find('.fpagesearch1 input[type=checkbox]').attr('name', 'fpagesearch1[' + data[i] + ']');
                        tr.find('.fpagesearch2 input[type=checkbox]').attr('name', 'fpagesearch2[' + data[i] + ']');
                        tr.find('.fpageshow input[type=checkbox]').attr('name', 'fpageshow[' + data[i] + ']');
                        tr.find('.feditshow input[type=checkbox]').attr('name', 'feditshow[' + data[i] + ']');
                        tr.find('.frequire input[type=checkbox]').attr('name', 'frequire[' + data[i] + ']');
                        html += tr.html();
                    }
                    if ($('#show-table-form-group').get(0)) {
                        $('#show-table-form-group').html(html);
                    } else {
                        $this.parents('.form-group').after('<div class="form-group" id="show-table-form-group">' + html + '</div>');
                    }
                } else {
                    alert(res.message);
                }
            }, 'json');
        });
        $('body').on('click', '.fclick', function (e) {
            if (e.target.nodeName != 'INPUT') {
                var checkbox = $(this).find('input[type=checkbox]');
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
        });
    });
</script>
</html>