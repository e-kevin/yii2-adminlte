<?php

namespace wonail\adminlte\assetBundle;

use \yii\web\AssetBundle;

class ExtAdminlteAsset extends AssetBundle
{

    public $sourcePath = '@wonail/adminlte/assets';
    public $js = [
        'js/admlteext.min.js',
    ];
    public $depends = [
        'wonail\adminlte\assetBundle\AdminLteAsset',
        'wonail\base\assetBundle\CookieAsset',
    ];

}
