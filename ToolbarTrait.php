<?php
namespace wonail\adminlte;

use rmrevin\yii\fontawesome\FA;
use wonail\adminlte\assetBundle\GridSearchAsset;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Class ToolbarTrait
 *
 * @method View getView()
 */
trait ToolbarTrait
{

    /**
     * Renders the remove button
     *
     * @param array $options
     *
     * @return string
     */
    protected function renderRemoveButton($options = [])
    {
        $options = array_replace_recursive([
            'class' => 'btn btn-box-tool',
            'data-widget' => 'remove',
            'data-toggle' => 'tooltip',
        ], $options);

        return Html::button(FA::i('times'), $options);
    }

    /**
     * Renders the collapse button
     *
     * @param array $options
     * @param boolean $collapsed
     *
     * @return string
     */
    protected function renderCollapseButton($options = [], $collapsed = false)
    {
        $options = array_replace_recursive([
            'class' => 'btn btn-box-tool',
            'data-widget' => 'collapse',
        ], $options);

        return Html::button($collapsed ? FA::i('plus') : FA::i('minus'), $options);
    }

    /**
     * Renders the refresh list button
     *
     * @return string
     */
    protected function renderRefreshButton()
    {
        $options = [
            'class' => 'btn btn-box-tool',
            'data-widget' => 'reload-list',
            'data-toggle' => 'tooltip',
            'title' => Yii::t('adminlte', 'Refresh'),
        ];

        return Html::button(FA::i(FA::_REFRESH), $options);
    }

    /**
     * Renders the go back button
     *
     * @return string
     */
    protected function renderGobackButton()
    {
        $options = [
            'class' => 'btn',
            'data-widget' => 'goback',
            'data-toggle' => 'tooltip',
            'title' => Yii::t('adminlte', 'Go back'),
        ];

        return Html::button(FA::i(FA::_ARROW_LEFT), $options);
    }

    /**
     * Renders the search button
     *
     * @return string
     */
    protected function renderSearchButton()
    {
        $view = $this->getView();
        $defaultOptions = [
            'title' => Yii::t('adminlte', 'Search'),
            'url' => 'search',
            'params' => ['referer' => $this->boxUrl],
            'searchLabel' => Yii::t('adminlte', 'Search'),
            'closeLabel' => Yii::t('adminlte', 'Close') . '(Esc)',
            'resetLabel' => Yii::t('adminlte', 'Reset'),
            'size' => 'size-wide',
        ];
        $options = array_replace_recursive($defaultOptions, $this->searchOptions);
        $searchDialogId = $this->options['id'] . '-search-dialog';

        // 打开搜索模态框时删除`_toggle`,`page`,`per-page`参数，以免[[Controller::display]]无法正确渲染视图
        $params = "var boxUrl=$('#{$this->options['id']}').attr('data-box-url');" .
            "boxUrl=wn.url.deleteQueryString(boxUrl, 'page');" .
            "boxUrl=wn.url.deleteQueryString(boxUrl, 'per-page');" .
            "boxUrl=wn.url.deleteQueryString(boxUrl, '_toggle');" .
            "var boxParams=((pos=boxUrl.indexOf('?')) !== -1)?boxUrl.substring(pos+1):'';";
        $bdAjaxOpts = Json::encode([
            'type' => 'get',
            'url' => Url::toRoute(array_merge((array)ArrayHelper::remove($options, 'url'), ArrayHelper::remove($options, 'params'))),
            'data' => new JsExpression('boxParams'), // 添加搜索条件或其他附加参数
            'timeout' => "4000",
            'dataType' => "HTML",
            'success' => new JsExpression("function(data){addToDialog(data, dialog, '{$searchDialogId}');}"),
            'error' => new JsExpression("function(XMLHttpRequest, textStatus, errorThrown){errorResponse(XMLHttpRequest, errorThrown);}"),
        ]);
        $opts = Json::encode([
            'title' => ArrayHelper::remove($options, 'title'),
            'size' => ArrayHelper::remove($options, 'size'),
            'message' => new JsExpression("function(dialog){{$params}$.ajax($bdAjaxOpts);}"),
            'buttons' => [
                [
                    'id' => 'submit',
                    'label' => ArrayHelper::remove($options, 'searchLabel'),
                    'cssClass' => 'btn-success',
                    'action' => new JsExpression('function(){$("#search_div").trigger("submit");}'),
                ],
                [
                    'id' => 'reset',
                    'label' => ArrayHelper::remove($options, 'resetLabel'),
                    'action' => new JsExpression('function(){$("#search_div").trigger("reset");}'),
                ],
                [
                    'id' => 'close',
                    'label' => ArrayHelper::remove($options, 'closeLabel'),
                    'action' => new JsExpression('function(dialogRef){dialogRef.close();}'),
                ],
            ],
        ]);

        $widgetSearchOpts = 'wnSearchOpts_' . hash('crc32', $opts);
        $view->registerJs("var {$widgetSearchOpts}={$opts}", View::POS_HEAD);

        $widgetSearch = 'wnSearchWidget_' . hash('crc32', "#{$this->options['id']} [data-widget=search]");
        $opts = Json::encode([
            'boxId' => $this->options['id'],
            'dialogId' => $searchDialogId,
            'widgetSearch' => new JsExpression("{$widgetSearch}"),
        ]);
        $view->registerJs("var {$widgetSearch}=new BootstrapDialog({$widgetSearchOpts});\nwnSearchWidget({$opts});");

        GridSearchAsset::register($view);

        return Html::button(FA::i(FA::_SEARCH), [
            'class' => 'btn btn-box-tool',
            'data-widget' => 'search',
            'data-toggle' => 'tooltip',
            'title' => $defaultOptions['title'],
        ]);
    }
}
