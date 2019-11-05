$(function () {
    $('.select2').select2();
    $('form .select2,.select').on('change', function () {
        $(this).valid();
    });
    jQuery.validator.setDefaults({
        errorClass: "invalid-feedback",
        errorElement: "div",
        highlight: function (element, errorClass, validClass) {
            if ($(element).hasClass('select2')) {
                $(element).next('span').find('.select2-selection').removeClass('is-valid').addClass('is-invalid');
            } else {
                $(element).parents('.form-group').find('input,select').removeClass('is-valid').addClass('is-invalid');
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            if ($(element).hasClass('select2')) {
                $(element).next('span').find('.select2-selection').removeClass('is-invalid').addClass('is-valid');
            } else {
                $(element).parents('.form-group').find('input,select').removeClass('is-invalid').addClass('is-valid');
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr('type') == 'radio' || element.attr('type') == 'checkbox') {
                error.insertAfter(element.parents('.form-radio-group'));
            } else if (element.hasClass('select2')) {
                error.insertAfter(element.next('span'));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('.fileinput-box-list').on('change', '.fileinput-input', function () {
        var $this = $(this);
        var box = $this.parents('.fileinput-box');
        box.find('input[type=hidden]').remove();
        var boxContainer = $this.parents('.fileinput-box-list');
        var maxNumber = boxContainer.attr('data-max');
        var nowNumber = boxContainer.find('.fileinput-box').length;
        if ($this.val()) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if (box.find('img').length) {
                    box.find('img').attr('src', e.target.result);
                } else {
                    var imgHtml = '<img src="' + e.target.result + '">';
                    box.find('.fileinput-button').before(imgHtml);
                    var btnHtml = '<div class="file-remove-btn"><div class="btn btn-sm btn-outline-danger" style="font-size: 0.5rem;">删除</div></div>';
                    box.find('.fileinput-button').after(btnHtml);
                    box.find('.plus-symbol').hide();
                }
            };
            if ($this.hasClass('add-new') && maxNumber > nowNumber) {
                var newBox = box.clone();
                box.after('<div class="fileinput-box">' + newBox.html() + '</div>');
            }
            if ($this[0].files.length) {
                reader.readAsDataURL($this[0].files[0]);
                $this.removeClass('add-new');
            }
        } else {
            box.find('.plus-symbol').show();
            box.find('img').remove();
        }
    }).on('click', '.file-remove-btn', function () {
        var $this = $(this);
        var boxContainer = $this.parents('.fileinput-box-list');
        var box = $this.parents('.fileinput-box');
        var emptyBox = box.clone();
        if (!boxContainer.find('.add-new').length) {
            emptyBox.find('img').remove();
            emptyBox.find('input[type=hidden]').remove();
            emptyBox.find('.fileinput-input').addClass('add-new');
            emptyBox.find('.file-remove-btn').remove();
            emptyBox.find('.plus-symbol').show();
            boxContainer.find('.fileinput-box').last().after('<div class="fileinput-box">' + emptyBox.html() + '</div>')
        }
        box.remove();
    });
    $('.main-item').click(function () {
        $('.sub-item').collapse('hide');
        $(this).next('.sub-item').collapse('toggle')
    });
    $('.list-sub-item .list-group-item').click(function () {
        location.href = $(this).data('url');
    });
    $('.search-form').on('click', '.search-btn', function () {
        if ($('#page-size').get(0)) {
            $(this).parents('form').append('<input type="hidden" name="pageSize" value="' + $('#page-size').val() + '"/>');
        }
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
    if ($('.kindeditor').get(0)) {
        KindEditor.create('.kindeditor', {
            allowFileManager: false,
            uploadJson: '/system/upload/index',
            afterBlur: function () {
                this.sync();
            }
        });
    }
});