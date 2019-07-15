var toastr = {
    count: 1,
    option: {
        'successClass': 'alert-success',
        'errorClass': 'alert-danger',
    },
    success: function (msg, callback, timeout) {
        if (timeout == null) {
            timeout = 3000;
        }
        var alertC = 'alert' + toastr.count;
        toastr.count++;
        var html = '<div class="alert ' + toastr.option.successClass + ' alert-dismissible fade show ' + alertC + '" role="alert">' +
            '<span>' + msg + '</span>' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">×</span>' +
            '</button></div>';
        var popupContent = $('body').find('.popup-content');
        if (!$('body').find('.popup-content').get(0)) {
            $('body').append('<div class="popup-content"></div>');
            popupContent = $('body').find('.popup-content');
        }
        popupContent.append(html);
        setTimeout(function () {
            $('.' + alertC).alert('close');
            if (typeof callback == 'function') {
                callback();
            }
        }, timeout);

    },
    error: function (msg, callback, timeout) {
        if (timeout == null) {
            timeout = 3000;
        }
        var alertC = 'alert' + toastr.count;
        toastr.count++;
        var html = '<div class="alert ' + toastr.option.errorClass + ' alert-dismissible fade show ' + alertC + '" role="alert">' +
            '<span>' + msg + '</span>' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">×</span>' +
            '</button></div>';
        var popupContent = $('body').find('.popup-content');
        if (!$('body').find('.popup-content').get(0)) {
            $('body').append('<div class="popup-content"></div>');
            popupContent = $('body').find('.popup-content');
        }
        popupContent.append(html);
        setTimeout(function () {
            $('.' + alertC).alert('close');
            if (typeof callback == 'function') {
                callback();
            }
        }, timeout);
    },
    loading: function (type, msg) {
        if (type == null) {
            type = 'show';
        }
        if (msg == null) {
            msg = '数据加载中，请稍后...';
        }
        if (type == 'show') {
            if (!$('#loadingModel').get(0)) {
                var html = '<div id="loadingModel" class="modal">' +
                    '<div class="modal-body">' +
                    '<span><img src="/static/images/load.gif"/></span>' +
                    '<span style="color:red;font-size:15px;">' + msg + '</span>' +
                    '</div></div>';
                $('body').append(html);
                var width = document.documentElement.clientWidth || document.body.clientWidth,
                    height = document.documentElement.clientHeight || document.body.clientHeight,
                    loadingModal = $('#loadingModel');
                var top = (height - loadingModal.height()) / 2;
                var left = (width - loadingModal.width()) / 2;
                loadingModal.css({
                    top: top,
                    left: left
                });
                loadingModal.find('.modal-body').css('padding', 0);
                loadingModal.modal({backdrop: 'static'});
                loadingModal.modal('show');
            }
        } else {
            if ($('#loadingModel').get(0)) {
                $('#loadingModel').modal('hide');
                $('#loadingModel').remove();
            }
        }
    }
};
