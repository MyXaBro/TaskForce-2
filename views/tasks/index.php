<?php
/**
 * @var Tasks[] $models
 * @var $this View
 * @var $task
 * @var $categories
 * @var Pagination $pages
 */

use models\Categories;
use models\Tasks;
use yii\data\Pagination;
use yii\helpers\BaseStringHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;


$this->title = 'Просмотр новых заданий';
?>

<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>
    <?php foreach ($models as $model): ?>
        <?=$this->render('//partials/_task', ['model' => $model]); ?>
    <?php endforeach; ?>
    <div class="pagination-wrapper">
        <?=LinkPager::widget([
            'pagination' => $pages,
            'prevPageCssClass' => 'pagination-item mark',
            'nextPageCssClass' => 'pagination-item mark',
            'pageCssClass' => 'pagination-item',
            'activePageCssClass' => 'pagination-item--active',
            'linkOptions' => ['class' => 'link link--page'],
            'nextPageLabel' => '',
            'prevPageLabel' => '',
            'maxButtonCount' => 5
        ]); ?>
    </div>
</div>
<div class="right-column">
    <div class="right-card black">
        <div class="search-form">
            <?php $form = ActiveForm::begin(); ?>
            <h5 class="head-card">Категории</h5>
            <div class="checkbox-wrapper">
                <!-- cоздает набор чекбоксов на основе ассоциативного массива данных,
                переданных в качестве второго аргумента, и привязывает их к свойству модели-->
                <?= Html::activeCheckboxList($task, 'category_id', array_column($categories, 'name', 'id'),
                    ['tag' => 'div', 'itemOptions' => ['labelOptions' => ['class' => 'control-label']]]); ?>
            </div>
            <h5 class="head-card">Дополнительно</h5>
            <div class="checkbox-wrapper">
                <!--генерирует разметку для поля модели и создает соответствующий элемент формы-->
                <?=$form->field($task, 'noResponses')->checkbox(['labelOptions' => ['class' => 'control-label']]); ?>
            </div>
            <div class="checkbox-wrapper">
                <?=$form->field($task, 'noLocation')->checkbox(['labelOptions' => ['class' => 'control-label']]); ?></div>
            <h5 class="head-card">Период</h5>
            <!-- cоздает выпадающий список для выбора периода -->
            <?=$form->field($task, 'filterPeriod', ['template' => '{input}'])->dropDownList([
                '3600' => 'За последний час', '86400' => 'За сутки', '604800' => 'За неделю'
            ], ['prompt' => 'Выбрать']); ?>
            <input type="submit" class="button button--blue" value="Искать">
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
