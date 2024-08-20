<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;


/** @var yii\web\View $this */
/** @var app\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Top';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => [Url::current()]
    ]) ?>
    
    <?php 
    echo Select2::widget([
        'name' => 'year',
        'data' => [
            2024=>2024,
            2023=>2023,
            2022=>2022
        ],
        'options' => [
            'placeholder' => 'Выбрать год ...',            
        ],
    ]); ?>
    
    <?php echo Html::submitButton("Смотреть"); ?>
    
    <?php ActiveForm::end(); ?>
    
    <ol>
    <?php foreach ($data as $item){
        echo "<li>".$item['fullname']." (".$item['countbooks'].")</li>";
    } ?>
    </ol>
</div>
