$(function () {
    $('#save-form').validate({
        rules: {
            name: {
                required: true
            }
        },
        messages: {
            name: {
                required: '请输入角色名称'
            }
        },
        errorClass: "invalid-feedback",
        errorElement: "div",
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            $(element).addClass('is-valid');
        },
        submitHandler: function (e) {
            saveForm();
            return false;
        }
    });
    var setting = {
        check: {
            enable: true,
            chkboxType: {'Y': 'p' + 's', 'N': 'p' + 's'}
        },
        view: {
            dblClickExpand: false
        },
        data: {
            simpleData: {
                enable: true
            }
        }
    };
    $('#save-role-menu-form').validate({
        submitHandler: function (e) {
            saveRoleMenuForm();
            return false;
        }
    });
    $('#save-role-admin-form').validate({
        submitHandler: function (e) {
            saveRoleAdminForm();
            return false;
        }
    });
    if (typeof menuList != 'undefined') {
        $.fn.zTree.init($('#menuTree'), setting, menuList);
    }
});
var nodesArr = [];

function getCheckTreeNodes(nodes) {
    $.each(nodes, function (i, obj) {
        if (obj.checked) {
            nodesArr.push(obj.id);
            if (obj.children) {
                getCheckTreeNodes(obj.children);
            }
        }
    });
}

function saveRoleAdminForm() {
    var form = $('#save-role-admin-form');
    var data = form.serialize();
    $.loading('show');
    $.post(form.attr('action'), data, function (res) {
        $.loading('hide');
        if (res.code == 200) {
            $.success(res.message);
        } else {
            $.error(res.message);
        }
    }, 'json');
}

function saveRoleMenuForm() {
    nodesArr = [];
    var zTree = $.fn.zTree.getZTreeObj('menuTree');
    getCheckTreeNodes(zTree.getNodes());
    var form = $('#save-role-menu-form');
    $('input[name=menu_ids]').val(nodesArr.join(','));
    var data = form.serialize();
    $.loading('show');
    $.post(form.attr('action'), data, function (res) {
        $.loading('hide');
        if (res.code == 200) {
            $.success(res.message);
        } else {
            $.error(res.message);
        }
    }, 'json');
}

function saveForm() {
    var form = $('#save-form');
    var data = form.serialize();
    $.loading('show');
    $.post(form.attr('action'), data, function (res) {
        $.loading('hide');
        if (res.code == 200) {
            $.success(res.message);
        } else {
            $.error(res.message);
        }
    }, 'json');
}