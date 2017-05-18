<?php
namespace wonail\adminlte\grid;

use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;

class CheckboxColumn extends \yii\grid\CheckboxColumn
{

    use ColumnTrait;

    public $hidden;

    /**
     * @var array HTML attributes for the page summary cell. The following special attributes are available:
     * - `prepend`: _string_, a prefix string that will be prepended before the pageSummary content
     * - `append`: _string_, a suffix string that will be appended after the pageSummary content
     */
    public $pageSummaryOptions = [];

    /**
     * @var boolean 导出时是否隐藏，默认隐藏
     */
    public $hiddenFromExport = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->checkboxOptions['label'] = !isset($this->checkboxOptions['label']) ? FA::icon('square-o') : '';
        $this->parseVisibility();
    }

    /**
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || !$this->multiple) {
            return parent::renderHeaderCellContent();
        } else {
            $checkbox = Html::checkBox($this->getHeaderCheckBoxName(), false, [
                'class' => 'select-on-check-all',
                'label' => $this->checkboxOptions['label'],
            ]);

            return Html::tag('div', $checkbox, ['class' => 'wn-checkbox']);
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return Html::tag('div', parent::renderDataCellContent($model, $key, $index), [
            'class' => 'wn-checkbox',
            'style' => 'padding-top:2px;',
        ]);
    }

}
