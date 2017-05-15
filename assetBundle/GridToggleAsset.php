<?php

namespace wonail\adminlte\assetBundle;

use yii\base\Exception;
use yii\web\AssetBundle;

class GridToggleAsset extends AssetBundle
{

    public $sourcePath = '@wonail/adminlte/assets';

    public $js = [
        'js/wn-grid-toggle.js',
    ];

}
