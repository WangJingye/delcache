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
});

function saveForm() {
    var form = $('.install-form');
    toastr.loading('show');
    $.post(form.attr('action'), form.serialize(), function (res) {
        toastr.loading('hide');
        if (res.code == 0) {
            toastr.success(res.message, function () {
                location.href='/';
            },1000);
        } else {
            toastr.error(res.message);
        }
    }, 'json');
}