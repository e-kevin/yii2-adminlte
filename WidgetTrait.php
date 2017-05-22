<?php
namespace wonail\adminlte;
use yii\base\Widget;

/**
 * @method Widget getId()
 */
trait WidgetTrait
{

    /**
     * @var string panel|box组件的路由地址，一般在单页面同时显示多个panel|box组件且多个（一个以上）组件的数据来源和当前的路由地址
     * 不相同时使用，此种情况建议配置[[boxParams]]参数用于标识数据来源的组件用以返回相关数据。
     * 该值为完整的路由地址，如：account/user/index、account/user/forbidden-list，默认值为`Yii::$app->controller->getRoute()`
     */
    public $boxUrl;

    /**
     * @var array panel|box组件的url参数，一般在单页面同时显示多个panel|box组件且多个（一个以上）组件的数据来源和当前的路由地址
     * 不相同时使用，可以用于标识操作来源属于哪个组件。
     * 如['from-box' => 'forbidden-list']，则可在相应的[[Controller]]控制器里根据获取到的`from-box`参数值返回相应结果
     * 给客户端的组件，避免返回其他多余的数据。
     */
    public $boxParams = [];
    
    protected function uniqueWidget($widgetName)
    {
        return empty($widgetName) ? $this->getId() : $widgetName . '-' . hash('crc32', $this->boxUrl . $this->getId());
    }

}
