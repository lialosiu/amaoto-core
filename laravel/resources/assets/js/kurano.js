class kurano {
    static showAjaxingMask(form) {
        jQuery(form).append('<div class="ajaxing-mask"><div class="progress"><div class="progress-bar progress-bar-striped active" style="width: 100%"></div></div></div>');
    }

    static removeAjaxingMask(form) {
        jQuery(form).find('.ajaxing-mask').remove();
    }

    static resolveResponse(response) {
        let resolved = {};

        if (typeof response === 'undefined') {
            resolved.level = 'error';
            resolved.message = '--Response Exception--';
            resolved.redirect = '[NOT_REDIRECT]';
            resolved.errors = [];
            return resolved;
        }

        try {
            resolved = jQuery.parseJSON(response);
        } catch (e) {
            resolved = response;
        }

        return resolved;
    }

    static doAjax(url, type, data, form, successCallback, failCallback) {
        kurano.showAjaxingMask(form);
        jQuery.ajax({
            url: url,
            type: type,
            data: data,
            dataType: 'json',
            success: function (rsp) {
                rsp = kurano.resolveResponse(rsp);
                noty({
                    type: rsp.level,
                    text: rsp.message,
                    callback: {
                        afterClose: function () {
                            switch (rsp.redirect) {
                                case '[NOT_REDIRECT]':
                                    kurano.removeAjaxingMask(form);
                                    if (typeof successCallback === 'function') {
                                        successCallback(rsp);
                                    } else if (typeof successCallback === 'string') {
                                        eval(successCallback);
                                    }
                                    break;
                                case '[RELOAD]':
                                    return location.reload();
                                default:
                                    return location.href = rsp.redirect;
                            }
                        }
                    }
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                var errorsMessage = '', rsp;
                rsp = kurano.resolveResponse(XMLHttpRequest.responseJSON);
                jQuery.each(rsp.errors, function (name, error) {
                    return jQuery.each(error, function (k, msg) {
                        return errorsMessage += '<br/>' + msg;
                    });
                });
                noty({
                    type: rsp.level,
                    text: rsp.message + errorsMessage
                });
                kurano.removeAjaxingMask(form);
                if (typeof failCallback === 'function') {
                    return failCallback(rsp);
                } else if (typeof failCallback === 'string') {
                    eval(failCallback);
                }
            }
        });
    }
}