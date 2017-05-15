<?php

namespace wonail\adminlte\assetBundle;

use yii\web\AssetBundle;

/**
 * Fix AdminLte AssetBundle
 */
class AdminLteAsset extends AssetBundle
{

    public $sourcePath = '@wonail/adminlte/assets';

    public $css = [
        /**
         * 1、禁止加载谷歌字体
         */
        'css/AdminLTE.min.css',
        /**
         * 1、修正媒体查询时表格的响应样式
         */
        'css/fixAdminLTE.min.css',
//        'css/fixAdminLTE.css',
    ];

    public $js = [
        'js/console.js',
        'js/app.js'
    ];

    public $depends = [
        'yii\widgets\PjaxAsset',
        'wonail\base\assetBundle\WnAsset',
        'wonail\adminlte\assetBundle\BaseAdminLteAsset',
    ];

}
