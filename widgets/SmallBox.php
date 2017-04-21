<?php

namespace wonail\adminlte\widgets;

use rmrevin\yii\fontawesome\component\Icon;
use yii\base\Widget;
use yii\helpers\Html;

class SmallBox extends Widget
{

    /**
     * @var array the HTML attributes for the callout tag
     */
    public $options = [];

    /**
     * @var string 背景颜色，颜色值在gumoi\adminlte\AdminLTE
     */
    public $bgColor;

    /**
     * @var string font awesome icon name
     */
    public $icon;

    /**
     * @var string header text
     */
    public $header;

    /**
     * @var string link label
     */
    public $linkLabel = '查看详情 <i class="fa fa-arrow-circle-right"></i>';

    /**
     * @var string|array link route
     */
    public $linkRoute = '#';

    /**
     * @var string short description
     */
    public $description;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, 'small-box');
        if (!empty($this->bgColor)) {
            Html::addCssClass($this->options, $this->bgColor);
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::beginTag('div', $this->options);
        echo Html::tag('div', Html::tag('h3', $this->header) . Html::tag('p', $this->description), ['class' => 'inner']);
        if (!empty($this->icon)) {
            echo Html::tag('div', new Icon($this->icon), ['class' => 'icon']);
        }
        if (!empty($this->linkLabel)) {
            echo Html::a($this->linkLabel, $this->linkRoute, ['class' => 'small-box-footer']);
        }
        echo Html::endTag('div');
        parent::run();
    }

}
