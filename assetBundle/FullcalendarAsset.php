<?php

namespace wonail\adminlte\assetBundle;

use yii\web\AssetBundle;

/**
 * Fullcalendar AssetBundle
 */
class FullcalendarAsset extends AssetBundle
{

    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';
    public $css = [
        'css/alt/AdminLTE-fullcalendar.min.css',
    ];
    public $depends = [
        'wonail\adminlte\assetBundle\AdminLteAsset',
    ];

}
