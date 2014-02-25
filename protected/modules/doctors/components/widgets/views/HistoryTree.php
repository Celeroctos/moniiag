<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'patient-edit-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/editpatient'),
    'htmlOptions' => array(
        'class' => 'form-horizontal col-xs-12',
        'role' => 'form'
    )
));
foreach($dividedCats as $key => $template) {
    ?>
    <h4>
        <?php echo $template['name']; ?>
    </h4>
    <?php
    foreach($template['cats']  as $index => $categorie) {
        $this->drawHistoryCategorie($categorie, $index, $form, $model, 'h', $key);
    }
}
$this->endWidget();
?>