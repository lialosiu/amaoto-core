(function () {
    $.noty.defaults = {
        layout: 'center',
        theme: 'defaultTheme',
        type: 'alert',
        text: '',
        dismissQueue: true,
        template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
        animation: {
            open: 'animated fadeInDown',
            close: 'animated fadeOutDown',
            easing: 'swing',
            speed: 200
        },
        timeout: 2000,
        force: false,
        modal: true,
        maxVisible: 5,
        killer: false,
        closeWith: ['click'],
        callback: {
            onShow: function () {
            },
            afterShow: function () {
            },
            onClose: function () {
            },
            afterClose: function () {
            },
            onCloseClick: function () {
            }
        },
        buttons: false
    };

}).call(this);