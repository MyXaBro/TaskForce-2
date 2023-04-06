<?php


namespace app\assets;


use yii\web\AssetBundle;

class YandexAsset extends AssetBundle
{
    public $sourcePath = null;

    public function init()
    {
        parent::init();

        $api_key = getenv('YANDEX_API_KEY');
        $this->js[] = "https://api-maps.yandex.ru/2.1/?apikey=$api_key&lang=ru_RU";
    }

}