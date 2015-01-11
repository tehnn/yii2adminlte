
<div class="alert alert-success"><?=$sql?></div>
<!-- ที่  View -->
<?php echo \yii\grid\GridView::widget([
  'dataProvider' => $dataProvider,
  
 
]); ?>


