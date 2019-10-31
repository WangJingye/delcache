$(function () {
    $('#save-form').validate({
        rules: {
            parent_id: {
                required: true
            },
            name: {
                required: true
            }
        },
        messages: {
            parent_id: {
                required: '请选择父级功能'
            },
            name: {
                required: '请输入标题'
            }
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
        $.loading('show');
        $.post('/system/menu/set-status', args, function (res) {
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

});

function saveForm() {
    var form = $('#save-form');
    var data = form.serialize(),
        type = form.find('[name=type]:checked').val();

    if ($.inArray(type, ['2', '3']) != -1 && !form.find('select[name=url]').val().length) {
        $.error('左部功能/列表功能 必须选择 链接地址');
        return false;
    }
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