<?php

namespace app\controllers;

use app\controllers\SecuredController;
use app\helpers\UIHelper;
use app\models\Categories;
use app\models\Files;
use app\models\Opinions;
use app\models\Replies;
use app\models\Tasks;
use app\src\logic\actions\CancelAction;
use app\src\logic\actions\DenyAction;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class TasksController extends SecuredController
{
    /**
     * Функция устанавливает свойствам модели - выбранные в форме параметры
     * @return string
     */
    public function actionIndex():string
    {
        //Создает новый экземпляр модели Tasks
        $task = new Tasks();
        //Загружает данные из POST-запроса в этот экземпляр модели, используя метод load()
        $task->load(Yii::$app->request->post());

        //Получает запрос на выборку задач с помощью метода getSearchQuery(), который возвращает объект ActiveQuery
        $tasksQuery = $task->getSearchQuery();
        /*Получает список всех категорий задач с помощью метода find() модели Categories, который также возвращает объект ActiveQuery,
        и вызывает метод all(), чтобы получить массив объектов Category*/
        $categories = Categories::find()->all();

        $countQuery = clone $tasksQuery;
        //Создает новый экземпляр Pagination, указывая количество элементов на странице равным 5
        // и общее количество элементов равным количеству задач,
        // полученных в результате выполнения запроса на выборку.
        $pages = new Pagination(['totalCount' => $countQuery-> count(), 'pageSize' => 5]);
        //Выполняет запрос на выборку задач с использованием методов offset(), limit() и all() объекта ActiveQuery.
        $models = $tasksQuery->offset($pages->offset)->limit($pages->limit)->all();

        //Возвращает результат в виде рендера представления index, передавая список задач, пагинацию,
        // экземпляр модели Tasks и список категорий в качестве параметров рендера.
        return $this->render('index',['models' => $models, 'pages' => $pages, 'task' => $task, 'categories' => $categories]);
    }

    public function actionCreate()
    {
        $task = new Tasks();
        $categories = Categories::find()->all();

        if (!Yii::$app->session->has('task_uid')) {
            Yii::$app->session->set('task_uid', uniqid('upload'));
        }

        if (Yii::$app->request->isPost) {
            $task->load(Yii::$app->request->post());
            $task->uid = Yii::$app->session->get('task_uid');
            $task->save();

            if ($task->id) {
                Yii::$app->session->remove('task_uid');
                return $this->redirect(['tasks/view', 'id' => $task->id]);
            }
        }

        return $this->render('create', ['model' => $task, 'categories' => $categories]);
    }

    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {
            $model = new Files();
            $model->task_uid = Yii::$app->session->get('task_uid');
            $model->file = UploadedFile::getInstanceByName('file');

            $model->upload();

            return $this->asJson($model->getAttributes());
        }
    }


    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $task = $this->findOrDie($id, Tasks::class);
        $reply = new Replies;
        $opinion = new Opinions;

        return $this->render('view', ['model' => $task, 'newReply' => $reply, 'opinion' => $opinion]);
    }

    public function actionCancel($id)
    {
        /**
         * @var Tasks $task
         */
        $task = $this->findOrDie($id, Tasks::class);
        $task->goToNextStatus(new CancelAction);

        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }

    public function actionDeny($id)
    {
        /**
         * @var Tasks $task
         */
        $task = $this->findOrDie($id, Tasks::class);
        $task->goToNextStatus(new DenyAction());

        $performer = $task->performer;
        $performer->increaseFailCount();

        return $this->redirect(['tasks/view', 'id' => $task->id]);
    }

    public function actionMy($status = null)
    {
        $menuItems = UIHelper::getMyTasksMenu($this->getUser()->is_contractor);

        if (!$status) {
            $this->redirect($menuItems[0]['url']);
        }

        $tasks = $this->getUser()->getTasksByStatus($status)->all();

        return $this->render('my', ['menuItems' => $menuItems, 'tasks' => $tasks]);
    }
}