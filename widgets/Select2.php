<?php
namespace wonail\adminlte\widgets;

use kartik\select2\Select2Asset;

/**
 * Class Select2
 *
 * @package wonail\adminlte\widgets
 * @see https://select2.github.io/examples.html
 */
class Select2 extends \kartik\select2\Select2
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->theme = parent::THEME_DEFAULT;
    }

    /**
     * @inheritdoc
     */
    public function registerAssetBundle()
    {
        $view = $this->getView();
        $lang = isset($this->language) ? $this->language : '';
        Select2Asset::register($view)->addLanguage($lang, '', 'js/i18n');
        \wonail\adminlte\assetBundle\Select2Asset::register($view); // 修正AdminLte主题下的样式
    }

}
