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
});
