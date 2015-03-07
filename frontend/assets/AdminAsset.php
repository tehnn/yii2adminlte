<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle {

    public $sourcePath = '@themes/admin-lte';
    public $baseUrl = '@web';
    public $css = array(
        //'admin-lte/css/bootstrap.min.css',
        'css/AdminLTE.css',
        'css/font-awesome.min.css',
        'css/ionicons.min.css'
    );
    public $js = array(
        'js/AdminLTE/app.js',
    );
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}

?>