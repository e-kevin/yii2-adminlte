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
     * @var string the theme name to be used for styling the Select2.
     */
    public $theme = self::THEME_DEFAULT;

    /**
     * Registers the asset bundle and locale
     */
    public function registerAssetBundle()
    {
        $view = $this->getView();
        $lang = isset($this->language) ? $this->language : '';
        Select2Asset::register($view)->addLanguage($lang, '', 'js/i18n');
        if (in_array($this->theme, self::$_inbuiltThemes)) {
            \wonail\adminlte\assetBundle\Select2Asset::register($view); // AdminLte主题下的样式修正
        }
    }

}
