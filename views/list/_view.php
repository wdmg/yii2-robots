<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $source string */
?>

<div class="robots-rule-view">
    <?= Html::textarea('robots-txt-source', $source, [
        'class' => 'form-control',
        'rows' => 12
    ]) ?>
</div>