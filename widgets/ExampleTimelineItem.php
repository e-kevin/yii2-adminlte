<?php

namespace wonail\adminlte\widgets;

use wonail\adminlte\AdminLTE;

class ExampleTimelineItem extends TimelineItem
{

    public $type = '';

    public function init()
    {
        if (!$this->icon) {
            $this->setIconClass();
        }
        if (!$this->iconBgColor) {
            $this->setIconBg();
        }
        $this->setTime();
    }

    public function setTime()
    {
        $this->time = date('H:i:s', $this->time);
    }

    public function setIconClass()
    {
        $this->icon = $this->type == 1 ? 'bomb' : 'cloud';
    }

    public function setIconBg()
    {
        $m = date('n', $this->time);
        if ($m == 12 or ( $m >= 1 && $m < 3)) {
            $this->iconBgColor = AdminLTE::BG_AQUA;
        } elseif ($m <= 3 && $m < 6) {
            $this->iconBgColor = AdminLTE::BG_LIME;
        } elseif ($m <= 6 && $m < 9) {
            $this->iconBgColor = AdminLTE::BG_GREEN;
        } else {
            $this->iconBgColor = AdminLTE::BG_ORANGE;
        }
    }

}
