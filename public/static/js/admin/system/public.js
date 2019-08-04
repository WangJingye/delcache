$(function () {
    $('#login-form').validate({
        rules: {
            username: {
                required: true
            },
            password: {
                required: true
            },
            captcha: {
                required: true
            }
        },
        messages: {
            username: {
                required: '请输入用户名'
            },
            password: {
                required: '请输入密码'
            },
            captcha: {
                required: '请输入验证码'
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
            submitForm();
            return false;
        }
    });

    $('.captcha-box').on('click','img',function () {
        $(this).attr('src', $(this).data('src') + '?' + new Date().getTime());
    });
});

function submitForm() {
    var form = $('#login-form');
    var data = form.serialize();
    toastr.loading('show');
    $.post(form.attr('action'), data, function (res) {
        toastr.loading('hide');
        if (res.code == 0) {
            toastr.success(res.message);
            setTimeout(function () {
                location.href = '/';
            }, 2000)
        } else {
            $('.captcha-box').find('img').click();
            toastr.error(res.message);
        }
    }, 'json');
}