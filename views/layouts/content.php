<?php

use wonail\adminlte\widgets\FlashAlert;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\Breadcrumbs;

?>
<div class="content-wrapper" id="content-wrapper" data-title="<?= Html::encode($this->title) ?>">
    <section class="content-header">
        <h1>
            <?php
            if ($this->title !== null) {
                echo Html::encode($this->title);
                echo isset($this->params['breadcrumb_description']) ? '&nbsp;&nbsp;<small>' . Html::encode($this->params['breadcrumb_description']) . '</small>' : '';
            } else {
                echo Inflector::camel2words(
                    Inflector::id2camel($this->context->module->id)
                );
                echo ($this->context->module->id !== Yii::$app->id) ? '&nbsp;&nbsp;<small>Module</small>' : '';
            }
            ?>
        </h1>

        <?=
        Breadcrumbs::widget([
            'homeLink' =>
                [
                    'label' => FA::i(FA::_DASHBOARD) . Yii::t('yii', 'Home'),
                    'url' => Yii::$app->homeUrl,
                    'encode' => false,
                ],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ])
        ?>
    </section>

    <section class="content">
        <?= FlashAlert::widget() ?>
        <?= $content ?>
    </section>
</div>