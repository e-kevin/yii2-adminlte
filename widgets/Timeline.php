<?php

namespace wonail\adminlte\widgets;

use wonail\adminlte\AdminLTE;
use rmrevin\yii\fontawesome\component\Icon;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;

class Timeline extends Widget
{

    /**
     * @var array array of events
     * @example
     *  'items' => [
     *     '07.10.2014' => [array of TimelineItems ],
     *     'some object' => [array of TimelineItems ],
     *     '15.11.2014' => [array of TimelineItems ],
     *     'some object' => [array of TimelineItems ],
     *  ]
     *
     */
    public $items = [];

    /**
     * @var string|\Closure
     * @example
     * 'timeLabelBgColor' => function($data) {
     *      if (is_string($data)) {
     *          return wocenter\adminlte\AdminLTE::BG_BLUE;
     *      } elseif($data->type==1) {
     *          return wocenter\adminlte\AdminLTE::BG_LBLUE;
     *      } else {
     *         return wocenter\adminlte\AdminLTE::BG_TEAL;
     *      }
     * }
     * 
     */
    public $timeLabelBgColor = AdminLTE::BG_GREEN;
    public $timeLabelFormat = 'm-d, Y';
    public $headerTimeFormat = 'H:i:s';

    public function init()
    {
        $this->timeLabelFormat = 'php:' . $this->timeLabelFormat;
        $this->headerTimeFormat = 'php:' . $this->headerTimeFormat;
    }

    public function run()
    {
        if (empty($this->items)) {
            return;
        }
        echo Html::tag('ul', $this->renderItems(), ['class' => 'timeline']);
    }

    protected function renderItems()
    {
        $res = '';
        foreach ($this->items as $data => $events) {
            if (!empty($events)) {
                $res .= $this->renderTimeLabel($data);
                foreach ($events as $event) {
                    $res .= $this->renderEvent($event);
                }
            }
        }
        return $res;
    }

    protected function renderTimeLabel($data)
    {
        $parsedDatetime = Yii::$app->formatter->asDate($data, $this->timeLabelFormat);
        if (is_callable($this->timeLabelBgColor)) {
            $this->timeLabelBgColor = call_user_func($this->timeLabelBgColor, $data);
        }
        return Html::tag('li', Html::tag('span', $parsedDatetime, ['class' => $this->timeLabelBgColor]), ['class' => 'time-label']);
    }

    protected function renderEvent($event)
    {
        $res = '';
        if ($event instanceof TimelineItem) {
            $res .= new Icon($event->icon, ['class' => $event->iconBgColor]);
            $item = '';
            if ($event->time) {
                $parsedDatetime = Yii::$app->formatter->asTime($event->time, $this->headerTimeFormat);
                $item .= Html::tag('span', new Icon('clock-o') . ' ' . $parsedDatetime, ['class' => 'time']);
            }
            if ($event->header) {
                $item .= Html::tag('h3', $event->header, ['class' => 'timeline-header ' . (!$event->body && !$event->footer ? 'no-border' : '')]);
            }
            $item .= Html::tag('div', $event->body, ['class' => 'timeline-body']);
            if ($event->footer) {
                $item .= Html::tag('div', $event->footer, ['class' => 'timeline-footer']);
            }
            $res .= Html::tag('div', $item, ['class' => 'timeline-item']);
        } else {
            throw new InvalidConfigException('event must be instanceof TimelineItem');
        }

        return Html::tag('li', $res);
    }

}
