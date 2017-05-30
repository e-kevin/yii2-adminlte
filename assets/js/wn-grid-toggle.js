var wnToggleWidget;
(function ($) {
    "use strict";
    wnToggleWidget = function (opts) {
        $('#' + opts.boxId + ' [data-widget=toggle]').off('click').on('click', function (e, params) {
            var $btn = $(this),
                options = {
                    $grid: $('#' + opts.boxId),
                    hideActionList: true,
                    refreshToggleStatus: true,
                    callback: {
                        normal: function () {
                            $btn.attr('title', isPage ? opts.page.title : opts.all.title);
                            $btn.html(isPage ? opts.page.label : opts.all.label);
                            $btn.tooltip('destroy');
                        }
                    }
                },
                $toggleParams = options.$grid.find('[data-toggle-params]');

            if ($toggleParams.length) {
                var toggleParams = JSON.parse($toggleParams.attr('data-toggle-params')),
                    message = toggleParams.message,
                    mode = toggleParams.mode,
                    showMsg = toggleParams.showMessage === 'true',
                    isPage = mode === 'page';

                if (params && params.redirect) {
                    $.ajax({
                        type: 'get',
                        url: wn.url.setQueryString(options.$grid.attr('data-box-url'), '_toggle', isPage ? 'all' : 'page'),
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
                if (isPage && showMsg) {
                    e.preventDefault();
                    opts.lib.confirm(message, function (result) {
                        if (result) {
                            $btn.trigger('click', {redirect: true});
                        }
                    });
                } else {
                    $btn.trigger('click', {redirect: true});
                }
            }
        });
    };
})(window.jQuery);