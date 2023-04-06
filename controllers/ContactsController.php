<?php
namespace app\controllers;
use app\models\Contacts;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ContactsController extends Controller
{
    public $layout = 'common';

    public function init()
    {
        parent::init();
        Yii::$app->user->loginUrl = ['login/index'];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false,
                        'actions' => ['update'],
                        'matchCallback' => function($rule, $action){
                            $id = Yii::$app->request->get('id');
                            $contact = Contacts::findOne($id);

                            return $contact->owner_id != Yii::$app->user->getId();
                        }
                    ]
                ]
            ]
        ];
    }

    public function actionList()
    {
        $contacts = Contacts::find()->all();

        return $this->render('list', ['contacts' => $contacts]);
    }

    public function actionUpdate($id)
    {
        $contact = Contacts::findOne($id);

        if (!$contact) {
            throw new NotFoundHttpException("Контакт с ID #$id не найден");
        }

        return $this->render('update', ['contact' => $contact]);
    }
}