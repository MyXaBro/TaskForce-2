<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property string $description
 * @property string|null $location
 * @property int|null $budget
 * @property string|null $expire_dt
 * @property string|null $dt_add
 * @property int $client_id
 * @property int|null $performer_id
 * @property int $status_id
 *
 * @property Categories $category
 * @property Files[] $files
 * @property Replies[] $replies
 * @property Statuses $status
 */
class Tasks extends ActiveRecord
{
    //свойство без откликов, не присутствует в бд, а только в форме
    public $noResponses;
    //свойство удалённой работы
    public $noLocation;
    //свойство периода фильтрации
    public $filterPeriod;
    /**
     * @var mixed|null
     */
    private mixed $user;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'client_id',
                'updatedByAttribute' => null
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status_id'], 'default', 'value' => function($model, $attr) {
                return Statuses::find()->select('id')->where('id=1')->scalar();
            }],
            [['city_id'], 'default', 'value' => function($model, $attr) {
                if ($model->location) {
                    return \Yii::$app->user->getIdentity()->city_id;
                }

                return null;
            }],
            [['noResponses', 'noLocation'], 'boolean'],
            [['filterPeriod'], 'number'],
            [['category_id', 'budget', 'performer_id', 'status_id', 'city_id'], 'integer'],
            [['budget'], 'integer', 'min' => 1],
            [['description'], 'string'],
            [['expire_dt'], 'date', 'format' => 'php:Y-m-d', 'min' => date('Y-m-d'), 'minString' => 'чем текущий день'],
            [['name', 'location'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::class, 'targetAttribute' => ['category_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Statuses::class, 'targetAttribute' => ['status_id' => 'id']],
            [['name', 'category_id', 'description', 'status_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'category_id' => 'Категория',
            'description' => 'Описание',
            'location' => 'Место',
            'budget' => 'Бюджет',
            'expire_dt' => 'Крайний срок',
            'dt_add' => 'Дата создания',
            'client_id' => 'Заказчик',
            'city_id' => 'Город',
            'performer_id' => 'Исполнитель',
            'status_id' => 'Статус',
            'task_uid' => 'Task_uid',
            'noLocation' => 'Удаленная работа',
            'noResponses' => 'Без откликов'
        ];
    }

    /**
     * Функция обработки поискового запроса
     * @return \yii\db\ActiveQuery
     */
    public function getSearchQuery(): \yii\db\ActiveQuery
    {
        $query = self::find();
        $query->where(['status_id' => Statuses::STATUS_NEW]);

        $query->andFilterWhere(['category_id' => $this->category_id]);

        if ($this->noLocation) {
            $query->andWhere('location IS NULL');
        }

        if ($this->noResponses) {
            $query->joinWith('replies r')->andWhere('r.id IS NULL');
        }

        if ($this->filterPeriod) {
            $query->andWhere('UNIX_TIMESTAMP(tasks.dt_add) > UNIX_TIMESTAMP() - :period', [':period' => $this->filterPeriod]);
        }

        return $query->orderBy('dt_add DESC');
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::class, ['id' => 'category_id']);
    }

    public function getPerformer()
    {
        return $this->hasOne(Users::class, ['id' => 'performer_id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Users::class, ['id' => 'client_id']);
    }

    public function getCity()
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery|FilesQuery
     */
    public function getFiles()
    {
        return $this->hasMany(Files::class, ['task_uid' => 'uid']);
    }

    /**
     * Gets query for [[Replies]].
     *
     */
    public function getReplies(IdentityInterface $user = null)
    {
        $allRepliesQuery = $this->hasMany(Replies::class, ['task_id' => 'id']);

        if ($user && $user->getId() !== $this->client_id) {
            $allRepliesQuery->where(['replies.user_id' => $user->getId()]);
        }

        return $allRepliesQuery;
    }

    /**
     * Gets query for [[Status]].
     *
     */
    public function getStatus()
    {
        return $this->hasOne(Statuses::class, ['id' => 'status_id']);
    }

    /**
     * Gets query for [[Opinion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpinions()
    {
        return $this->hasMany(Opinions::class, ['task_id' => 'id']);
    }
}
