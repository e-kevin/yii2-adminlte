<?php
namespace wonail\adminlte\assetBundle;

use yii\web\AssetBundle;

/**
 * Select2 AssetBundle
 */
class Select2Asset extends AssetBundle
{

    public $sourcePath = '@vendor/wonail/yii2-adminlte/assets';

    public $css = [
        'css/fixSelect2.min.css',
    ];

    public $depends = [
        'wonail\adminlte\assetBundle\AdminLteSelect2Asset',
        'wonail\adminlte\assetBundle\AdminLteAsset',
    ];

}
