<?php
namespace wonail\adminlte\grid;

use Closure;
use yii\helpers\Html;

/**
 * ColumnTrait maintains generic methods used by all column widgets in [[GridView]].
 *
 * @property boolean $hidden
 * @property boolean $hiddenFromExport
 * @property array $options
 * @property array $headerOptions
 * @property array $filterOptions
 * @property array $footerOptions
 * @property array $contentOptions
 * @property array $pageSummaryOptions
 *
 */
trait ColumnTrait
{

    /**
     * Checks `hidden` property and hides the column from display
     */
    protected function parseVisibility()
    {
        if ($this->hidden === true) {
            Html::addCssClass($this->filterOptions, 'kv-grid-hide');
            Html::addCssClass($this->headerOptions, 'kv-grid-hide');
            Html::addCssClass($this->contentOptions, 'kv-grid-hide');
            Html::addCssClass($this->footerOptions, 'kv-grid-hide');
            Html::addCssClass($this->pageSummaryOptions, 'kv-grid-hide');
        }
        if ($this->hiddenFromExport === true) {
            Html::addCssClass($this->filterOptions, 'skip-export');
            Html::addCssClass($this->headerOptions, 'skip-export');
            Html::addCssClass($this->contentOptions, 'skip-export');
            Html::addCssClass($this->footerOptions, 'skip-export');
            Html::addCssClass($this->pageSummaryOptions, 'skip-export');
            Html::addCssClass($this->options, 'skip-export');
        }
        if (is_array($this->hiddenFromExport) && !empty($this->hiddenFromExport)) {
            $tag = 'skip-export-';
            $css = $tag . implode(" {$tag}", $this->hiddenFromExport);
            Html::addCssClass($this->filterOptions, $css);
            Html::addCssClass($this->headerOptions, $css);
            Html::addCssClass($this->contentOptions, $css);
            Html::addCssClass($this->footerOptions, $css);
            Html::addCssClass($this->pageSummaryOptions, $css);
            Html::addCssClass($this->options, $css);
        }
    }

}
