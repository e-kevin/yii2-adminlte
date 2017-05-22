<?php
namespace wonail\adminlte\widgets;

use kartik\base\TranslationTrait;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\bootstrap\ActiveField as baseActiveField;
use yii\bootstrap\Html;

class ActiveField extends baseActiveField
{

    use TranslationTrait;

    public function init()
    {
        $this->initI18N('@wonail/adminlte', 'adminlte');

        parent::init();
    }

    /**
     * @param array $items
     * @param array $options
     * @param bool $generateDefault 是否生成默认选项，默认为`true`
     *
     * @return $this
     */
    public function dropDownList($items, $options = [], $generateDefault = true)
    {
        if ($generateDefault === true && !isset($options['prompt'])) {
            $options['prompt'] = Yii::t('adminlte', 'Please choose');
        }

        return parent::dropDownList($items, $options);
    }

    public function radioList($items, $options = [])
    {
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
        $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions) {
            $options = array_merge(['label' => FA::icon('circle-o') . $label, 'value' => $value], $itemOptions);

            return Html::tag('div', Html::radio($name, $checked, $options), ['class' => 'wn-radio-inline']);
        };

        return parent::radioList($items, $options);
    }

    public function checkboxList($items, $options = [])
    {
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
        $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions) {
            $options = array_merge(['label' => FA::icon('square-o') . $label, 'value' => $value], $itemOptions);

            return Html::tag('div', Html::checkbox($name, $checked, $options), ['class' => 'wn-checkbox-inline']);
        };

        return parent::checkboxList($items, $options);
    }

}
