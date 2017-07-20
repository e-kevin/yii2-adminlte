# yii2-adminlte
AdminLTE theme

包含组件
 * [Box|Panel]-widget 
 * Smallbox-widget
 * FlashAlert-widget
 * Callout-widget
 * Infobox-widget
 * Timeline widget 
 	- 效果图:http://almsaeedstudio.com/themes/AdminLTE/pages/UI/timeline.html
 	- 使用方法请查看[Timeline.md](https://github.com/Wonail/yii2-adminlte/blob/master/widgets/Timeline.md)文件

查看更多有关AdminLTE主题的部件
* http://almsaeedstudio.com/themes/AdminLTE/pages/widgets.html
* http://almsaeedstudio.com/themes/AdminLTE/pages/UI/general.html

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist wonail/yii2-adminlte "*"
```

or add

```
"wonail/yii2-adminlte": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

### Skins

默认的，AdminLTE主题扩展使用蓝色（skin-blue）主题，你也可以在配置文件里自定义其他颜色，如下：

```php
'components' => [
    'assetManager' => [
        'bundles' => [
            'wonail\adminlte\AdminLteAsset' => [
                'skin' => 'skin-black',
            ],
        ],
    ],
],
```

当自定义了其他颜色，你可以使用`AdminLteHelper`助手类读取配置值，如下：
```html
<body class="<?= \wonail\adminlte\AdminLteHelper::skinClass() ?>">
```

**Note:** 只有通过配置文件自定义主题颜色才可以使用该方法读取主题颜色值，否则无法获取到相关主题颜色

以下是可用的主题颜色：

```
"skin-blue",
"skin-black",
"skin-red",
"skin-yellow",
"skin-purple",
"skin-green",
"skin-blue-light",
"skin-black-light",
"skin-red-light",
"skin-yellow-light",
"skin-purple-light",
"skin-green-light"
```

### FlashAlert
```php
<?php
    Yii::$app->session->setFlash('success', 'The extension is installed!');
    Yii::$app->session->setFlash('error', ['error1', 'error2']); // 支持数组
    echo \wonail\adminlte\widgets\FlashAlert::widget();
?>
```

### Panel
```php
<?=
\wonail\adminlte\widgets\Box::widget([
    'header' => 'Panel widget',
    'body' => 'This is a panel widget.',
    'isPanel' => true,
]);
?>
```

### Box
```php
<?=
\wonail\adminlte\widgets\Box::widget([
    'header' => 'Box widget',
    'body' => 'This is a box widget.',
]);
?>
```

### Callout
```php
<?=
\wonail\adminlte\widgets\Callout::widget([
    'type'=>\wonail\adminlte\AdminLTE::TYPE_WARNING,
    'body'=>'This is a callout widget.'
]);?>
```

### SmallBox
```php
<?=
\wonail\adminlte\widgets\SmallBox::widget([
    'bgColor'=>\wonail\adminlte\AdminLTE::BG_PURPLE,
    'header'=>'90%',
    'description'=>'Free Space',
    'icon'=>'cloud-download',
    'linkLabel'=>'查看更多 <i class="fa fa-arrow-circle-right"></i>',
    'linkRoute'=>'#'
]);?>
```

### InfoBox
```php
<?=
\wonail\adminlte\widgets\InfoBox::widget([
    'bgColor' => \wonail\adminlte\AdminLTE::BG_AQUA,
    'number' => 100500,
    'header' => 'InfoBox widget',
    'icon' => 'bolt',
    'progress' => 66,
    'progressDescription' => 'Something about this'
])
?>
```
