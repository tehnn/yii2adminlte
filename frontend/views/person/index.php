<?php

//utehn phnu
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'People';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-index">


    <?php //echo $this->render('_search', ['model' => $searchModel]);     ?>

    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        //'id',
        'prename',
        [
            'attribute' => 'fname',
            'format' => 'raw',
            'value' => function($data) {
                return Html::a(Html::encode($data->fname), array('view', 'id' => $data->id));
            }
                ],
                [
                    'attribute' => 'lname',
                    'value' => function ($data) {
                        return md5($data->lname);
                    },
                ]
                ,
                'mtype',
                ['class' => '\kartik\grid\ActionColumn'],
            ];
            echo \kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $columns,
                'pjax' => true,
                'pjaxSettings' => [
                    //'neverTimeout' => true,
                    'options' => [
                        'enablePushState' => false,
                    ],
                ],
                'responsive' => true,
                'hover' => true,
                'panel' => [
                    'before' => '',
                //'after'=>''
                ],
                'toolbar' => [
                    ['content' =>
                    
                        Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-flat btn-success'])
                    ],
                    '{export}',
                ],
                'exportConfig' => [
                    \kartik\grid\GridView::EXCEL => ['label' => 'Excel'],
                    \kartik\grid\GridView::CSV => ['label' => 'CSV'],
                    \kartik\grid\GridView::PDF => ['label' => 'PDF'],
                ],
            ]);
            ?>

</div>
