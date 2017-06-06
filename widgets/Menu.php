<?php

namespace wonail\adminlte\widgets;

use Closure;
use rmrevin\yii\fontawesome\FA;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * Class Menu
 * Theme menu widget.
 */
class Menu extends \yii\widgets\Menu
{

    /**
     * @var string 菜单数据里显示菜单名称的字段
     */
    public $labelField = 'label';

    /**
     * @var string 菜单数据里二级菜单的键名
     */
    public $submenuName = 'items';

    /**
     * @var string 菜单数据里显示菜单图标的字段
     */
    public $iconField = 'icon';

    /**
     * @var string 菜单数据里二级菜单的默认图标
     */
    public $submenuDefaultIcon = 'circle-o';

    public $linkTemplate = '<a href="{url}">{icon} {label}</a>';

    public $submenuTemplate = "\n<ul class='treeview-menu' {show}>\n{items}\n</ul>\n";

    public $activateParents = true;

    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        if (isset($item[$this->submenuName])) {
            $angleLeft = FA::i('angle-left', ['class' => 'pull-right']);
            $labelTemplate = '<a href="{url}">{label} ' . $angleLeft . '</a>';
            $linkTemplate = '<a href="{url}">{icon} {label} ' . $angleLeft . '</a>';
            $submenuDefaultIcon = FA::i($item[$this->iconField]);
        } else {
            $labelTemplate = $this->labelTemplate;
            $linkTemplate = $this->linkTemplate;
            $submenuDefaultIcon = FA::i($this->submenuDefaultIcon);
        }

        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $linkTemplate);
            $replace = [
                '{url}' => Html::encode(Url::to($item['url'])),
                '{label}' => $item[$this->labelField],
                '{icon}' => !empty($item[$this->iconField]) ? FA::i($item[$this->iconField]) : $submenuDefaultIcon,
            ];
        } else {
            $template = ArrayHelper::getValue($item, 'template', $labelTemplate);
            $replace = [
                '{label}' => $item[$this->labelField],
                '{icon}' => !empty($item[$this->iconField]) ? FA::i($item[$this->iconField]) : null,
            ];
        }

        return strtr($template, $replace);
    }

    /**
     * @inheritdoc
     */
    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if (isset($item[$this->submenuName])) {
                $class[] = 'treeview';
            }
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }
            $menu = $this->renderItem($item);
            if (!empty($item[$this->submenuName])) {
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $menu .= strtr($submenuTemplate, [
                    '{show}' => $item['active'] ? "style='display: block'" : '',
                    '{items}' => $this->renderItems($item[$this->submenuName]),
                ]);
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }

    /**
     * @inheritdoc
     */
    protected function normalizeItems($items, &$active)
    {
        foreach ($items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);
                continue;
            }
            // 处理url地址
            $this->parseUrl($items[$i], $item);
            if (!isset($item[$this->labelField])) {
                $item[$this->labelField] = '';
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $items[$i][$this->labelField] = $encodeLabel ? Html::encode($item[$this->labelField]) : $item[$this->labelField];
            $hasActiveChild = false;
            if (isset($item[$this->submenuName])) {
                $items[$i][$this->submenuName] = $this->normalizeItems($item[$this->submenuName], $hasActiveChild);
                if (empty($items[$i][$this->submenuName]) && $this->hideEmptyItems) {
                    unset($items[$i][$this->submenuName]);
                    if (!isset($item['url'])) {
                        unset($items[$i]);
                        continue;
                    }
                }
            }
            if (!isset($item['active'])) {
                if ($this->activateParents && $hasActiveChild || $this->activateItems && $this->isItemActive($item)) {
                    $active = $items[$i]['active'] = true;
                } else {
                    $items[$i]['active'] = false;
                }
            } elseif ($item['active'] instanceof Closure) {
                $active = $items[$i]['active'] = call_user_func($item['active'], $item, $hasActiveChild, $this->isItemActive($item), $this);
            } elseif ($item['active']) {
                $active = true;
            }
        }

        return array_values($items);
    }

    /**
     * 处理url路由地址
     * 以`/`开头的url地址为模块地址，则自动转换为数组格式
     *
     * @param array $items 菜单数据
     * @param array $item 菜单数据
     */
    protected function parseUrl(&$items, &$item)
    {
        if (isset($item['url'])) {
            $url = $item['url'];
            $items['url'] = $item['url'] = is_string($url) && strpos($url, '/') === 0 ? (array)$url : $url;
        }
    }

    /**
     * @inheritdoc
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = Yii::getAlias($item['url'][0]);
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                foreach (array_splice($item['url'], 1) as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
