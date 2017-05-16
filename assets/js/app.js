/**
 * 是否使用PJAX功能，默认使用
 */
window.PJAX_ENABLED = true;

/**
 * 是否使用调试模式，用于打印一些日志信息
 */
window.DEBUG = true;

$(function () {
    /**
     * @constructor
     */
    var AdminLteApp = function () {
        this.pjaxEnabled = window.PJAX_ENABLED;
        this.debug = window.DEBUG;
        this.$sidebar = $('.main-sidebar');
        this.$contentWrap = '#content-wrapper';
        this.$loaderWrap = $('#loader');
        this.loading = false;

        this._changeActiveNavigationItem();

        if (this.pjaxEnabled) {
            /**
             * Initialize pjax & attaching all related events
             */
            var el = '.main-sidebar a:not([data-no-pjax], [href="#"], [href="javascript:;"])' +
                ', .breadcrumb a' +
                ', [data-pjax]';
            $(el).on('click', $.proxy(this._checkLoading, this));
            var pjax_container = $(el).data('pjax-container') || this.$contentWrap;
            $(document).pjax(el, pjax_container, {
                fragment: pjax_container,
                type: 'GET', //this.debug ? 'POST' : 'GET', //GET - for production, POST - for debug.
                timeout: 4000
            });
            $(document).on('pjax:send', $.proxy(this.showLoader, this));
            $(document).on('pjax:success', $.proxy(this._loadingFinished, this));
            $(document).on('pjax:end', $.proxy(this.hideLoader, this));
            $(document).on('pjax:end', $.proxy(this._changeActiveNavigationItem, this));
            $(document).on('pjax:error', $.proxy(this.catchException, this));
        }

        window.onerror = $.proxy(this._logErrors, this);
    };

    /**
     * Changes active navigation item depending on current page.
     * @param event
     * @param xhr
     * @param options
     * @private
     */
    AdminLteApp.prototype._changeActiveNavigationItem = function (event, xhr, options) {
        var $newActiveLink = this.$sidebar.find('.sidebar-menu a[href ="' + $("#navSelectPage").val() + '"]');
        this.$sidebar.find('.active').removeClass('active');
        $newActiveLink.closest('li').addClass('active');
    };

    AdminLteApp.prototype.extractPageName = function (url) {
        var pageName = url.split('#')[0].substring(url.lastIndexOf("/") + 1).split('?')[0];
        return pageName === '' ? 'index.html' : pageName;
    };

    AdminLteApp.prototype.showLoader = function () {
        this.$loaderWrap.removeClass('hide');
    };

    AdminLteApp.prototype.hideLoader = function () {
        var view = this;
        this.$loaderWrap.one($.support.transition.end, function () {
            view.$loaderWrap.addClass('hide');
        }).emulateTransitionEnd(200);
    };

    AdminLteApp.prototype._checkLoading = function (e) {
        var oldLoading = this.loading;
        this.loading = true;
        if (oldLoading) {
            this.log('attempt to load page while already loading; preventing.');
            e.preventDefault();
        } else {
            this.log(e.currentTarget.href + ' loading started.', this.extractPageName(e.currentTarget.href));
        }
        //prevent default if already loading
        return !oldLoading;
    };

    AdminLteApp.prototype.catchException = function (XMLHttpRequest, textStatus, errorThrown) {
        var data = textStatus.responseText;
        $(this.$contentWrap).html($(data).html());
        this._loadingFinished();
        return false;
    };

    AdminLteApp.prototype._loadingFinished = function () {
        this.loading = false;
    };

    AdminLteApp.prototype._logErrors = function () {
        var errors = JSON.parse(localStorage.getItem('wn-errors')) || {};
        errors[new Date().getTime()] = arguments;
        localStorage.setItem('wn-errors', JSON.stringify(errors));
        this.debug && alert('check errors');
    };

    AdminLteApp.prototype.log = function (message, url) {
        if (this.debug) {
            url = url ? url : this.extractPageName(location.href);
            console.log("AdminLteApp: " + message + " - " + url);
        }
    };

    window.AdminLteApp = new AdminLteApp();

    initAppFunctions();
});

/**
 * Wonail AdminLte required js functions
 */
function initAppFunctions() {
    !function ($) {

        /*
         * Open new windows
         */
        $(document).on('click', '.open-new', function (e) {
            var target;
            if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
                window.open(target);
                e.preventDefault();
            }
        });

        $(document).on('click', '[data-widget]', function () {
            $(this).tooltip('hide');
            $(this).blur();
        });

        /*
         * Reload Page
         */
        $(document).on('click', '[data-page-reload]', function () {
            if (AdminLteApp.pjaxEnabled) {
                $.pjax.reload({container: AdminLteApp.$contentWrap, fragment: AdminLteApp.$contentWrap, timeout: 4000});
            } else {
                location.reload();
            }
        });

        /*
         * Go back
         */
        $(document).on('click', '[data-widget=goback]', function () {
            history.go(-1);
        });

        /*
         * Reload list and hide relation action list
         */
        $(document).on('click', '[data-widget=reload-list]', function () {
            var $this = $(this),
                $oldIcon = $this.children('i').attr('class'),
                data,
                options = {
                    box: $this.closest('.grid-view'),
                    hideActionList: true,
                    refreshToggleStatus: true,
                    callback: {
                        normal: function () {
                            $this.one($.support.transition.end, function () {
                                $this.children('i').attr('class', $oldIcon);
                                $this.removeClass('disabled').prop('disabled', false);
                            }).emulateTransitionEnd(200);
                        }
                    }
                };

            if (options.box.attr('data-box-url').indexOf('reload-list') < 0) {
                data = {'reload-list': 'true'};
            }

            $this.children('i').attr('class', 'fa fa-spin fa-spinner');
            $this.addClass('disabled').prop('disabled', true);

            $.ajax({
                type: 'get',
                url: options.box.attr('data-box-url'),
                data: data,
                timeout: "4000",
                dataType: "HTML",
                success: function (data) {
                    successResponse(data, options);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    errorResponse(XMLHttpRequest, errorThrown, options);
                }
            });
            return false;
        });

        /*
         * Pagination with AJAX
         */
        $(document).on('click', '.pagination a', function () {
            var $this = $(this),
                options = {
                    box: $this.closest('.grid-view'),
                    hideActionList: true,
                    refreshToggleStatus: true,
                };
            $.ajax({
                type: 'GET',
                url: $this.attr('href'),
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
            return false;
        });

        /*
         * All the form submission change to the AJAX mode
         */
        $(document).on('submit', 'form:not(.kv-export-form)', function (e) {
            e.preventDefault();

            var $form = $(this),
                $action = $form.attr('action'),
                $target = $(e.delegateTarget.activeElement),
                method = $target.attr('data-method') ? $target.attr('data-method') : 'POST',
                params = $target.attr('data-params'),
                $isActionListBtn = $target.closest('[data-widget=action-list]').length > 0,
                options = {
                    callback: {
                        normal: function () {
                            $target.removeClass('disabled').prop('disabled', false);
                        }
                    }
                };

            if ($isActionListBtn) {
                var selectData = $target.closest('.grid-view').yiiGridView('getSelectedRows');
                if (selectData.length <= 0) {
                    wn.notificationBox.error('请选择要操作的数据', '', 1500);
                    return false;
                } else {
                    $(selectData).each(function (idx, obj) {
                        $form.append($('<input/>').attr({name: 'selection[]', value: obj, type: 'hidden'}));
                    });
                }
            }

            // temporarily add hidden inputs according to data-params
            if (params !== undefined) {
                if (params.indexOf('{"') === 0) {
                    params = JSON.parse(params);
                }
                if (params && $.isPlainObject(params)) {
                    var input;
                    $.each(params, function (idx, obj) {
                        input = $('input[name="' + idx + '"]', $form);
                        if (input.length) {
                            input.val(obj);
                        } else {
                            $form.append($('<input/>').attr({name: idx, value: obj, type: 'hidden'}));
                        }
                    });
                }
            }

            var $data = $form.serialize();

            $target.addClass('disabled').prop('disabled', true);

            $.ajax({
                type: method,
                url: $action,
                data: $data,
                timeout: "4000",
                dataType: "JSON",
                success: function (data) {
                    successResponse(data, options);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    errorResponse(XMLHttpRequest, errorThrown, options);
                }
            });
            return false;
        });

        /*
         * Table head check all checkboxes
         */
        $(document).on('change.yiiGridView keydown.yiiGridView', 'table input[type=checkbox]', function () {
            var container = $(this).closest('.grid-view');
            toggleHeaderActionList(container.attr('id'), container.find('[data-widget=action-list]'));
        });

        function toggleHeaderActionList(container, actionList) {
            var selectData = $('#' + container).yiiGridView('getSelectedRows');
            if (selectData.length !== 0) {
                actionList.removeClass("hide");
            } else {
                actionList.addClass("hide");
            }
        }

        /*
         * form添加noEnter属性，禁止文本框回车提交
         */
        $('form[noEnter]').each(function () {
            $(':text', $(this)).keypress(function (e) {
                var key = e.which;
                if (key == 13) {
                    return false;
                }
            });
        });

    }(jQuery);
}

$.ajaxSetup({
    beforeSend: function () {
        $(AdminLteApp.$loaderWrap).removeClass('hide');
    },
    complete: function () {
        $(AdminLteApp.$loaderWrap).addClass('hide');
    }
});

/**
 *
 * Ajax方式请求时的错误回调函数
 *
 * @param XMLHttpRequest
 * @param errorThrown
 * @param options 配置信息
 */
function errorResponse(XMLHttpRequest, errorThrown, options) {
    if (errorThrown === 'Not Found') {
        $(AdminLteApp.$contentWrap).html("<section class='content'>" + XMLHttpRequest.responseText + "</section>");
        if (options.callback !== undefined && typeof options.callback.thrown === 'function') {
            (options.callback.thrown)(data);
        }
    } else {
        wn.notificationBox.error(XMLHttpRequest.responseText ? XMLHttpRequest.responseText : '操作超时，请重新执行', '', 1500);
        if (options.callback !== undefined && typeof options.callback.normal === 'function') {
            setTimeout(function () {
                (options.callback.normal)(data);
            }, 1500);
        }
    }
}

/**
 * Ajax方式请求时的成功回调函数
 *
 * @param data 请求成功后返回的结果数据
 * @param options 配置信息
 */
function successResponse(data, options) {
    var defaultOptions = {
        box: false,
        hideActionList: false,
        refreshUrl: false, // 更新boxUrl
        refreshToggleStatus: false,
        callback: false
    };
    options = options ? window.jQuery.extend({}, defaultOptions, options) : defaultOptions;
    // 隐藏头部操作栏
    if (options.hideActionList) {
        var hideActionList = function () {
            options.box.find('[data-widget=action-list]').addClass('hide');
        };
    }

    // 如果返回JSON字符串结果，则解析对象结果
    if (typeof data !== 'object' && data.indexOf('{"') === 0) {
        data = JSON.parse(data);
    }

    if (typeof data === 'object') {
        var time = data.waitSecond ? data.waitSecond * 1000 : 1000;
        if (data.status === 1) {
            wn.notificationBox.success(data.message, '', time);
            // 隐藏操作按钮
            if (options.hideActionList) {
                hideActionList();
            }
        } else {
            wn.notificationBox.error(data.message, '', time);
        }
        if (data.jumpUrl == 'reload-list') {
            $('[data-widget=reload-list]').trigger('click');
        } else if (data.jumpUrl) {
            setTimeout(function () {
                if (AdminLteApp.pjaxEnabled) {
                    $.pjax({
                        url: data.jumpUrl,
                        container: AdminLteApp.$contentWrap,
                        fragment: AdminLteApp.$contentWrap,
                        timeout: 4000
                    });
                } else {
                    location.href = data.jumpUrl;
                }
            }, time);
        }
        if (typeof(options.callback.normal) === 'function') {
            setTimeout(function () {
                (options.callback.normal)(data);
            }, time);
        }
    } else {
        // 更新列表数据
        options.box.find('.box-body').html($(data).find('.box-body').html());
        // 更新切换按钮状态
        if (options.refreshToggleStatus) {
            refreshToggleStatus();
        }
        // 隐藏操作按钮
        if (options.hideActionList) {
            hideActionList();
        }
        if (typeof(options.callback.normal) === 'function') {
            (options.callback.normal)(data);
        }

        /**
         * 更新切换按钮状态
         */
        function refreshToggleStatus() {
            var toggleParams = options.box.find('[data-toggle-params]'),
                toggleBtn = options.box.find('[data-widget=toggle]');
            if (toggleBtn.length) {
                if (toggleParams.length) {
                    var toggleParams = JSON.parse(toggleParams.attr('data-toggle-params')),
                        useToggle = toggleParams.useToggle;
                    if (useToggle) {
                        toggleBtn.removeClass('hide');
                    } else {
                        toggleBtn.addClass('hide');
                    }
                } else {
                    toggleBtn.addClass('hide');
                }
            }
        }
    }

    // 同步更新当前链接
    if (options.refreshUrl) {
        // 删除多余参数
        options.refreshUrl = wn.url.deleteQueryString(options.refreshUrl, 'reload-list');
        options.refreshUrl = wn.url.deleteQueryString(options.refreshUrl, 'from-search');
        options.box.attr('data-box-url', options.refreshUrl);
    }
}

/**
 *
 * 添加内容至当前打开的模态框内
 *
 * @param data 需要添加进模态框的内容
 * @param dialog 当前打开的模态框对象
 * @param dialogId 当前打开的模态框ID
 */
function addToDialog(data, dialog, dialogId) {
    // 如果返回JSON字符串结果，则解析对象结果
    if (typeof data !== 'object' && data.indexOf('{"') === 0) {
        data = JSON.parse(data);
    }

    if (typeof data === 'object') {
        var time = data.waitSecond ? data.waitSecond * 1000 : 1000;
        if (data.status === 1) {
            wn.notificationBox.success(data.message, '', time);
        } else {
            wn.notificationBox.error(data.message, '', time);
        }
        if (data.jumpUrl) {
            setTimeout(function () {
                if (AdminLteApp.pjaxEnabled) {
                    $.pjax({
                        url: data.jumpUrl,
                        container: AdminLteApp.$contentWrap,
                        fragment: AdminLteApp.$contentWrap,
                        timeout: 4000
                    });
                } else {
                    location.href = data.jumpUrl;
                }
            }, time);
        }
    } else {
        var $content = $("<div class='overlay-wrapper' id='" + dialogId + "'></div>")
            .append('<div class="overlay hide"><i class="fa fa-refresh fa-spin"></i></div>' + data);

        dialog.$modalBody.find('.bootstrap-dialog-message').append($content);
        dialog.open();
    }
}