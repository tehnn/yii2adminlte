<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-krajee-base
 * @version 1.6.0
 */

namespace kartik\base;

/**
 * Asset bundle for Html5Input Widget
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Html5InputAsset extends \kartik\base\AssetBundle
{
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/html5input']);
        parent::init();
    }
}
