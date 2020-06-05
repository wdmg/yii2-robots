<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\robots\models\Rules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="robots-rule-form">
    <?php $form = ActiveForm::begin([
        'id' => "addNewRule",
        'enableAjaxValidation' => true
    ]); ?>

    <?= $form->field($model, 'robot')->textInput(['placeholder' => "Enter robot User-agent..."]) ?>

    <?= $form->field($model, 'mode')->widget(SelectInput::class, [
        'items' => $model->getModesList(),
        'options' => [
            'id' => 'robots-form-mode',
            'class' => 'form-control'
        ]
    ])->label(Yii::t('app/modules/robots','Directive')); ?>

    <?= $form->field($model, 'rule')->textInput([
        'placeholder' => "Enter rule path or value...",
        'autocomplete' => 'off'
    ]) ?>

    <?= $form->field($model, 'status')->widget(SelectInput::class, [
        'items' => $model->getStatusesList(),
        'options' => [
            'id' => 'robots-form-status',
            'class' => 'form-control'
        ]
    ]); ?>
    <div class="row">
        <div class="modal-footer">
            <?= Html::a(Yii::t('app/modules/robots', 'Close'), "#", [
                'class' => 'btn btn-default pull-left',
                'data-dismiss' => 'modal'
            ]) ?>
            <?= Html::submitButton(Yii::t('app/modules/robots', 'Save'), [
                'class' => 'btn btn-save btn-success pull-right'
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>