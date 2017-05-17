/*
 * The search form submission change to the AJAX mode
 */
var wnSearchWidget;
(function ($) {
    "use strict";
    wnSearchWidget = function (opts) {
        $(document).off('click', '#' + opts.boxId + ' [data-widget=search]')
            .on('click', '#' + opts.boxId + ' [data-widget=search]', function () {
                opts.widgetSearch.realize();
            });

        $(document).off('beforeSubmit.yii.activeForm', '#' + opts.dialogId + ' #search_div')
            .on('beforeSubmit.yii.activeForm', '#' + opts.dialogId + ' #search_div', function (e) {
                var $form = $(this),
                    $overLay = $form.closest('.overlay-wrapper').find('.overlay'),
                    $submitBtn = $(e.delegateTarget.activeElement),
                    $closeBtn = $submitBtn.closest('.bootstrap-dialog').find('[class=close]'),
                    options = {
                        $grid: $('#' + opts.boxId),
                        hideActionList: true,
                        refreshToggleStatus: true,
                        callback: {
                            normal: function () {
                                $submitBtn.removeClass('disabled').prop('disabled', false);
                            },
                            thrown: function () {
                                $closeBtn.click();
                            }
                        }
                    };

                // 添加搜索来源字段，便于[[Controller::display]]正确渲染所需视图
                if ($('input[name=from-search]', $form).length == 0) {
                    $form.append($('<input/>').attr({name: 'from-search', value: 'true', type: 'hidden'}));
                }

                // 添加数据切换字段，使搜索结果的显示方式前后一致
                var $toggleParams = options.$grid.find('[data-toggle-params]'),
                    mode = $toggleParams.length ?
                        JSON.parse($toggleParams.attr('data-toggle-params')).mode :
                        'page';

                if ($('input[name=_toggle]', $form).length == 0) {
                    $form.append($('<input/>').attr({
                        name: '_toggle', value: mode,
                        type: 'hidden'
                    }));
                } else {
                    $('input[name=_toggle]', $form).attr('value', mode);
                }

                $.ajax({
                    type: 'get',
                    url: $form.attr('action'),
                    data: $form.serialize(),
                    timeout: "4000",
                    dataType: "HTML",
                    beforeSend: function () {
                        $submitBtn.addClass('disabled').prop('disabled', true);
                        $overLay.removeClass('hide');
                    },
                    complete: function () {
                        $overLay.addClass('hide');
                    },
                    success: function (data) {
                        options.refreshUrl = this.url;
                        successResponse(data, options);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        errorResponse(XMLHttpRequest, errorThrown, options);
                    }
                });
                return false;
            });
    };
})(window.jQuery);