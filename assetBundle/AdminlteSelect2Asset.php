<?php

namespace wonail\adminlte\assetBundle;

use yii\web\AssetBundle;

/**
 * Select2 AssetBundle
 */
class AdminlteSelect2Asset extends AssetBundle
{

    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';
    public $css = [
        'css/alt/AdminLTE-select2.min.css',
    ];
    public $depends = [
        'wonail\adminlte\assetBundle\AdminLteAsset',
    ];

}
