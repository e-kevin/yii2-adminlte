var wnToggleWdiget;
(function ($) {
    "use strict";
    wnToggleWdiget = function (opts) {
        $('#' + opts.boxId + ' [data-widget=toggle]').off('click').on('click', function (e, params) {
            var $btn = $(this),
                mode = $btn.attr('data-toggle-mode'),
                message = $btn.attr('data-toggle-message');
            if (params && params.redirect) {
                var options = {
                    box: $('#' + opts.boxId),
                    hideActionList: true,
                    callback: {
                        normal: function () {
                            $btn.attr('title', mode === 'page' ? opts.page.title : opts.all.title);
                            $btn.attr('data-toggle-mode', mode === 'page' ? 'all' : 'page');
                            $btn.html(mode === 'page' ? opts.page.label : opts.all.label);
                            $btn.tooltip('destroy');
                        }
                    }
                };

                $.ajax({
                    type: 'get',
                    url: wn.url.setQueryString(options.box.attr('data-box-url'), '_toggle', mode === 'page' ? 'all' : 'page'),
                    timeout: "4000",
                    dataType: "HTML",
                    success: function (data) {
                        options.refreshUrl = this.url;
                        successResponse(data, options);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        errorResponse(XMLHttpRequest, errorThrown, options);
                    }
                });
                return;
            }
            if (mode === 'page') {
                e.preventDefault();
                opts.lib.confirm(message, function (result) {
                    if (result) {
                        $btn.trigger('click', {redirect: true});
                    }
                });
            } else {
                $btn.trigger('click', {redirect: true});
            }
        });
    };
})(window.jQuery);