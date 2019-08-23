$(function () {
    $('.select2').select2();
    $('.fileinput-box').on('change', '.fileinput-input', function () {
        var $this = $(this);
        var box = $this.parents('.fileinput-box');
        if ($(this).val()) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if (box.find('img').length) {
                    box.find('img').attr('src', e.target.result);
                } else {
                    var imgHtml = '<img src="' + e.target.result + '">';
                    box.find('.fileinput-button').before(imgHtml);
                    box.find('.plus-symbol').hide();
                }
            };
            if ($this[0].files.length) {
                reader.readAsDataURL($this[0].files[0]);
            }
        } else {
            box.find('.plus-symbol').show();
            box.find('img').remove();
        }
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
});
