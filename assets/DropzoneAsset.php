<?php

namespace app\assets;

use yii\web\AssetBundle;

class DropzoneAsset extends AssetBundle
{
    public $sourcePath = '@vendor/enyo/dropzone/dist';

    public $css = [
        'basic.css',
        'dropzone.css',
    ];
    public $js = [
        'dropzone.js'
    ];
}
