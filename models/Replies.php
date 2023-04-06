<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "replies".
 *
 * @property int $id
 * @property int $user_id
 * @property string $dt_add
 * @property string $description
 * @property int $task_id
 * @property int|null $is_approved
 * @property int $budget
 *
 * @property Tasks $task
 * @property Users $user
 */
class Replies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'replies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'budget'], 'required'],
            [['budget'], 'integer', 'min' => 1],
            [['description'], 'string', 'max' => 255],
            [['description'], 'unique', 'targetAttribute' => ['task_id', 'user_id'], 'message' => 'Вы уже оставляли отклик к этому заданию'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'dt_add' => 'Dt Add',
            'description' => 'Description',
            'task_id' => 'Task ID',
            'is_approved' => 'Is Approved',
            'budget' => 'Бюджет',
            'avatar' => 'Аватар'
        ];
    }


    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::class, ['id' => 'task_id']);
    }

    public function getIsHolded()
    {
        return $this->is_denied || $this->is_approved || $this->task->status_id == Statuses::STATUS_IN_PROGRESS;
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

}
