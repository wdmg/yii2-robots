<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $path string */
?>

<div class="robots-rule-view">
    <?php
        $webPath = Url::to($path, true);
        $checkFailtureMessage = Yii::t('app/modules/robots', 'Robots.txt file not exists or unavailable by URL: {url}', [
            'url' => Html::a($webPath, $webPath, [
                'target' => "_blank",
                'data-pjax' => "0"
            ])
        ]);
        $checkSuccessMessage = Yii::t('app/modules/robots', 'Robots.txt file exists and available by URL: {url}', [
            'url' => Html::a($webPath, $webPath, [
                'target' => "_blank",
                'data-pjax' => "0"
            ])
        ]);
    ?>
    <div id="robots-web-check"></div>
    <?= Html::textarea('robots-txt-source', $source, [
        'class' => 'form-control',
        'rows' => 12
    ]) ?>
</div>
<?php $this->registerJs(<<< JS
    $(function() {
        $.ajax({
            url:'$webPath',
            type:'HEAD',
            error: function() {
                $('#robots-web-check').append('<div class="alert alert-danger">$checkFailtureMessage</div>');
            },
            success: function() {
                $('#robots-web-check').append('<div class="alert alert-success">$checkSuccessMessage</div>');
            }
        });
    });
JS
); ?>