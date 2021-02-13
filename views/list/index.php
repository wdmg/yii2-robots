<?php

use wdmg\widgets\SelectInput;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel wdmg\votes\models\VotesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $this->context->module->name;
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="list-index">
    <?php Pjax::begin([
        'id' => "robotsRulesAjax",
        'timeout' => 5000
    ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'robot',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'robot',
                    'items' => $searchModel->getRobotsList(true),
                    'options' => [
                        'id' => 'robots-robot',
                        'class' => 'form-control'
                    ]
                ]),
            ],
            [
                'attribute' => 'mode',
                'format' => 'html',
                'label' => Yii::t('app/modules/robots','Directive'),
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'mode',
                    'items' => $searchModel->getModesList(true),
                    'options' => [
                        'id' => 'robots-mode',
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {

                    if ($data->mode == $data::RULE_MODE_ALLOW)
                        return '<span class="label label-success">'.Yii::t('app/modules/robots','Allow').'</span>';
                    elseif ($data->mode == $data::RULE_MODE_DISALLOW)
                        return '<span class="label label-danger">'.Yii::t('app/modules/robots','Disallow').'</span>';
                    elseif ($data->mode == $data::RULE_MODE_CLEAN)
                        return '<span class="label label-warning">'.Yii::t('app/modules/robots','Clean-Param').'</span>';
                    elseif ($data->mode == $data::RULE_MODE_HOST)
                        return '<span class="label label-primary">'.Yii::t('app/modules/robots','Host').'</span>';
                    elseif ($data->mode == $data::RULE_MODE_DELAY)
                        return '<span class="label label-info">'.Yii::t('app/modules/robots','Crawl-delay').'</span>';
                    elseif ($data->mode == $data::RULE_MODE_SITEMAP)
                        return '<span class="label label-default">'.Yii::t('app/modules/robots','Sitemap').'</span>';
                    else
                        return $data->mode;

                }
            ],
            'rule',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'id' => 'robots-status',
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->status == $data::RULE_STATUS_ACTIVE) {
                        return '<div id="switcher-' . $data->id . '" data-value-current="' . $data->status . '" data-id="' . $data->id . '" data-toggle="button-switcher" class="btn-group btn-toggle"><button data-value="0" class="btn btn-xs btn-default">OFF</button><button data-value="1" class="btn btn-xs btn-primary">ON</button></div>';
                    } else {
                        return '<div id="switcher-' . $data->id . '" data-value-current="' . $data->status . '" data-id="' . $data->id . '" data-toggle="button-switcher" class="btn-group btn-toggle"><button data-value="0" class="btn btn-xs btn-danger">OFF</button><button data-value="1" class="btn btn-xs btn-default">ON</button></div>';
                    }
                }
            ],
            [
                'attribute' => 'created',
                'label' => Yii::t('app/modules/robots','Created'),
                'format' => 'html',
                'contentOptions' => [
                    'class' => 'text-center',
                    'style' => 'min-width:146px'
                ],
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->createdBy) {
                        $output = Html::a($user->username, ['users/view', 'id' => $user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->created_by) {
                        $output = $data->created_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->created_at, 'datetime');
                    return $output;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/robots', 'Actions'),
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'visibleButtons' => [
                    'update' => true,
                    'delete' => true,
                    'view' => false,
                ],
                'buttons'=> [
                    'update' => function($url, $data, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Url::toRoute(['list/update', 'id' => $data['id']]), [
                                'title' => Yii::t('app/modules/robots', 'Update rule'),
                                'class' => 'update-rule',
                                'data-toggle' => 'updateRuleForm',
                                'data-id' => $key,
                                'data-pjax' => '1'
                            ]);
                    },
                ]
            ],
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => 'prev',
            'nextPageCssClass' => 'next',
            'firstPageCssClass' => 'first',
            'lastPageCssClass' => 'last',
            'firstPageLabel' => Yii::t('app/modules/robots', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/robots', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/robots', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/robots', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div>
        <div class="btn-group">
            <?= Html::a(Yii::t('app/modules/robots', 'Re-generate'), ['generate'], [
                'class' => 'btn btn-warning',
                'data-pjax' => '0'
            ]) ?>
            <?= Html::a(Yii::t('app/modules/robots', 'View robots.txt'), ['view'], [
                'class' => 'btn btn-info view-robots',
                'data-toggle' => 'modal',
                'data-target' => '#viewRobotsTxt',
                'data-pjax' => '1'
            ]) ?>
        </div>
        <?= Html::a(Yii::t('app/modules/robots', 'Add new rule'), ['create'], [
            'class' => 'btn btn-add btn-success add-rule pull-right',
            'data-toggle' => 'modal',
            'data-target' => '#addRuleForm',
            'data-pjax' => '1'
        ]) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>

<?php $this->registerJs(
    'var $container = $("#robotsRulesAjax");
    var requestURL = window.location.href;
    if ($container.length > 0) {
        $container.delegate(\'[data-toggle="button-switcher"] button\', \'click\', function() {
            var id = $(this).parent(\'.btn-group\').data(\'id\');
            var value = $(this).data(\'value\');
            let url = new URL(requestURL);
            url.searchParams.set(\'change\', \'status\');            
            $.ajax({
                type: "POST",
                url: url.toString(),
                dataType: \'json\',
                data: {\'id\': id, \'value\': value},
                complete: function(data) {
                    $.pjax.reload({type:\'POST\', container:\'#robotsRulesAjax\'});
                }
            });
        });
    }
    ', \yii\web\View::POS_READY
); ?>

<?php $this->registerJs(<<< JS
    $('body').delegate('.add-rule', 'click', function(event) {
        event.preventDefault();
        $.get(
            $(this).attr('href'),
            function (data) {
                $('#addRuleForm .modal-body').html(data);
                $('#addRuleForm').modal();
            }
        );
    });
    $('body').delegate('.update-rule', 'click', function(event) {
        event.preventDefault();
        $.get(
            $(this).attr('href'),
            function (data) {
                $('#updateRuleForm .modal-body').html(data);
                $('#updateRuleForm').modal();
            }
        );
    });
    $('body').delegate('.view-robots', 'click', function(event) {
        event.preventDefault();
        $.get(
            $(this).attr('href'),
            function (data) {
                $('#viewRobotsTxt .modal-body').html(data);
                $('#viewRobotsTxt').modal();
            }
        );
    });
JS
); ?>

<?php Modal::begin([
    'id' => 'addRuleForm',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/robots', 'Add new rule').'</h4>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'updateRuleForm',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/robots', 'Update rule').'</h4>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'id' => 'viewRobotsTxt',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/robots', 'View robots.txt').'</h4>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php Modal::end(); ?>
