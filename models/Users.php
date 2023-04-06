<?php

namespace app\models;

use app\models\Categories;
use app\models\Cities;
use app\models\Files;
use app\models\Opinions;
use app\models\Replies;
use app\models\BaseUser;
use app\models\Statuses;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property int $city_id
 * @property int $fail_count
 * @property string $password
 * @property string $avatar
 * @property string $dt_add
 * @property string $bd_date
 * @property string $phone
 * @property string $tg
 * @property string $description
 * @property boolean $hide_contacts
 * @property boolean $is_contractor
 *
 * @property Files[] $File
 * @property Opinions[] $opinions
 * @property Replies[] $Reply
 * @property Categories[] $categories
 * @property Cities $city
 */
class Users extends BaseUser implements IdentityInterface
{
    public $password_repeat;

    public $old_password;
    public $new_password;
    public $new_password_repeat;

    /**
     * @var UploadedFile
     */
    public $avatarFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    public function behaviors()
    {
        return [
            'saveRelations' => [
                'class'     => SaveRelationsBehavior::className(),
                'relations' => [
                    'categories'
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'name'], 'required'],
            [['city_id', 'password'], 'required', 'on' => 'insert'],
            [['new_password'], 'required', 'when' => function ($model) {
                return $model->old_password;
            }],
            [['password_repeat', 'categories', 'old_password', 'new_password', 'new_password_repeat'], 'safe'],
            [['avatarFile'], 'file', 'mimeTypes' => ['image/jpeg', 'image/png'], 'extensions' => ['png', 'jpg', 'jpeg']],
            [['password'], 'compare', 'on' => 'insert'],
            [['new_password'], 'compare', 'on' => 'update'],
            [['bd_date'], 'date', 'format' => 'php:Y-m-d',],
            [['is_contractor', 'hide_contacts'], 'boolean'],
            [['phone'], 'match', 'pattern' => '/^[+-]?\d{11}$/', 'message' => 'Номер телефона должен быть строкой в 11 символов'],
            [['email', 'name'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['phone'], 'number'],
            [['password', 'tg'], 'string', 'max' => 64],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::class, 'targetAttribute' => ['city_id' => 'id']],

            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name' => 'Имя',
            'city_id' => 'Город',
            'categories' => 'Выбранные категории',
            'password' => 'Пароль',
            'old_password' => 'Старый пароль',
            'new_password' => 'Новый пароль',
            'password_repeat' => 'Повтор пароля',
            'new_password_repeat' => 'Повтор пароля',
            'hide_contacts' => 'Показывать контакты только заказчику',
            'dt_add' => 'Dt Add',
            'bd_date' => 'Дата рождения',
            'phone' => 'Номер телефона',
            'description' => 'Информация о себе',
            'tg' => 'Telegram',
            'is_contractor' => 'я собираюсь откликаться на заказы'
        ];
    }

    public function isBusy()
    {
        return $this->getAssignedTasks()->joinWith('status', true, 'INNER JOIN')->where(['statuses.id' => Statuses::STATUS_IN_PROGRESS])->exists();
    }

    public function isContactsAllowed(IdentityInterface $user)
    {
        $result = true;

        if ($this->hide_contacts) {
            $result = $this->getAssignedTasks($user)->exists();
        }

        return $result;
    }

    public function getRating()
    {
        $rating = null;

        $opinionsCount = $this->getOpinions()->count();

        if ($opinionsCount) {
            $ratingSum = $this->getOpinions()->sum('rate');
            $failCount = $this->fail_count;
            $rating = round(intdiv($ratingSum, $opinionsCount + $failCount), 2);
        }

        return $rating;
    }

    public function getRatingPosition()
    {
        $result = null;

        $sql = "SELECT u.id, (SUM(o.rate) / (COUNT(o.id) + u.fail_count)) as rate FROM users u
                LEFT JOIN opinions o on u.id = o.performer_id
                GROUP BY u.id
                ORDER BY rate DESC";

        $records = Yii::$app->db->createCommand($sql)->queryAll(\PDO::FETCH_ASSOC);
        $index = array_search($this->id, array_column($records, 'id'));

        if ($index !== false) {
            $result = $index + 1;
        }

        return $result;
    }

    public function getAge()
    {
        $result = null;

        if ($this->bd_date) {
            $bd = new \DateTime($this->bd_date);
            $now = new \DateTime();
            $diff = $now->diff($bd);
            $result = $diff->y;
        }

        return $result;
    }

    /**
     * Gets query for [[File]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasMany(Files::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Opinion0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOpinions()
    {
        return $this->hasMany(Opinions::class, ['performer_id' => 'id']);
    }

    /**
     * Gets query for [[Reply]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReplies()
    {
        return $this->hasMany(Replies::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Categories::class, ['id' => 'category_id'])->viaTable('user_categories', ['user_id' => 'id']);
    }


    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::class, ['id' => 'city_id']);
    }

    public function getTasksByStatus($status)
    {
        $query = Tasks::find();
        $query->joinWith('performer p')->joinWith('customer c');

        switch ($status) {
            case 'new':
                $query->where(['status_id' => Statuses::STATUS_NEW]);
                break;
            case 'close':
                $query->where(['status_id' => [Statuses::STATUS_COMPLETE, Statuses::STATUS_FAIL, Statuses::STATUS_CANCEL]]);
                break;
            case 'in_progress':
                $query->where(['status_id' => Statuses::STATUS_IN_PROGRESS]);
                break;
            case 'expired':
                $query->where(['status_id' => Statuses::STATUS_IN_PROGRESS])
                    ->andWhere(['<', 'expire_dt', date('Y-m-d')]);
                break;
        }

        $tb = $this->is_contractor ? 'p' : 'c';
        $query->andWhere("$tb.id = :user_id", [':user_id' => $this->id]);

        return $query;
    }
}
