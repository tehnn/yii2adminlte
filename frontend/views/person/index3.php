<?php

use yii\widgets\Pjax;
?>


<?php Pjax::begin(); ?>
<?php
echo \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
]);
?>
<?php Pjax::end(); ?>


