<?php
namespace wonail\adminlte\grid;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class ActionColumn extends \yii\grid\ActionColumn
{

    use ColumnTrait;

    public $header = '操作';

    public $template = '{update} {delete}';

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
     * @var array 允许显示操作id的方法
     */
    public $visibleId = ['update'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->parseVisibility();
    }

    protected function initDefaultButtons()
    {
        $this->initDefaultButton('update', 'pencil', [
            'data-pjax' => 1,
        ]);
        $this->initDefaultButton('delete', 'trash', [
            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            'data-method' => 'post',
        ]);
    }

    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Yii::t('yii', 'Delete');
                        $additionalOptions['data-params'] = [
                            'selection' => $key,
                        ];
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);

                return Html::a($icon, $url, $options);
            };
        }
    }

    public function createUrl($action, $model, $key, $index)
    {
        if (is_callable($this->urlCreator)) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
        } else {
            // 主要添加对是否显示操作ID的支持
            $params = is_array($key) ? $key : (in_array($action, $this->visibleId) ? ['id' => (string)$key] : []);
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return Url::toRoute($params);
        }
    }

}
