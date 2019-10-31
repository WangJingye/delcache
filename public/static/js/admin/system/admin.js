$(function () {
    //编辑用户
    $('#save-form').validate({
        rules: {
            username: {
                required: true
            },
            realname: {
                required: true
            },
            email: {
                required: true
            }
        },
        messages: {
            username: {
                required: '请输入用户名称'
            },
            realname: {
                required: '请输入真实姓名'
            },
            email: {
                required: '请输入邮箱'
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
    //修改个人信息
    $('#change-user-info-form').validate({
        rules: {
            realname: {
                required: true
            },
            email: {
                required: true
            }
        },
        messages: {
            realname: {
                required: '请输入真实姓名'
            },
            email: {
                required: '请输入邮箱'
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
            changeUserForm();
            return false;
        }
    });
    //修改密码
    $('#change-password-form').validate({
        rules: {
            password: {
                required: true
            },
            newPassword: {
                minlength: 6,
                required: true
            },
            rePassword: {
                minlength: 6,
                required: true,
                equalTo: "#newPassword"
            }
        },
        messages: {
            password: {
                required: '请输入当前登录密码'
            },
            newPassword: {
                minlength: '新登录密码不能少于6位',
                required: '请输入新登录密码'
            },
            rePassword: {
                minlength: '确认密码不能少于6位',
                required: '请输入确认新登录密码',
                equalTo: '确认密码和新登录密码不一致'
            }
        },
        submitHandler: function (e) {
            changePasswordForm();
            return false;
        }
    });

    $('.set-status-btn').click(function () {
        let args = {
            id: $(this).data('id'),
            status: $(this).data('status')
        };
        $.loading('show');
        $.post('/system/admin/set-status', args, function (res) {
            $.loading('hide');
            if (res.code == 200) {
                $.success(res.message, function () {
                    location.reload();
                });
            } else {
                $.error(res.message);
            }
        }, 'json');
    });
    $('.reset-password-btn').click(function () {
        if (!confirm('是否重置密码为' + $(this).data('default') + '?')) {
            return false;
        }
        $.loading('show');
        $.post('/system/admin/reset-password', {admin_id: $(this).data('id')}, function (res) {
            $.loading('hide');
            if (res.code == 200) {
                $.success(res.message);
            } else {
                $.error(res.message);
            }
        }, 'json');
    });

    if (window.location.hash && $(window.location.hash).get(0)) {
        $('.profile-nav a[href="' + window.location.hash + '"]').tab('show');
    }
});

function saveForm() {
    var form = $('#save-form');
    var formData = new FormData();
    var data = form.serializeArray();

    for (var i in data) {
        formData.append(data[i].name, data[i].value);
    }
    if ($('input[type=file]').val().length) {
        formData.append('file', $('input[type=file]')[0].files[0]);
    }
    $.loading('show');
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (res) {
            if (res.code == 200) {
                $.success(res.message);
            } else {
                $.error(res.message);
            }
        },
        complete: function () {
            $.loading('hide');
        }
    });
}

function changeUserForm() {
    let form = $('#change-user-info-form');
    let formData = new FormData();
    let data = form.serializeArray();
    for (let i in data) {
        formData.append(data[i].name, data[i].value);
    }
    if ($('input[type=file]').val().length) {
        formData.append('file', $('input[type=file]')[0].files[0]);
    }
    $.loading('show');
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (res) {
            if (res.code == 200) {
                $.success(res.message, function () {
                    location.reload();
                }, 2000);
            } else {
                $.error(res.message);
            }
        },
        complete: function () {
            $.loading('hide');
        }
    });
}

function changePasswordForm() {
    let form = $('#change-password-form');
    $.post(form.attr('action'), form.serialize(), function (res) {
        $.loading('hide');
        if (res.code == 200) {
            $.success(res.message, function () {
                location.reload();
            }, 2000);
        } else {
            $.error(res.message);
        }
    }, 'json');
}