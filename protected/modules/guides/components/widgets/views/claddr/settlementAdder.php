<div class="modal fade" id="addSettlementPopup">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Добавить населённый пункт</h4>
            </div>
            <?php
            $form = $this->beginWidget('CActiveForm', array(
                'focus' => array($model,'description'),
                'id' => 'settlement-add-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'action' => CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/cladr/settlementadd'),
                'htmlOptions' => array(
                    'class' => 'form-horizontal col-xs-12',
                    'role' => 'form'
                )
            ));
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <?php echo $form->labelEx($model,'name', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'name', array(
                                    'id' => 'name',
                                    'class' => 'form-control',
                                    'placeholder' => 'Название населённого пункта'
                                )); ?>
                            </div>
                        </div>
                        <?php
                        if ($printCodeField==1)
                        {
                            ?>
                            <div class="form-group">
                                <?php echo $form->labelEx($model,'codeCladr', array(
                                    'class' => 'col-xs-3 control-label'
                                )); ?>
                                <div class="col-xs-9">
                                    <?php echo $form->textField($model,'codeCladr', array(
                                        'id' => 'codeCladr',
                                        'class' => 'form-control',
                                        'placeholder' => 'Код'
                                    )); ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="form-group chooser" id="regionChooserForSettlement">
                            <?php echo $form->labelEx($model,'codeRegion', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeRegion', array(
                                    'id' => 'codeRegion',
                                    'class' => 'form-control',
                                    'placeholder' => 'Регион'
                                )); ?>
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                        <div class="form-group chooser" id="districtChooserForSettlement">
                            <?php echo $form->labelEx($model,'codeDistrict', array(
                                'class' => 'col-xs-3 control-label'
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model,'codeDistrict', array(
                                    'id' => 'codeDistrict',
                                    'class' => 'form-control',
                                    'placeholder' => 'Район'
                                )); ?>
                                <ul class="variants no-display">
                                </ul>
                                <div class="choosed">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <?php echo CHtml::ajaxSubmitButton(
                    'Добавить',
                    CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/cladr/settlementadd'),
                    array(
                        'success' => 'function(data, textStatus, jqXHR) {
                                $("#settlement-add-form").trigger("success", [data, textStatus, jqXHR])
                        }',
                        'beforeSend' => 'function(jqXHR, settings) {
                             $("#settlement-add-form").trigger("beforesend", [settings, jqXHR])
                        }'
                    ),
                    array(
                        'class' => 'btn btn-primary'
                    )
                ); ?>
            </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>