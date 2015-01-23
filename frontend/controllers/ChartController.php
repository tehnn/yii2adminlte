<?php
namespace frontend\controllers;
use Yii;


class ChartController extends \yii\web\Controller {
    
    public function actionChart1(){
        return $this->render('chart1');
    }
}
