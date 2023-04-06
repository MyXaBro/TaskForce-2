<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "categories".
 *
 * @property string|null $name
 * @property string|null $icon
 */
class Categories extends ActiveRecord
{
    /**
     * @return string;
     */
    public static function tableName():string
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'icon' => 'Иконка',
        ];
    }

}
