<?php

namespace wonail\adminlte\widgets;

use wonail\adminlte\AdminLTE;
use yii\base\Widget;
use yii\helpers\Html;

class Callout extends Widget
{

    /**
     * @var array the HTML attributes for the callout tag
     */
    public $options = [];

    /**
     * @var string callout type. It may be one of these constants:
     *  - TYPE_SUCCESS
     *  - TYPE_INFO
     *  - TYPE_WARNING
     *  - TYPE_DANGER
     */
    public $type = AdminLTE::TYPE_INFO;

    /**
     * @var string callout content
     */
    public $body;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        Html::addCssClass($this->options, 'callout');
        Html::addCssClass($this->options, "callout-{$this->type}");
        echo Html::beginTag('div', $this->options);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo $this->body;
        echo Html::endTag('div');
    }

}
