<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle {

    public $sourcePath = '@theme/';
    public $baseUrl = '@web';
    public $css = array(
        //'admin-lte/css/bootstrap.min.css',
        'admin-lte/css/AdminLTE.css',
        'admin-lte/css/font-awesome.min.css',
        'admin-lte/css/ionicons.min.css'
    );
    public $js = array(
        'admin-lte/js/AdminLTE/app.js',
    );
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}

?>