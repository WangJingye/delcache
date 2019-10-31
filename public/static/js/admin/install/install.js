$(function () {
    $('.install-form').validate({
        rules: {
            dbname: {
                required: true
            },
            login_password: {
                required: true
            },
            login_re_password: {
                required: true,
                equalTo: '#login_password'
            },
            email: {
                required: true,
            }
        },
        messages: {
            dbname: {
                required: '数据库名称必填'
            },
            login_password: {
                required: '登录密码必填'
            },
            login_re_password: {
                required: '重复密码必填',
                equalTo: '重复密码与登录密码必须一致'
            },
            email: {
                required: '请输入邮箱',
            }
        },
        submitHandler: function (e) {
            saveForm();
            return false;
        }
    });
});

function saveForm() {
    var form = $('.install-form');
    $.loading('show');
    $.post(form.attr('action'), form.serialize(), function (res) {
        $.loading('hide');
        if (res.code == 200) {
            $.success(res.message, function () {
                location.href='/';
            },1000);
        } else {
            $.error(res.message);
        }
    }, 'json');
}