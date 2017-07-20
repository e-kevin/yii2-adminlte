<?php

namespace wonail\adminlte\assetBundle;

use \yii\web\AssetBundle;

class ExtAdminlteAsset extends AssetBundle
{

    public $sourcePath = '@vendor/wonail/yii2-adminlte/assets';
    public $js = [
        'js/admlteext.js',
    ];
    public $depends = [
        'wonail\adminlte\assetBundle\AdminLteAsset',
        'wonail\base\assetBundle\CookieAsset',
    ];

}
