<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/engine/modules/hospital/widgets/medical_directions_form.js"></script>
<div id="accordionD" class="accordion col-xs-12" >
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapseD" data-parent="#accordionD" data-toggle="collapse" class="accordion-toggle" data-toggle="tooltip" data-placement="right" title="Здесь можно посмотреть направления пациента"><strong>Направления</strong></a>
        </div>
        <div class="accordion-body collapse in" id="collapseD">
            <div class="accordion-inner">
                <div class="directionsList overlayCont">
                    <ul class="cont">
                    </ul>
                    <div class="btns">
                        <button type="button" id="toHospitalizationBtn" class="btn btn-success">На госпитализацию</button>
                        <button type="button" id="toConsultationBtn" class="btn btn-success">На консультацию</button>
                    </div>
                </div>
                <div class="directionAdd no-display overlayCont">
                    <?php
                        $form = $this->beginWidget('CActiveForm', array(
                            'id' => 'add-direction-form',
                            'enableAjaxValidation' => true,
                            'enableClientValidation' => true,
                            'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/hospital/mdirections/add'),
                            'htmlOptions' => array(
                                'class' => 'form-horizontal col-xs-12',
                                'role' => 'form'
                            )
                        ));
                    ?>

                    <?php echo $form->hiddenField($model, 'omsId', array(
                        'id' => 'directionOmsId',
                        'value' => $currentOmsId
                    )); ?>
                    <?php echo $form->hiddenField($model, 'doctorId', array(
                        'value' => $currentDoctorId
                    )); ?>
                    <?php echo $form->hiddenField($model, 'cardNumber', array(
                        'value' => $currentMedcard
                    )); ?>
                    <div class="form-group col-xs-12">
                        <?php echo $form->labelEx($model,'type', array(
                            'class' => 'col-xs-5 control-label'
                        )); ?>
                        <div class="col-xs-7">
                            <?php echo $form->dropDownList($model, 'type', array('Обычная', 'Срочная'), array(
                                'class' => 'form-control'
                            )); ?>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <?php echo $form->labelEx($model,'isPregnant', array(
                            'class' => 'col-xs-5 control-label'
                        )); ?>
                        <div class="col-xs-7">
                            <?php echo $form->dropDownList($model, 'isPregnant', array('Нет', 'Да'), array(
                                'class' => 'form-control'
                            )); ?>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <?php echo $form->labelEx($model,'wardId', array(
                            'class' => 'col-xs-5 control-label'
                        )); ?>
                        <div class="col-xs-7">
                            <?php echo $form->dropDownList($model, 'wardId', $wardsList, array(
                                'class' => 'form-control'
                            )); ?>
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <?php echo $form->labelEx($model,'pregnantTerm', array(
                            'class' => 'col-xs-5 control-label'
                        )); ?>
                        <div class="col-xs-7">
                            <?php echo $form->textField($model, 'pregnantTerm', array(
                                'class' => 'form-control'
                            )); ?>
                        </div>
                    </div>
                    <div class="form-group btns">
                        <button type="button" id="directionAddSubmit" class="btn btn-success">ОК</button>
                        <button type="button" id="directionAddClose" class="btn btn-default">Закрыть</button>
                    </div>
                    <?php
                        $this->endWidget();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>