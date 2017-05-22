<?php
namespace wonail\adminlte\widgets;

use kartik\base\TranslationTrait;
use wonail\adminlte\AdminLTE;
use wonail\adminlte\assetBundle\ExtAdminlteAsset;
use wonail\adminlte\ToolbarTrait;
use wonail\adminlte\WidgetTrait;
use rmrevin\yii\fontawesome\component\Icon;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Box widget
 */
class Box extends Widget
{

    use WidgetTrait;
    use ToolbarTrait;
    use TranslationTrait;

    /**
     * @var string the template for rendering the grid within a bootstrap styled box.
     * The following special tokens are recognized and will be replaced:
     * - `{boxHeader}`: _string_, which will render the box header block.
     * - `{boxFooter}`: _string_, which will render the box footer block.
     * - `{body}`: _string_, which will render the grid items.
     */
    public $boxTemplate = <<< HTML
    {boxHeader}
    {body}
    {boxFooter}
HTML;

    /**
     * @var string the template for rendering the box header. The following special tokens are
     * recognized and will be replaced:
     * - `{heading}`: _string_, which will render the box heading content.
     * - `{leftToolbar}`: _string_, which will render the [[leftToolbar]] property passed
     * - `{rightToolbar}`: _string_, which will render the [[rightToolbar]] property passed
     */
    public $boxHeaderTemplate = <<< HTML
    <h3 class="box-title">
        {heading}
    </h3>
    {leftToolbar}
    {rightToolbar}
HTML;

    /**
     * @var string the template for rendering the box footer. The following special tokens are
     * recognized and will be replaced:
     * - `{footer}`: _string_, which will render the box footer content.
     * - `{leftToolbar}`: _string_, which will render the [[leftToolbar]] property passed
     * - `{rightToolbar}`: _string_, which will render the [[rightToolbar]] property passed
     */
    public $boxFooterTemplate = <<< HTML
    {footer}
HTML;

    /**
     * @var string 布局模板
     */
    public $template = '{body}';

    /**
     * @var array the HTML attributes for the widget container tag.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var string 头部标题
     */
    public $header;

    /**
     * @var string 头部FontAwesome图标
     */
    public $headerIcon;

    /**
     * @var array 头部配置数据
     */
    public $headerOptions = [];

    /**
     * @var string 尾部内容
     */
    public $footer;

    /**
     * @var array 尾部配置数据
     */
    public $footerOptions = [];

    /**
     * @var string box widget类型，可选项为 AdminLTE::TYPE_DEFAULT, AdminLTE::TYPE_PRIMARY, AdminLTE::TYPE_INFO
     *      , AdminLTE::TYPE_DANGER, AdminLTE::TYPE_WARNING, AdminLTE::TYPE_SUCCESS
     *
     * @see wonail\adminlte\AdminLTE
     */
    public $type = AdminLTE::TYPE_DEFAULT;

    /**
     * @var string box widget填充颜色，可选项在wonail\adminlte\AdminLTE里以bg-开头的常量
     */
    public $color;

    /**
     * @var string 主体内容
     */
    public $body;

    /**
     * @var array 主体配置数据
     */
    public $bodyOptions = [];

    /**
     * @var boolean 是否为Panel widget小部件，默认为`false`
     */
    public $isPanel = false;

    /**
     * @var boolean 头部栏是否使用下边框，默认`使用` 。仅在box widget模式下有效
     */
    public $withBorder = true;

    /**
     * @var string|array 右侧工具栏
     * - 为字符窜，则直接返回结果
     * - 为数组，则应包含以下键名:
     *     - `{collapse}`, _string_ 折叠按钮.
     *     - `{remove}`, _string_ 移除当前box widget按钮.
     *     - `content`: _string_, 将被解析的内容，如果包含以下特定标签，则会自动返回相应渲染结果:
     *     - `options`: _array_, 配置参数，默认为`btn-group`.
     */
    public $rightToolbar = [
        '{collapse}',
        '{remove}',
    ];

    /**
     * @var string|array 右侧工具栏
     * - 为字符窜，则直接返回结果
     * - 为数组，则应包含以下键名:
     *     - `{goback}`, _string_ 移除当前box widget按钮.
     *     - `content`: _string_, 将被解析的内容，如果包含以下特定标签，则会自动返回相应渲染结果:
     *     - `options`: _array_, 配置参数，默认为`btn-group`.
     */
    public $leftToolbar = '';

    /**
     * @var boolean 是否使用cookie记住折叠状态，默认为`不记住`
     */
    public $collapseRemember = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initI18N('@wonail/adminlte', 'adminlte');
        parent::init();

        if (empty($this->options['id'])) {
            $this->options['id'] = $this->uniqueWidget($this->isPanel ? 'panel' : 'box');
        }
        // 添加box url，为每个box组件添加唯一的url，供刷新等局部操作之用
        $this->boxUrl = $this->boxUrl ?: Yii::$app->controller->getRoute();
        $this->options['data-box-url'] = Yii::$app->getUrlManager()->createUrl(array_merge(
            (array)$this->boxUrl,
            array_merge(Yii::$app->getRequest()->getQueryParams(), $this->boxParams)
        ));
        if (!$this->body) {
            ob_start();
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->body = $this->body ?: ob_get_clean();
        $this->renderBox();
        $this->initTemplate();

        echo $this->template;
        $this->registerAsset();
    }

    protected function renderBox()
    {
        $header = $this->renderHeader();
        $footer = $this->renderFooter();
        $boxHeader = '';
        $boxFooter = '';

        // 初始化box widget的样式配置
        $this->initBoxCss();
        // 渲染头部
        if ($header !== false) {
            Html::addCssClass($this->headerOptions, 'box-header');
            if ($this->withBorder) {
                Html::addCssClass($this->headerOptions, 'with-border');
            }
            $content = strtr($this->boxHeaderTemplate, ['{heading}' => $header]);
            $boxHeader = Html::tag('div', $content, $this->headerOptions);
        }
        // 渲染尾部
        if ($footer !== false) {
            Html::addCssClass($this->footerOptions, 'box-footer');
            $content = strtr($this->boxFooterTemplate, ['{footer}' => $footer]);
            $boxFooter = Html::tag('div', $content, $this->footerOptions);
        }
        // 渲染主体
        Html::addCssClass($this->bodyOptions, 'box-body');

        $this->template = strtr($this->boxTemplate, [
            '{boxHeader}' => $boxHeader,
            '{boxFooter}' => $boxFooter,
            '{body}' => Html::tag('div', $this->body, $this->bodyOptions),
        ]);
    }

    /**
     * @var boolean 是否使用折叠功能
     */
    protected $useCollapse = false;

    /**
     * 初始化box widget的样式配置
     */
    protected function initBoxCss()
    {
        Html::addCssClass($this->options, 'box');
        // Panel widget
        if ($this->isPanel) {
            Html::addCssClass($this->options, 'box-solid');
        }
        Html::addCssClass($this->options, "box-{$this->type}");
        if ($this->color) {
            Html::addCssClass($this->options, "{$this->color}");
        }
        // 折叠小部件
        $rightToolbar = $this->renderRightToolbar();
        if (!empty($rightToolbar)) {
            if (strpos($rightToolbar, '{collapse}') !== false) {
                $this->useCollapse = true;
            }
        }
        if ($this->useCollapse && $this->collapseRemember) {
            Html::addCssClass($this->options, 'collapsed-box');
        }
    }

    protected function initTemplate()
    {
        // 左侧工具栏
        $leftToolbar = $this->renderLeftToolbar();
        $goback = '';
        if (!empty($leftToolbar)) {
            if (strpos($leftToolbar, '{goback}') !== false) {
                $goback = $this->renderGobackButton();
            }
            $leftToolbar = strtr(
                $leftToolbar, [
                    '{goback}' => $goback,
                ]
            );
            $leftToolbar = Html::tag('div', $leftToolbar, [
                'class' => 'pull-left',
            ]);
        }
        $replace['{leftToolbar}'] = $leftToolbar;

        // 右侧工具栏
        $rightToolbar = $this->renderRightToolbar();
        $remove = $collapse = '';
        if (!empty($rightToolbar)) {
            if (strpos($rightToolbar, '{collapse}') !== false) {
                $collapse = $this->renderCollapseButton(['style' => $this->isPanel ? 'color:white' : null], $this->collapseRemember);
            }
            if (strpos($rightToolbar, '{remove}') !== false) {
                $remove = $this->renderRemoveButton(['style' => $this->isPanel ? 'color:white' : null]);
            }
            $rightToolbar = strtr(
                $rightToolbar, [
                    '{collapse}' => $collapse,
                    '{remove}' => $remove,
                ]
            );
            $rightToolbar = Html::tag('div', $rightToolbar, [
                'class' => 'box-tools pull-right',
                'style' => !empty($leftToolbar) ? 'top:12px' : null, // 修正样式
            ]);
        }
        $replace['{rightToolbar}'] = $rightToolbar;

        $this->template = Html::tag('div', strtr($this->template, $replace), $this->options);
    }

    /**
     * 生成头部栏
     */
    protected function renderHeader()
    {
        if (empty($this->header) && empty($this->rightToolbar) && empty($this->leftToolbar)) {
            return false;
        }

        return (isset($this->headerIcon) ? new Icon($this->headerIcon) . '&nbsp;' : '') . $this->header;
    }

    /**
     * 生成尾部栏
     */
    protected function renderFooter()
    {
        return $this->footer ?: false;
    }

    /**
     * 生成右侧工具栏
     *
     * @return string
     */
    protected function renderRightToolbar()
    {
        if (empty($this->rightToolbar) || (!is_string($this->rightToolbar) && !is_array($this->rightToolbar))) {
            return '';
        }
        if (is_string($this->rightToolbar)) {
            return $this->rightToolbar;
        }
        $rightToolbar = '';
        foreach ($this->rightToolbar as $item) {
            if (is_array($item)) {
                $content = ArrayHelper::getValue($item, 'content', '');
                $options = ArrayHelper::getValue($item, 'options', []);
                Html::addCssClass($options, 'btn-group');
                $rightToolbar .= Html::tag('div', $content, $options);
            } else {
                $rightToolbar .= "\n{$item}";
            }
        }

        return $rightToolbar;
    }

    /**
     * 生成左侧工具栏
     *
     * @return string
     */
    protected function renderLeftToolbar()
    {
        if (empty($this->leftToolbar) || (!is_string($this->leftToolbar) && !is_array($this->leftToolbar))) {
            return '';
        }
        if (is_string($this->leftToolbar)) {
            return $this->leftToolbar;
        }
        $leftToolbar = '';
        foreach ($this->leftToolbar as $item) {
            if (is_array($item)) {
                $content = ArrayHelper::getValue($item, 'content', '');
                $options = ArrayHelper::getValue($item, 'options', []);
                Html::addCssClass($options, 'btn-group');
                $leftToolbar .= Html::tag('div', $content, $options);
            } else {
                $leftToolbar .= "\n{$item}";
            }
        }

        return $leftToolbar;
    }

    public function registerAsset()
    {
        if ($this->useCollapse && $this->collapseRemember) {
            $view = $this->getView();
            ExtAdminlteAsset::register($view);
        }
    }

}
