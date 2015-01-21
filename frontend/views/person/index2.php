<?php

use yii\widgets\Pjax;
?>
<div class="alert alert-success"><?= $sql ?></div>

<?php Pjax::begin(); ?>
<?php
echo \kartik\grid\GridView::widget([
    'dataProvider' => $dataProvider,
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
]);
?>
<?php Pjax::end(); ?>


