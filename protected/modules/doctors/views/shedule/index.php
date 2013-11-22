<div class="row">
    <div class="col-xs-5">
        <h5><strong>Список пациентов на <span class="text-danger">23.05.2013</span></strong></h5>
        <?php
        $filterForm = $this->beginWidget('CActiveForm', array(
            'id' => 'change-date-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/view'),
            'htmlOptions' => array(
                'class' => 'form-horizontal col-xs-12',
                'role' => 'form'
            )
        ));
        ?>
            <div class="form-group">
                <?php echo $filterForm->labelEx($filterModel,'date', array(
                    'class' => 'col-xs-3 control-label'
                )); ?>
                <div class="col-xs-6 input-group date" id="date-cont">
                    <?php echo $filterForm->textField($filterModel,'date', array(
                        'id' => 'filterDate',
                        'class' => 'form-control',
                        'placeholder' => 'Выберите дату',
                    )); ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <?php echo CHtml::submitButton(
                        'Показать',
                        array('class' => 'btn btn-success')
                      );
            ?>
        <?php $this->endWidget(); ?>
        <div class="row">
            <div class="col-xs-12 borderedBox">
                <table id="omsSearchWithCardResult" class="table table-condensed table-hover">
                    <thead>
                    <tr class="header">
                        <td>
                            ФИО
                        </td>
                        <td>
                            Время приёма
                        </td>
                        <td>
                            Посмотреть карту
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $key => $patient) { ?>
                        <tr>
                            <td>
                                <?php echo CHtml::link($patient['fio'], array('/doctors/shedule/view?cardid='.$patient['medcard_id'])); ?>
                            </td>
                            <td>
                                <?php echo $patient['patient_time']; ?>
                            </td>
                            <td>
                                <?php echo CHtml::link('<span class="glyphicon glyphicon-edit"></span>', array('/reception/patient/editcardview/?cardid='.$patient['medcard_id'])); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php if($currentPatient !== false) { ?>
    <div class="col-xs-7">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'shedule-edit-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => true,
            'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/editpatient'),
            'htmlOptions' => array(
                'class' => 'form-horizontal col-xs-12',
                'role' => 'form'
            )
        ));
        echo $form->hiddenField($model,'medcardId', array(
            'id' => 'medcardId',
            'class' => 'form-control',
            'value' => $currentPatient
        ));
        ?>
        <div id="myAccordion" class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a href="#collapseOne" data-parent="#myAccordion" data-toggle="collapse" class="accordion-toggle">Реквизитная информация</a>
                </div>
                <div class="accordion-body collapse" id="collapseOne">
                    <div class="accordion-inner">
                        <p>HTML stands for HyperText Markup Language. HTML is the main markup language for describing the structure of Web pages. <a href="http://www.tutorialrepublic.com/html-tutorial/" target="_blank">Learn more.</a></p>
                    </div>
                </div>
            </div>
        </div>
        <?php foreach($categories  as $index => $template) {
                foreach($template  as $key => $categorie) {
        ?>
        <div id="myAccordion2" class="accordion">
            <div class="accordion-group">
                <div class="accordion-heading">
                    <a href="#collapse<?php echo $categorie['id']; ?>" data-parent="#myAccordion" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?></a>
                </div>
                <div class="accordion-body collapse in" id="collapse<?php echo $categorie['id']; ?>">
                    <div class="accordion-inner">
                        <?php foreach($categorie['elements'] as $element) { ?>
                            <div class="form-group">
                                <div class="col-xs-3">
                                    <?php echo $form->labelEx($model,'f'.$element['id'], array(
                                        'class' => 'col-xs-12 control-label'
                                    )); ?>
                                </div>
                                <div class="col-xs-9">
                                    <?php
                                    if($element['type'] == 0) {
                                        echo $form->textField($model,'f'.$element['id'], array(
                                            'id' => 'f'.$element['id'],
                                            'class' => 'form-control',
                                            'placeholder' => ''
                                        ));
                                    } elseif($element['type'] == 1) {
                                        echo $form->textArea($model,'f'.$element['id'], array(
                                            'id' => 'f'.$element['id'],
                                            'class' => 'form-control',
                                            'placeholder' => ''
                                        ));
                                    } elseif($element['type'] == 2) {
                                        echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], array(
                                            'id' => 'f'.$element['id'],
                                            'class' => 'form-control',
                                            'placeholder' => '',
                                            'options' => $element['selected']
                                        ));
                                    } elseif($element['type'] == 3) {
                                        echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], array(
                                            'id' => 'f'.$element['id'],
                                            'class' => 'form-control',
                                            'placeholder' => '',
                                            'multiple' => 'multiple',
                                            'options' => $element['selected']
                                        ));
                                    } ?>
                                </div>
                            </div>
                        <?php  } ?>
                    </div>
                </div>
            </div>
        </div>
            <?php } ?>
        <?php } ?>
        <div class="form-group submitEditPatient">
            <?php echo CHtml::ajaxSubmitButton(
                'Сохранить',
                CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/doctors/shedule/patientedit'),
                array(
                    'success' => 'function(data, textStatus, jqXHR) {

                            }'
                ),
                array(
                    'class' => 'btn btn-primary'
                )
            ); ?>
        </div>
    <?php $this->endWidget(); ?>
    </div>
    <?php } ?>
</div>