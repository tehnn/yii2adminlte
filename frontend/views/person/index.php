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


    <?php //echo $this->render('_search', ['model' => $searchModel]);    ?>

    <?php
    $columns = [
        ['class' => 'yii\grid\SerialColumn'],
        //'id',
        'prename',
        'fname',
        [
            'attribute' => 'lname',
            'value' => function ($data) {
                return md5($data->lname);
            },
        ]
        ,
        'mtype',
        ['class' => 'yii\grid\ActionColumn'],
    ];
    echo \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
        'pjax' => true,
        'responsive' => true,
        'hover' => true,
        //'floatHeader' => true,
        //'floatHeaderOptions' => ['scrollingTop' => '50'],
        'panel' => [
            'before' => '',
        //'after'=>''
        ],
        'toolbar' => [
            ['content' =>
                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'], ['class' => 'btn btn-success'])
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
