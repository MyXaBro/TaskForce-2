<?php


namespace app\controllers;


use app\controllers\SecuredController;
use app\models\Users;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserController extends SecuredController
{

    public function actionView($id): string
    {
        $user = $this->findOrDie($id, Users::class);

        if (!$user->is_contractor) throw new NotFoundHttpException('Пользователь не найден');

        return $this->render('view', ['user' => $user]);
    }

    public function actionSettings()
    {
        /**
         * @var Users $user
         */
        $user = $this->getUser();
        $user->setScenario('update');

        if (\Yii::$app->request->isPost) {
            $user->load(\Yii::$app->request->post());
            $user->avatarFile = UploadedFile::getInstance($user, 'avatarFile');

            if ($user->save()) {
                return $this->redirect(['user/view', 'id' => $user->id]);
            }
        }

        return $this->render('settings', ['user' => $this->getUser()]);

    }
}