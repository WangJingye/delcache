$(function () {
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

    $('.set-status-btn').click(function () {
        let args = {
            id: $(this).data('id'),
            status: $(this).data('status')
        };
        toastr.loading('show');
        $.post('/system/admin/setStatus', args, function (res) {
            toastr.loading('hide');
            if (res.errno == 0) {
                toastr.success(res.message, function () {
                    location.reload();
                });
            } else {
                toastr.error(res.message);
            }
        }, 'json');
    });
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
    toastr.loading('show');
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (res) {
            if (res.errno == 0) {
                toastr.success(res.message);
            } else {
                toastr.error(res.message);
            }
        },
        complete: function () {
            toastr.loading('hide');
        }
    });
}