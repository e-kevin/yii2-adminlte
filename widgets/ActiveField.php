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

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initI18N('@wonail/adminlte', 'adminlte');

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function dropDownList($items, $options = [], $generateDefault = true)
    {
        if ($generateDefault === true && !isset($options['prompt'])) {
            $options['prompt'] = Yii::t('adminlte', 'Please choose');
        }

        parent::dropDownList($items, $options);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function radioList($items, $options = [])
    {
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
        $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions) {
            $options = array_merge(['label' => FA::icon('circle-o') . $label, 'value' => $value], $itemOptions);

            return Html::tag('div', Html::radio($name, $checked, $options), ['class' => 'wn-radio-inline']);
        };

        parent::radioList($items, $options);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function checkboxList($items, $options = [])
    {
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
        $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions) {
            $options = array_merge(['label' => FA::icon('square-o') . $label, 'value' => $value], $itemOptions);

            return Html::tag('div', Html::checkbox($name, $checked, $options), ['class' => 'wn-checkbox-inline']);
        };

        parent::checkboxList($items, $options);

        return $this;
    }

    /**
     * 分组显示多个同名CheckboxList。
     * 主要修复在使用同一个[[$name]]分组显示多个[[checkboxList()]]时，因为多个分组使用同一个[[$name]]，前面分组的数据会被
     * 最后一个分组隐藏的同名[[$name]]的checkbox input值覆盖归零。该方法确保只生成一个同名的隐藏input
     *
     * @see Html::activeListInput() Html根据`unselect`值自动添加隐藏input，导致同名input会被最后一个同名input值覆盖
     *
     * @param array $items
     * @param array $options
     *
     * @return $this
     */
    public function groupCheckboxList($items, $options = [])
    {
        $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
        $options['item'] = function ($index, $label, $name, $checked, $value) use ($itemOptions) {
            $options = array_merge(['label' => FA::icon('square-o') . $label, 'value' => $value], $itemOptions);

            return Html::tag('div', Html::checkbox($name, $checked, $options), ['class' => 'wn-checkbox-inline']);
        };

        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = $this->activeListInput('checkboxList', $items, $options);

        return $this;
    }

    /**
     * 主要修复多个同名[[$type]]分组显示时，前面数据会被后面隐藏的input值覆盖问题。该方法确保只生成一个同名的隐藏input
     *
     * @see Html::activeListInput()
     *
     * @param $type
     * @param $items
     * @param array $options
     *
     * @return
     */
    protected function activeListInput($type, $items, $options = [])
    {
        static $index;
        $name = isset($options['name']) ? $options['name'] : Html::getInputName($this->model, $this->attribute);
        $selection = isset($options['value']) ? $options['value'] : Html::getAttributeValue($this->model, $this->attribute);

        if (!isset($index[$name]) && !array_key_exists('unselect', $options)) {
            $index[$name] = 0;
            $options['unselect'] = '';
        }
        if (!array_key_exists('id', $options)) {
            $options['id'] = Html::getInputId($this->model, $this->attribute);
        }

        return Html::$type($name, $selection, $items, $options);
    }

}
