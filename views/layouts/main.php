<?php

use yii\helpers\Html;
use wonail\adminlte\assetBundle\AdminLteAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AdminLteAsset::register($this);

$isPjax = Yii::$app->getRequest()->getIsPjax();
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<?php if (!$isPjax) : //pjax:START ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?php
    $this->registerMetaTag([
        'charset' => Yii::$app->charset
    ]);
    $this->registerMetaTag([
        'http-equiv' => 'X-UA-Compatible',
        'content' => 'IE=edge'
    ]);
    $this->registerMetaTag([
        'name' => 'viewport',
        'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
    ]);
    $this->registerMetaTag([
        'name' => 'description',
        'content' => 'wonail/yii2-adminlte',
    ], 'description');
    $this->registerMetaTag([
        'name' => 'keywords',
        'content' => 'adminlte theme, wonail',
    ], 'keywords');
    echo Html::csrfMetaTags();
    echo Html::tag('title', Html::encode($this->title));
    ?>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php $this->head() ?>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<?php $this->beginBody() ?>
<div class="wrapper">

    <?= $this->render(
        'header.php',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?= $this->render(
        'left.php',
        ['directoryAsset' => $directoryAsset]
    ) ?>

    <?php endif; //pjax:end ?>

    <?= $this->render(
        'content.php',
        ['content' => $content]
    ) ?>

    <?php if (!$isPjax) : //pjax:START ?>

    <?= \wonail\scrolltop\ScrollTop::widget() ?>

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> <?= Yii::$app->params['app.version'] ?>
        </div>
        <strong>Copyright &copy; <?= Yii::$app->params['app.copyright'] . ' ' . Yii::$app->params['app.name'] ?>.</strong>
    </footer>

    <?= $this->render(
        'control_sidebar.php'
    ) ?>

</div>

<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
<?php endif; //pjax:end ?>