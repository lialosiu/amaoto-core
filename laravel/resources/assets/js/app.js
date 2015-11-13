(function () {
    jQuery.ajaxSetup({
        cache: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).on('submit', 'form', function (e) {
        var action, data, form, method, successCallback, failCallback;
        form = this;
        if (jQuery(form).data('not-handle')) {
            return true;
        }
        action = jQuery(form).attr('action');
        method = jQuery(form).attr('method');
        data = jQuery(form).serializeArray();

        successCallback = jQuery(form).data('success-callback');
        failCallback = jQuery(form).data('fail-callback');

        kurano.doAjax(action, method, data, form, successCallback, failCallback);
        e.preventDefault();
    });

    //$(document).on('click', '.btn-ajax-with-confirm', function () {
    //    var really;
    //    really = confirm($(this).data('content'));
    //    if (!really) {
    //        return;
    //    }
    //    return doAjax($(this).data('action'), $(this).data('method'), {
    //        '_success_redirect': '[RELOAD]'
    //    }, $(this), function (rsp) {
    //        return console.log(rsp);
    //    });
    //});

}).call(this);