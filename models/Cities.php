<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "cities".
 *
 * @property string|null $city
 * @property float|null $lat
 * @property float|null $longest
 */
class Cities extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['lat', 'long'], 'string', 'max' => 16],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city' => 'Город',
            'lat' => 'Lat',
            'longest' => 'Longest',
        ];
    }
}
