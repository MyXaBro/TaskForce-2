<?php


namespace app\controllers;

use app\models\Opinions;
use app\models\Tasks;
use app\src\logic\actions\CompleteAction;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

class OpinionController extends SecuredController
{
    public function actionCreate($task)
    {
        /**
         * @var Tasks $task
         */
        $task = $this->findOrDie($task, Tasks::class);
        $opinion = new Opinions();

        if (Yii::$app->request->isPost) {
            $opinion->load(Yii::$app->request->post());
            $opinion->performer_id = $task->performer_id;

            if ($opinion->validate()) {
                $task->link('opinions', $opinion);
                $task->goToNextStatus(new CompleteAction);
            }
        }

        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }

    public function actionValidate()
    {
        $opinion = new Opinions();

        if (Yii::$app->request->isAjax && $opinion->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($opinion);
        }
    }

}