<?php

namespace wonail\adminlte\widgets;

use wonail\adminlte\assetBundle\ExtAdminlteAsset;
use wonail\adminlte\WidgetTrait;
use rmrevin\yii\fontawesome\component\Icon;
use rmrevin\yii\fontawesome\FA;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Box widget and Panel widget
 */
class Box extends Widget
{

    use WidgetTrait;

    /**
     * @var array the HTML attributes for the widget container tag.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var string|array 按钮组
     */
    protected $rightToolbars = [];

    /**
     * @var string 头部标题
     */
    public $header;

    /**
     * @var string FontAwesome图标
     */
    public $headerIcon;

    /**
     * @var array 头部配置数据
     */
    public $headerOptions = [];

    /**
     * @var string box widget类型，当前缀以bg-开头则为panel widget，否则为box widget
     *  - box widget: 可选项为[default, danger, info, primary, success, warning]
     *  - panel widget: 可选项在gumoi\adminlte\AdminLTE里以bg-开头的常量
     */
    public $type = 'default';

    /**
     * @var string 主体内容
     */
    public $body;

    /**
     * @var array 主体配置数据
     */
    public $bodyOptions = [];

    /**
     * @var boolean 是否填充头部和边框，默认`不填充`。仅在box widget模式下有效
     */
    public $filled = false;

    /**
     * @var boolean 头部栏是否使用下边框，默认`使用` 。仅在box widget模式下有效
     */
    public $withBorder = true;

    /**
     *  @var boolean 是否启用折叠功能，默认`不启用`
     */
    public $collapsable = false;

    /**
     * @var boolean 是否展开，默认为`展开 `
     */
    public $expandable = true;

    /**
     * @var boolean 是否使用cookie记住折叠状态，默认为`记住`
     */
    public $collapseRemember = true;

    /**
     * @var boolean 是否开启移除功能
     */
    public $removable = false;

    /**
     * @var boolean 是否为box widget
     */
    private $_isBoxWidget;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_isBoxWidget = strpos($this->type, 'bg-') === false;

        Html::addCssClass($this->options, 'box');
        if ($this->collapsable && !$this->expandable) {
            Html::addCssClass($this->options, 'collapsed-box');
        }
        // Panel widget
        if (!$this->_isBoxWidget) {
            if (empty($this->options['id'])) {
                $this->options['id'] = $this->uniqidWidget('panel');
            }
            Html::addCssClass($this->options, 'box-solid');
            if ($this->type != 'bg-white') {
                Html::addCssClass($this->options, $this->type);
            }
        } else {
            // Box widget
            if (empty($this->options['id'])) {
                $this->options['id'] = $this->uniqidWidget('box');
            }
            Html::addCssClass($this->options, "box-{$this->type}");
            if ($this->filled) {
                Html::addCssClass($this->options, 'box-solid');
            }
        }

        // BOX::START
        echo Html::beginTag('div', $this->options);
        $this->renderHeader();
        // BODY::START
        Html::addCssClass($this->bodyOptions, 'box-body');
        echo Html::beginTag('div', $this->bodyOptions);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo $this->body;
        // BODY::END
        echo Html::endTag('div');
        // BOX::END
        echo Html::endTag('div');

        $this->registerJs();
        parent::run();
    }

    /**
     * 生成头部栏
     */
    protected function renderHeader()
    {
        $this->initRightToolbars();
        $headerIsEmpty = empty($this->header) && empty($this->rightToolbars);
        if (!$headerIsEmpty) {
            // HEADER::START
            $headerOptions = [];
            Html::addCssClass($headerOptions, "box-header");
            if ($this->withBorder && $this->_isBoxWidget) {
                Html::addCssClass($headerOptions, "with-border");
            }
            echo Html::beginTag('div', $headerOptions);
            echo Html::tag('h3', (isset($this->headerIcon) ? new Icon($this->headerIcon) . '&nbsp;' : '') . $this->header, ['class' => 'box-title']);
            if (!empty($this->rightToolbars)) {
                echo Html::tag('div', implode("\n", $this->rightToolbars), ['class' => 'box-toolbars pull-right']);
            }
            // HEADER::END
            echo Html::endTag('div');
        }
    }

    /**
     * 初始化按钮栏
     */
    protected function initRightToolbars()
    {
        $this->renderCollapseButton();
        $this->renderRemoveButton();
    }

    protected function renderCollapseButton()
    {
        if ($this->collapsable) {
            $this->rightToolbars[] = Html::button(
            new Icon($this->expandable ? FA::_MINUS : FA::_PLUS), [
                'class' => 'btn btn-box-tool',
                'data-widget' => 'collapse',
                'style' => !$this->_isBoxWidget ? 'color:white' : '',
            ]);
        }
    }

    protected function renderRemoveButton()
    {
        if ($this->removable) {
            $this->rightToolbars[] = Html::button(
            new Icon('times'), [
                'class' => 'btn btn-box-tool',
                'data-widget' => 'remove',
                'style' => !$this->_isBoxWidget ? 'color:white' : '',
            ]);
        }
    }

    public function registerJs()
    {
        if ($this->collapseRemember && $this->collapsable) {
            $view = $this->getView();
            ExtAdminlteAsset::register($view);
        }
    }

}
