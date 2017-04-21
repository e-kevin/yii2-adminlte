<?php

namespace wonail\adminlte\widgets;

use rmrevin\yii\fontawesome\component\Icon;
use yii\base\Widget;
use yii\helpers\Html;

class InfoBox extends Widget
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
     * @var string value number
     */
    public $number;

    /**
     * @var boolean is filled box
     */
    public $filled = false;

    /**
     * @var integer progress in percents
     */
    public $progress;

    /**
     * @var string progress description
     */
    public $progressDescription;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Html::addCssClass($this->options, 'info-box');
        if ($this->filled && !empty($this->bgColor)) {
            Html::addCssClass($this->options, $this->bgColor);
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::beginTag('div', $this->options);
        if (!empty($this->icon)) {
            echo Html::tag('span', new Icon($this->icon), [
                'class' => 'info-box-icon ' . (!$this->filled && !empty($this->bgColor) ? $this->bgColor : ''),
            ]);
        }
        echo Html::beginTag('div', ['class' => 'info-box-content']);
        echo Html::tag('span', $this->header, ['class' => 'info-box-text']);
        echo Html::tag('span', $this->number, ['class' => 'info-box-number']);
        echo $this->renderProgress();
        echo Html::tag('span', $this->progressDescription, ['class' => 'progress-description']);
        echo Html::endTag('div');
        echo Html::endTag('div');
    }

    protected function renderProgress()
    {
        if ($this->progress !== null) {
            if ($this->progress > 100) {
                $this->progress = 100;
            } elseif ($this->progress < 0) {
                $this->progress = 0;
            }
            echo Html::tag('div', Html::tag('div', '', [
                'class' => 'progress-bar',
                'style' => 'width:' . (int) $this->progress . '%'
            ]), [
                'class' => 'progress'
            ]);
        }
    }

}
