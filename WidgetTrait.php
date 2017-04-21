<?php

namespace wonail\adminlte;

/**
 * @method \yii\bootstrap\Widget getId()
 */
trait WidgetTrait
{
    
    protected function uniqidWidget($widgetName)
    {
        return empty($widgetName) ? $this->getId() : $widgetName . '-' . hash('crc32', \Yii::$app->getRequest()->getUrl() . $this->getId());
    }

}
