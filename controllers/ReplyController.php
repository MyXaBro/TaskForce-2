<?php


namespace app\controllers;


use app\models\Replies;
use app\models\Statuses;
use app\models\Tasks;
use yii\web\NotFoundHttpException;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ReplyController extends SecuredController
{

    public function actionCreate($task)
    {
        $task = $this->findOrDie($task, Tasks::class);

        if (Yii::$app->request->isPost) {
            $reply = new Replies();
            $reply->load(Yii::$app->request->post());

            if ($reply->validate()) {
                $task->link('replies', $reply);
            }

            return $this->redirect(['tasks/view', 'id' => $task->id]);
        }
    }

    public function actionValidate($task)
    {
        $reply = new Replies;
        $reply->task_id = $task;
        $reply->user_id = $this->getUser()->getId();

        if (Yii::$app->request->isAjax && $reply->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($reply);
        }
    }

    public function actionDeny($id)
    {
        $reply = $this->findOrDie($id, Replies::class);

        $reply->is_denied = true;
        $reply->save();

        return $this->redirect(['tasks/view', 'id' => $reply->task_id]);
    }

    public function actionApprove($id)
    {
        $reply = $this->findOrDie($id, Replies::class);
        $task = $reply->task;

        $reply->is_approved = true;
        $reply->save();

        $task->performer_id = $reply->user_id;
        $task->status_id = Statuses::STATUS_IN_PROGRESS;
        $task->save();

        return $this->redirect(['tasks/view', 'id' => $reply->task_id]);
    }
}