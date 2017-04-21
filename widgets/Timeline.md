Timeline Widget
==========================
用法
-----

简单测试用例

```php
<?=
\wonail\adminlte\widgets\Timeline::widget([
    'timeLabelBgColor' => \wonail\adminlte\AdminLTE::BG_PURPLE,
    'items' => [
        '1381767094' => [
            Yii::createObject([
                'class' => \wonail\adminlte\widgets\TimelineItem::className(),
                'time' => 1381767094,
                'header' => 'SOME HEADER',
                'body' => 'Well, i`m informative body',
                'icon' => 'beer',
                'iconBgColor' => 'orange'
            ]),
            Yii::createObject([
                'class' => \wonail\adminlte\widgets\TimelineItem::className(),
                'time' => 1381767098,
                'header' => 'SOME HEADER',
                'icon' => 'beer',
                'iconBgColor' => 'green'
            ])
        ],
        '1400880100' => [
            Yii::createObject([
                'class' => \wonail\adminlte\widgets\TimelineItem::className(),
                'time' => 1400880100,
                'body' => 'Well, i`m informative body',
                'icon' => 'cloud',
                'iconBgColor' => \wonail\adminlte\AdminLTE::BG_BLUE
            ]),
        ],
    ],
])
?>
```

生成TimeLine测试数据

```php
<?php
$timeline_items = [];
for ($i = 0; $i < 5; $i++) {
    $time = (time() - mt_rand(3600, 3600 * 24 * 7 * 30 * 5));
    $objcnt = mt_rand(1, 6);
    $events = [];
    for ($j = 0; $j < $objcnt; $j++) {
        $isFoot = mt_rand(0, 1);
        $footer = 'something in foot ' . $i . '_' . $j;
        $obj = Yii::createObject([
            'class' => \wonail\adminlte\widgets\ExampleTimelineItem::className(),
            'time' => $time - mt_rand(0, 3600 * 11),
            'header' => 'HEADER NUMBER ' . $i . '_' . $j,
            'body' => 'Well, i`m informative body ' . $i . '_' . $j,
            'type' => mt_rand(0, 1),
            'footer' => $isFoot ? $footer : ''
        ]);
        $events[] = $obj;
    }
    $timeline_items[$time] = $events;
}

echo \wonail\adminlte\widgets\Timeline::widget([
    'timeLabelBgColor' => function ($data) {
        $d = date('j', $data);
        if ($d <= 10) {
            return \wonail\adminlte\AdminLTE::BG_FUCHSIA;
        } elseif ($d <= 20) {
            return \wonail\adminlte\AdminLTE::BG_MAROON;
        } else {
            return \wonail\adminlte\AdminLTE::BG_PURPLE;
        }
    },
    'items' => $timeline_items,
]);

```