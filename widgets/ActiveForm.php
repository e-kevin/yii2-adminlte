<?php
namespace wonail\adminlte\widgets;

use kartik\base\TranslationTrait;
use wonail\adminlte\AdminLTE;
use Yii;
use yii\bootstrap\ActiveForm as baseActiveForm;
use yii\helpers\Html;

class ActiveForm extends baseActiveForm
{

    use TranslationTrait;

    /* @var \wonail\adminlte\widgets\ActiveField $fieldClass */
    public $fieldClass = 'wonail\adminlte\widgets\ActiveField';

    public $layout = 'horizontal';

    /**
     * @var array|boolean 当值为`false`时，表示不使用box widget小部件功能，否则详细配置请查看Box相关属性
     * @see \wonail\adminlte\widgets\Box
     */
    public $box = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->box !== false) {
            $defaultBoxOptions = [
                'type' => AdminLTE::TYPE_PRIMARY,
                'rightToolbar' => '',
                'leftToolbar' => '{goback}'
            ];
            $this->box = array_replace_recursive($defaultBoxOptions, $this->box);
            Box::begin($this->box);
        } else {
            $this->initI18N('@wonail/adminlte', 'adminlte');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();

        if ($this->box !== false) {
            Box::end();
        }
    }

    public function defaultButtons($options = [])
    {
        $buttons['submit'] = !isset($options['submit']) ?
            Html::submitButton(Yii::t('adminlte', 'Save'), ['class' => 'btn btn-success width-200']) :
            $options['submit'];
        $buttons['reset'] = !isset($options['reset']) ?
            Html::resetButton(Yii::t('adminlte', 'Reset'), ['class' => 'btn btn-default']) :
            $options['reset'];
        $buttons['return'] = !isset($options['return']) ?
            Html::button(Yii::t('adminlte', 'Go back'), ['class' => 'btn btn-default', 'data-widget' => 'goback']) :
            $options['return'];

        return Html::tag('div', implode("\n", $buttons), ['class' => 'form-actions text-center']);
    }

}
