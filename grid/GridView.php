<?php
namespace wonail\adminlte\grid;

use Closure;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class GridView extends \kartik\grid\GridView
{

    public $bordered = false;
    public $hover = true;
    public $resizableColumns = false;

    public $emptyText = 'aOh! 暂时还没有内容! ';

    public $emptyTextOptions = ['class' => 'text-center'];

    public $summaryOptions = ['class' => 'summary pull-right'];

    public $panelHeadingTemplate = <<< HTML
    <div class="pull-right">
        {toolbar}
    </div>
    <h3 class="box-title">
        {heading}
    </h3>
    <div class="clearfix"></div>
HTML;

    public $panelFooterTemplate = '{footer}';

    public $panelBeforeTemplate = '{before}';

    public $panelAfterTemplate = '{after}';

    public $panelPrefix = 'box box-';

    public $toolbar = [];

    public $toggleDataOptions = [
        'all' => [
            'data' => [
                'pjax' => true,
            ],
        ],
        'page' => [
            'data' => [
                'pjax' => true,
            ],
        ],
    ];

    public function init()
    {
        parent::init();
        $this->formatter->nullDisplay = 'N/A';
    }

    /**
     * Sets the grid layout based on the template and box settings
     */
    protected function renderPanel()
    {
        if (!$this->isFullPageLoad()) {
            return;
        }
        if (!$this->bootstrap || !is_array($this->panel) || empty($this->panel)) {
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
        if (!$this->isFullPageLoad()) {
            $this->layout = Html::tag('div', $this->layout, $this->containerOptions);
        } else {
            Html::addCssClass($this->filterRowOptions, 'skip-export');
            if ($this->resizableColumns && $this->persistResize) {
                $key = empty($this->resizeStorageKey) ? Yii::$app->user->id : $this->resizeStorageKey;
                $gridId = empty($this->options['id']) ? $this->getId() : $this->options['id'];
                $this->containerOptions['data-resizable-columns-id'] = (empty($key) ? "kv-{$gridId}" : "kv-{$key}-{$gridId}");
            }
            if ($this->hideResizeMobile) {
                Html::addCssClass($this->options, 'hide-resize');
            }
            $export = $this->renderExport();
            $toggleData = $this->renderToggleData();
            $toolbar = strtr(
                $this->renderToolbar(), [
                    '{export}' => $export,
                    '{toggleData}' => $toggleData,
                ]
            );
            $replace = ['{toolbar}' => $toolbar];
            if (strpos($this->layout, '{export}') > 0) {
                $replace['{export}'] = $export;
            }
            if (strpos($this->layout, '{toggleData}') > 0) {
                $replace['{toggleData}'] = $toggleData;
            }
            Html::addCssClass($this->containerOptions, 'box-body');
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

    /**
     * Generates the box header
     *
     * @return string
     */
    protected function renderBoxHeader()
    {
        // 是否隐藏顶部条
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        if (empty($heading) && empty($this->toolbar)) {
            return false;
        }
        $heading = is_array($heading) ? implode("\n", $heading) : $heading;
        $heading = strtr(
            $heading, [
                '{search}' => $this->renderSearchButton(),
                '{refresh}' => $this->renderRefreshButton(),
                '{goback}' => $this->renderGobackButton(),
            ]
        );

        return $heading;
    }

    /**
     * Generates the box footer
     *
     * @return string
     */
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
     * Renders the refresh button
     *
     * @return string
     */
    public function renderRefreshButton()
    {
        $options = [
            'class' => 'btn',
            'data-toggle' => 'tooltip',
            'data-reload-list' => Yii::$app->getRequest()->getUrl(),
//            'data-original-title' => '刷新',
        ];

        return Html::button(FA::i(FA::_REFRESH), $options);
    }

    /**
     * Renders the goback button
     *
     * @return string
     */
    public function renderGobackButton()
    {
        $options = [
            'class' => 'btn',
            'onclick' => 'javascript:history.go(-1);',
            'data-toggle' => 'tooltip',
//            'data-original-title' => '返回',
        ];

        return Html::button(FA::i(FA::_ARROW_LEFT), $options);
    }

    /**
     * Renders the search button
     *
     * @return string
     */
    public function renderSearchButton()
    {
        $options = [
            'class' => 'btn',
            'data-fold' => '#search_div',
            'data-toggle' => 'tooltip',
//            'data-original-title' => '搜索',
        ];

        return Html::button(FA::i(FA::_SEARCH), $options);
    }

    /**
     * 是否全页面加载
     *
     * @return bool
     */
    protected function isFullPageLoad()
    {
        $request = Yii::$app->getRequest();

        return $request->getIsPjax() || (!$request->getIsAjax() && $request->getIsGet());
    }

}
