<?php

namespace app\models;

use \yii\db\ActiveQuery;
/**
 * This is the ActiveQuery class for [[Cities]].
 *
 * @see Cities
 */
class CitiesQuery extends ActiveQuery
{
    public function active()
    {
        return $this->andWhere('[[status]]=1');
    }

    /**
     * {@inheritdoc}
     * @return Cities[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Cities|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
