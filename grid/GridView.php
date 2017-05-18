<?php
namespace wonail\adminlte\grid;

use Closure;
use kartik\dialog\Dialog;
use kartik\grid\GridFloatHeadAsset;
use kartik\grid\GridPerfectScrollbarAsset;
use kartik\grid\GridResizeColumnsAsset;
use kartik\grid\GridResizeStoreAsset;
use kartik\grid\GridViewAsset;
use rmrevin\yii\fontawesome\FA;
use wonail\adminlte\assetBundle\GridExportAsset;
use wonail\adminlte\assetBundle\GridSearchAsset;
use wonail\adminlte\assetBundle\GridToggleAsset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

class GridView extends \kartik\grid\GridView
{

    public $bordered = false;

    public $hover = true;

    public $emptyText = 'aOh! 暂时还没有内容! ';

    public $emptyTextOptions = ['class' => 'text-center'];

    public $summaryOptions = ['class' => 'summary pull-right'];

    /**
     * @var string 主要添加`{header-toolbar}`标识
     */
    public $panelHeadingTemplate = <<< HTML
    {toolbar}
    <h3 class="box-title">
        {heading}
    </h3>
    {headerToolbar}
    <div class="clearfix"></div>
HTML;

    public $panelBeforeTemplate = '{before}';

    public $panelPrefix = 'box box-';

    public $toolbar = [];

    public $export = [
        'options' => [
            'class' => 'btn btn-box-tool',
        ],
        'menuOptions' => [
            'style' => 'z-index:1002' // 当使用floatHeader时，使其在floatHeader之上
        ],
        'fontAwesome' => true,
    ];

    public $resizableColumns = false;

    public $persistResize = true;

    public $floatHeader = false;

    public $floatOverflowContainer = false;

    public $floatHeaderOptions = [];

    /**
     * @var string panel|box组件的路由地址，一般在单页面同时显示多个panel|box组件且多个（一个以上）组件的数据来源和当前的路由地址
     * 不相同时使用，此种情况建议配置[[boxParams]]参数用于标识数据来源的组件用以返回相关数据。
     * 该值为完整的路由地址，如：account/user/index、account/user/forbidden-list，默认值为`Yii::$app->controller->getRoute()`
     */
    public $boxUrl;

    /**
     * @var array panel|box组件的url参数，一般在单页面同时显示多个panel|box组件且多个（一个以上）组件的数据来源和当前的路由地址
     * 不相同时使用，可以用于标识操作来源属于哪个组件。
     * 如['from-box' => 'forbidden-list']，则可在相应的[[Controller]]控制器里根据获取到的`from-box`参数值返回相应结果
     * 给客户端的组件，避免返回其他多余的数据。
     */
    public $boxParams = [];

    /**
     * @var array 搜索按钮的配置数组
     *  - `title`:_string_ Dialog模态框的标题，默认为`Yii::t('wocenter/app', 'Search')`
     *  - `url`:_string_ 搜索页面的路由地址，默认为`search`
     *  - `params`:_array_ 搜索页面路由地址的附加参数，默认为`['referer' => $this->baseUrl']`
     *  - `searchLabel`:_string_ 搜索按钮的标签，默认为`Yii::t('wocenter/app', 'Search')`
     *  - `closeLabel`:_string_ 关闭按钮的标签，默认为`Yii::t('wocenter/app', 'Close') . '(Esc)'`
     *  - `resetLabel`:_string_ 重置按钮的标签，默认为`Yii::t('wocenter/app', 'Reset')`
     *  - `size`:_string_ 模态框的大小类型，默认为`size-wide`，可选至有`default`，`size-wide`，`size-large`
     */
    public $searchOptions = [];

    public $toggleDataOptions = [
        'maxCount' => 1000,
        'minCount' => 30,
    ];

    public function init()
    {
        parent::init();

        $this->formatter->nullDisplay = 'N/A';
        $this->boxUrl = $this->boxUrl ?: Yii::$app->controller->getRoute();
        // 重新获取toggle类型
        $this->_isShowAll = ArrayHelper::getValue($_GET, '_toggle', $this->defaultPagination) === 'all';
        if ($this->_isShowAll) {
            $this->dataProvider->pagination = false;
        } else {
            // 重置分页路由，以自定义的[[boxUrl]]为准，主要用于单页面显示多个panel|box组件时，为组件提供唯一的路由地址
            $this->dataProvider->getPagination()->route = $this->boxUrl;
            if ($this->isFullPageLoad() && !empty($this->boxParams)) {
                $this->dataProvider->getPagination()->params = $this->boxParams;
            }
        }
    }

    protected function initToggleData()
    {
        $defaultOptions = [
            'maxCount' => 10000,
            'minCount' => 500,
            'confirmMsg' => Yii::t(
                'kvgrid',
                'There are {totalCount} records. Are you sure you want to display them all?',
                ['totalCount' => number_format($this->dataProvider->getTotalCount())]
            ),
            'all' => [
                'icon' => 'resize-full',
                'label' => '',
                'class' => 'btn btn-box-tool',
                'title' => Yii::t('kvgrid', 'Show all data'),
                'data-toggle' => 'tooltip',
                'data-widget' => 'toggle',
            ],
            'page' => [
                'icon' => 'resize-small',
                'label' => '',
                'class' => 'btn btn-box-tool',
                'title' => Yii::t('kvgrid', 'Show first page data'),
                'data-toggle' => 'tooltip',
                'data-widget' => 'toggle',
            ],
        ];
        $this->toggleDataOptions = array_replace_recursive($defaultOptions, $this->toggleDataOptions);
        if (!$this->toggleData || !$this->isFullPageLoad()) {
            return;
        }
        foreach (['page', 'all'] as $row) {
            $icon = ArrayHelper::remove($this->toggleDataOptions[$row], 'icon', '');
            $label = !isset($this->toggleDataOptions[$row]['label']) ?
                $defaultOptions[$row]['label'] :
                $this->toggleDataOptions[$row]['label'];
            if (!empty($icon)) {
                $label = "<i class='glyphicon glyphicon-{$icon}'></i> " . $label;
            }
            $this->toggleDataOptions[$row]['label'] = $label;
        }
        $tag = $this->_isShowAll ? 'page' : 'all';
        if (!isset($this->toggleDataOptions[$tag]['title'])) {
            $this->toggleDataOptions[$tag]['title'] = $defaultOptions[$tag]['title'];
        }
    }

    public function renderToggleData()
    {
        if (!$this->toggleData) {
            return '';
        }
        $tag = $this->_isShowAll ? 'page' : 'all';
        $options = $this->toggleDataOptions[$tag];
        if (!$this->showToggle()) {
            Html::addCssClass($options, 'hide');
        }
        $label = ArrayHelper::remove($options, 'label', '');
        static::initCss($this->toggleDataContainer, 'btn-group');

        return Html::button($label, $options);
    }

    protected function genToggleDataScript()
    {
        $this->_toggleScript = '';
        if (!$this->toggleData) {
            return;
        }
        $view = $this->getView();
        $opts = Json::encode(
            [
                'boxId' => $this->options['id'],
                'lib' => new JsExpression(
                    ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog')
                ),
                'all' => [
                    'label' => $this->toggleDataOptions['all']['label'],
                    'title' => $this->toggleDataOptions['all']['title'],
                ],
                'page' => [
                    'label' => $this->toggleDataOptions['page']['label'],
                    'title' => $this->toggleDataOptions['page']['title'],
                ],
            ]
        );
        $this->_toggleOptionsVar = 'wnToggleOpts_' . hash('crc32', $opts);
        $view->registerJs("var {$this->_toggleOptionsVar}={$opts};", View::POS_HEAD);
        GridToggleAsset::register($view);
        $this->_toggleScript = "wnToggleWidget({$this->_toggleOptionsVar});";
    }

    protected $_showToggle;

    /**
     * 结合js动态显示切换按钮
     */
    protected function showToggle()
    {
        if ($this->_showToggle === null) {
            if (!$this->toggleData || strpos($this->renderToolbar(), '{toggleData}') === false) {
                $this->_showToggle = false;
            } else {
                $maxCount = ArrayHelper::getValue($this->toggleDataOptions, 'maxCount', false);
                if ($maxCount !== true && (!$maxCount || (int)$maxCount <= $this->dataProvider->getTotalCount())) {
                    $this->_showToggle = false;
                } else {
                    $this->_showToggle = true;
                }
            }
        }

        return $this->_showToggle;
    }

    public function renderPager()
    {
        $renderPager = parent::renderPager();
        $minCount = ArrayHelper::getValue($this->toggleDataOptions, 'minCount', 0);
        $toggleData = Html::tag('span', '', [
            'data-toggle-params' => Json::encode([
                'useToggle' => $this->showToggle() ? 'true' : 'false', // 结合js动态显示切换按钮
                'mode' => $this->_isShowAll ? 'all' : 'page',
                'showMessage' => (!$minCount || $minCount >= $this->dataProvider->getTotalCount()) ? 'false' : 'true',
                'message' => $this->toggleDataOptions['confirmMsg'],
            ]),
        ]);

        return $this->showToggle() ? $renderPager . $toggleData : $renderPager;
    }

    protected function renderPanel()
    {
        if (!$this->isFullPageLoad() || !$this->bootstrap || !is_array($this->panel) || empty($this->panel)) {
            return;
        }
        $type = ArrayHelper::getValue($this->panel, 'type', parent::TYPE_DEFAULT);
        $heading = $this->renderBoxHeader();
        $footer = $this->renderBoxFooter();
        $before = ArrayHelper::getValue($this->panel, 'before', '');
        $after = ArrayHelper::getValue($this->panel, 'after', '');
        $headingOptions = ArrayHelper::getValue($this->panel, 'headingOptions', []);
        $footerOptions = ArrayHelper::getValue($this->panel, 'footerOptions', []);
        $beforeOptions = ArrayHelper::getValue($this->panel, 'beforeOptions', []);
        $afterOptions = ArrayHelper::getValue($this->panel, 'afterOptions', []);
        $panelHeading = '';
        $panelBefore = '';
        $panelAfter = '';
        $panelFooter = '';

        if ($heading !== false) {
            static::initCss($headingOptions, 'box-header with-border');
            $content = strtr($this->panelHeadingTemplate, ['{heading}' => $heading]);
            $panelHeading = Html::tag('div', $content, $headingOptions);
        }
        if ($footer !== false) {
            static::initCss($footerOptions, 'box-footer');
            $content = strtr($this->panelFooterTemplate, ['{footer}' => $footer]);
            $panelFooter = Html::tag('div', $content, $footerOptions);
        }
        if ($before !== false) {
            static::initCss($beforeOptions, 'box-before');
            $content = strtr($this->panelBeforeTemplate, ['{before}' => $before]);
            $panelBefore = Html::tag('div', $content, $beforeOptions);
        }
        if ($after !== false) {
            static::initCss($afterOptions, 'box-after');
            $content = strtr($this->panelAfterTemplate, ['{after}' => $after]);
            $panelAfter = Html::tag('div', $content, $afterOptions);
        }
        $this->layout = strtr($this->panelTemplate, [
            '{prefix}' => $this->panelPrefix,
            '{type}' => $type,
            '{panelHeading}' => $panelHeading,
            '{panelFooter}' => $panelFooter,
            '{panelBefore}' => $panelBefore,
            '{panelAfter}' => $panelAfter,
        ]);
    }

    protected function initLayout()
    {
        // 该设置提前放置，主要用于Dialog部件使用时需要，因为在部件内需要使用overlay加载层
        Html::addCssClass($this->containerOptions, 'box-body');
        if (!$this->isFullPageLoad()) {
            $this->layout = Html::tag('div', $this->layout, $this->containerOptions);
        } else {
            Html::addCssClass($this->filterRowOptions, 'skip-export');
            $gridId = empty($this->options['id']) ? $this->getId() : $this->options['id'];
            if ($this->resizableColumns && $this->persistResize) {
                $key = empty($this->resizeStorageKey) ? Yii::$app->user->id : $this->resizeStorageKey;
                $this->containerOptions['data-resizable-columns-id'] = (empty($key) ? "kv-{$gridId}" : "kv-{$key}-{$gridId}");
            }
            if ($this->hideResizeMobile) {
                Html::addCssClass($this->options, 'hide-resize');
            }
            // 添加box url，为每个box组件添加唯一的url，供刷新等局部操作之用
            $this->options['data-box-url'] = Yii::$app->getUrlManager()->createUrl(array_merge(
                (array)$this->boxUrl,
                array_merge(Yii::$app->getRequest()->getQueryParams(), $this->boxParams)
            ));

            // 重新梳理右侧工具栏的加载流程
            $toolbar = $this->renderToolbar();
            $export = $toggleData = $refresh = $search = $remove = $collapse = '';
            if (strpos($toolbar, '{export}') > 0) {
                $export = $this->renderExport();
            }
            if (strpos($toolbar, '{toggleData}') > 0) {
                $toggleData = $this->renderToggleData();
            }
            if (strpos($toolbar, '{refresh}') > 0) {
                $refresh = $this->renderRefreshButton();
            }
            if (strpos($toolbar, '{search}') > 0) {
                $search = $this->renderSearchButton();
            }
            if (strpos($toolbar, '{remove}') > 0) {
                $remove = $this->renderRemoveButton();
            }
            if (strpos($toolbar, '{collapse}') > 0) {
                $collapse = $this->renderCollapseButton();
            }
            $toolbar = strtr(
                $toolbar, [
                    '{export}' => $export,
                    '{toggleData}' => $toggleData,
                    '{refresh}' => $refresh,
                    '{search}' => $search,
                    '{collapse}' => $collapse,
                    '{remove}' => $remove,
                ]
            );

            // 添加头部工具栏
            $headerToolbar = $this->renderHeaderToolbar();
            if ($headerToolbar != false) {
                if (strpos($this->layout, '{headerToolbar}') > 0) {
                    $replace['{headerToolbar}'] = Html::tag('div', $headerToolbar, [
                        'class' => 'pull-left',
                        'style' => !empty(ArrayHelper::getValue($this->panel, 'heading', '')) ? 'margin-left:5px' : '',
                    ]);
                }
            } else {
                $replace['{headerToolbar}'] = $headerToolbar;
            }

            $replace['{toolbar}'] = Html::tag('div', $toolbar, [
                'class' => 'box-tools pull-right',
                'style' => !empty($replace['{headerToolbar}']) ? 'top:14px' : '', // 修正样式
            ]);

            $this->layout = strtr($this->layout, $replace);
            $this->layout = str_replace('{items}', Html::tag('div', "{summary}\n{items}\n{pager}", $this->containerOptions), $this->layout);
            if (is_array($this->replaceTags) && !empty($this->replaceTags)) {
                foreach ($this->replaceTags as $key => $value) {
                    if ($value instanceof Closure) {
                        $value = call_user_func($value, $this);
                    }
                    $this->layout = str_replace($key, $value, $this->layout);
                }
            }
        }
    }

    protected function renderBoxHeader()
    {
        // 是否隐藏顶部条
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        if (empty($heading) && empty($this->toolbar)) {
            return false;
        }

        return $heading;
    }

    protected function renderHeaderToolbar()
    {
        $headerToolbar = ArrayHelper::getValue($this->panel, 'headerToolbar', '');
        if (empty($headerToolbar) && empty($this->toolbar)) {
            return false;
        }
        $headerToolbar = is_array($headerToolbar) ? implode("\n", $headerToolbar) : $headerToolbar;
        $headerToolbar = strtr(
            $headerToolbar, [
                '{goback}' => $this->renderGobackButton(),
            ]
        );

        return $headerToolbar;
    }

    protected function renderBoxFooter()
    {
        // 是否隐藏底部条
        $footer = ArrayHelper::getValue($this->panel, 'footer', '');
        if (empty($footer)) {
            return false;
        }

        return $footer;
    }

    /**
     * Renders the remove button
     *
     * @return string
     */
    public function renderRemoveButton()
    {
        $options = [
            'class' => 'btn btn-box-tool',
            'data-widget' => 'remove',
            'data-toggle' => 'tooltip',
        ];
        return Html::button(FA::i('times'), $options);
    }

    /**
     * Renders the collapse button
     *
     * @return string
     */
    public function renderCollapseButton()
    {
        $options = [
            'class' => 'btn btn-box-tool',
            'data-widget' => 'collapse',
            'data-toggle' => 'tooltip',
        ];
        return Html::button(FA::i('minus'), $options);
    }

    public function renderRefreshButton()
    {
        $options = [
            'class' => 'btn btn-box-tool',
            'data-widget' => 'reload-list',
            'data-toggle' => 'tooltip',
            'title' => Yii::t('wocenter/app', 'Refresh'),
        ];

        return Html::button(FA::i(FA::_REFRESH), $options);
    }

    public function renderGobackButton()
    {
        $options = [
            'class' => 'btn',
            'data-widget' => 'goback',
            'data-toggle' => 'tooltip',
            'title' => Yii::t('wocenter/app', 'Go back'),
        ];

        return Html::button(FA::i(FA::_ARROW_LEFT), $options);
    }

    public function renderSearchButton()
    {
        $view = $this->getView();
        $defaultOptions = [
            'title' => Yii::t('wocenter/app', 'Search'),
            'url' => 'search',
            'params' => ['referer' => $this->boxUrl],
            'searchLabel' => Yii::t('wocenter/app', 'Search'),
            'closeLabel' => Yii::t('wocenter/app', 'Close') . '(Esc)',
            'resetLabel' => Yii::t('wocenter/app', 'Reset'),
            'size' => 'size-wide',
        ];
        $options = array_replace_recursive($defaultOptions, $this->searchOptions);
        $searchDialogId = $this->options['id'] . '-search-dialog';

        // 打开搜索模态框时删除`_toggle`,`page`,`per-page`参数，以免[[Controller::display]]无法正确渲染视图
        $params = "var boxUrl=$('#{$this->options['id']}').attr('data-box-url');" .
            "boxUrl=wn.url.deleteQueryString(boxUrl, 'page');" .
            "boxUrl=wn.url.deleteQueryString(boxUrl, 'per-page');" .
            "boxUrl=wn.url.deleteQueryString(boxUrl, '_toggle');" .
            "var boxParams=((pos=boxUrl.indexOf('?')) !== -1)?boxUrl.substring(pos+1):'';";
        $bdAjaxOpts = Json::encode([
            'type' => 'get',
            'url' => Url::toRoute(array_merge((array)ArrayHelper::remove($options, 'url'), ArrayHelper::remove($options, 'params'))),
            'data' => new JsExpression('boxParams'), // 添加搜索条件或其他附加参数
            'timeout' => "4000",
            'dataType' => "HTML",
            'success' => new JsExpression("function(data){addToDialog(data, dialog, '{$searchDialogId}');}"),
            'error' => new JsExpression("function(XMLHttpRequest, textStatus, errorThrown){gridErrorResponse(XMLHttpRequest, errorThrown);}"),
        ]);
        $opts = Json::encode([
            'title' => ArrayHelper::remove($options, 'title'),
            'size' => ArrayHelper::remove($options, 'size'),
            'message' => new JsExpression("function(dialog){{$params}$.ajax($bdAjaxOpts);}"),
            'buttons' => [
                [
                    'id' => 'submit',
                    'label' => ArrayHelper::remove($options, 'searchLabel'),
                    'cssClass' => 'btn-success',
                    'action' => new JsExpression('function(){$("#search_div").trigger("submit");}'),
                ],
                [
                    'id' => 'reset',
                    'label' => ArrayHelper::remove($options, 'resetLabel'),
                    'action' => new JsExpression('function(){$("#search_div").trigger("reset");}'),
                ],
                [
                    'id' => 'close',
                    'label' => ArrayHelper::remove($options, 'closeLabel'),
                    'action' => new JsExpression('function(dialogRef){dialogRef.close();}'),
                ],
            ],
        ]);

        $widgetSearchOpts = 'wnSearchOpts_' . hash('crc32', $opts);
        $view->registerJs("var {$widgetSearchOpts}={$opts}", View::POS_HEAD);

        $widgetSearch = 'wnSearchWidget_' . hash('crc32', "#{$this->options['id']} [data-widget=search]");
        $opts = Json::encode([
            'boxId' => $this->options['id'],
            'dialogId' => $searchDialogId,
            'widgetSearch' => new JsExpression("{$widgetSearch}"),
        ]);
        $view->registerJs("var {$widgetSearch}=new BootstrapDialog({$widgetSearchOpts});\nwnSearchWidget({$opts});");

        GridSearchAsset::register($view);

        return Html::button(FA::i(FA::_SEARCH), [
            'class' => 'btn btn-box-tool',
            'data-widget' => 'search',
            'data-toggle' => 'tooltip',
            'title' => $defaultOptions['title'],
        ]);
    }

    protected function registerAssets()
    {
        if (!$this->isFullPageLoad()) {
            return;
        }
        $view = $this->getView();
        $script = '';
        if ($this->bootstrap) {
            GridViewAsset::register($view);
        }
        Dialog::widget($this->krajeeDialogSettings);
        $gridId = $this->options['id'];
        $NS = '.' . str_replace('-', '_', $gridId);
        if ($this->export !== false && is_array($this->export) && !empty($this->export)) {
            GridExportAsset::register($view);
            $target = ArrayHelper::getValue($this->export, 'target', self::TARGET_BLANK);
            $gridOpts = Json::encode(
                [
                    'gridId' => $gridId,
                    'target' => $target,
                    'messages' => $this->export['messages'],
                    'exportConversions' => $this->exportConversions,
                    'showConfirmAlert' => ArrayHelper::getValue($this->export, 'showConfirmAlert', true),
                ]
            );
            $gridOptsVar = 'kvGridExp_' . hash('crc32', $gridOpts);
            $view->registerJs("var {$gridOptsVar}={$gridOpts};", View::POS_HEAD);
            foreach ($this->exportConfig as $format => $setting) {
                $id = "$('#{$gridId} .export-{$format}')";
                $genOpts = Json::encode(
                    [
                        'filename' => $setting['filename'],
                        'showHeader' => $setting['showHeader'],
                        'showPageSummary' => $setting['showPageSummary'],
                        'showFooter' => $setting['showFooter'],
                    ]
                );
                $genOptsVar = 'kvGridExp_' . hash('crc32', $genOpts);
                $view->registerJs("var {$genOptsVar}={$genOpts};", View::POS_HEAD);
                $expOpts = Json::encode(
                    [
                        'dialogLib' => ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog'),
                        'gridOpts' => new JsExpression($gridOptsVar),
                        'genOpts' => new JsExpression($genOptsVar),
                        'alertMsg' => ArrayHelper::getValue($setting, 'alertMsg', false),
                        'config' => ArrayHelper::getValue($setting, 'config', []),
                    ]
                );
                $expOptsVar = 'kvGridExp_' . hash('crc32', $expOpts);
                $view->registerJs("var {$expOptsVar}={$expOpts};", View::POS_HEAD);
                $script .= "{$id}.gridexport({$expOptsVar});";
            }
        }
        $container = '$("#' . $this->containerOptions['id'] . '")';
        if ($this->resizableColumns) {
            $rcDefaults = [];
            if ($this->persistResize) {
                GridResizeStoreAsset::register($view);
            } else {
                $rcDefaults = ['store' => null];
            }
            $rcOptions = Json::encode(array_replace_recursive($rcDefaults, $this->resizableColumnsOptions));
            GridResizeColumnsAsset::register($view);
            $script .= "{$container}.resizableColumns('destroy').resizableColumns({$rcOptions});";
        }
        if ($this->floatHeader) {
            GridFloatHeadAsset::register($view);
            // fix floating header for IE browser when using group grid functionality
            $skipCss = '.kv-grid-group-row,.kv-group-header,.kv-group-footer'; // skip these CSS for IE
            $js = 'function($table){return $table.find("tbody tr:not(' . $skipCss . '):visible:first>*");}';
            $opts = [
                'floatTableClass' => 'kv-table-float',
                'floatContainerClass' => 'kv-thead-float',
                'getSizingRow' => new JsExpression($js),
            ];
            if ($this->floatOverflowContainer) {
                $opts['scrollContainer'] = new JsExpression("function(){return {$container};}");
            }
            $this->floatHeaderOptions = array_replace_recursive($opts, $this->floatHeaderOptions);
            $opts = Json::encode($this->floatHeaderOptions);
            $script .= "$('#{$gridId} .kv-grid-table:first').floatThead({$opts});";
            // integrate resizeableColumns with floatThead
            if ($this->resizableColumns) {
                $script .= "{$container}.off('{$NS}').on('column:resize{$NS}', function(e){" .
                    "\$('#{$gridId} .kv-grid-table:nth-child(2)').floatThead('reflow');" .
                    "});";
            }
        }
        if ($this->perfectScrollbar) {
            GridPerfectScrollbarAsset::register($view);
            $script .= "{$container}.perfectScrollbar(" . Json::encode($this->perfectScrollbarOptions) . ");";
        }
        $this->genToggleDataScript();
        $script .= $this->_toggleScript;
        $this->_gridClientFunc = 'kvGridInit_' . hash('crc32', $script);
        $this->options['data-krajee-grid'] = $this->_gridClientFunc;
        $view->registerJs("var {$this->_gridClientFunc}=function(){\n{$script}\n};\n{$this->_gridClientFunc}();");
    }

    /**
     * 是否全页面加载
     *
     * @return boolean
     */
    protected function isFullPageLoad()
    {
        $request = Yii::$app->getRequest();

        return $request->getIsPjax() || (!$request->getIsAjax() && $request->getIsGet());
    }

    protected function beginPjax()
    {
        return;
    }

    protected function endPjax()
    {
        return;
    }

}
