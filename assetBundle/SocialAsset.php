<?php

namespace wonail\adminlte\assetBundle;

use yii\web\AssetBundle;

/**
 * Social AssetBundle
 */
class SocialAsset extends AssetBundle
{

    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';
    public $css = [
        'css/alt/AdminLTE-bootstrap-social.min.css',
    ];
    public $depends = [
        'wonail\adminlte\assetBundle\AdminLteAsset',
    ];

}
