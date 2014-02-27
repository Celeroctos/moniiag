<div id="accordion<?php echo '_'.$prefix.'_'.$templateKey.'_'.$cId; ?>" class="accordion">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapse<?php echo $prefix.'_'.$templateKey.'_'.$cId;; ?>" data-parent="#accordion<?php echo '_'.$prefix.'_'.$templateKey.'_'.$cId; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?>
                <?php if(count($categorie['elements']) == 0 && ((isset($categorie['children']) && count($categorie['children']) == 0) || !isset($categorie['children']))) { ?>
                    (пустая категория)
                <?php } ?>
            </a>
        </div>
        <?php if(count($categorie['elements']) == 0 && ((isset($categorie['children']) && count($categorie['children']) == 0) || !isset($categorie['children']))) { ?>
            <div class="accordion-body collapse" id="collapse<?php echo $prefix.'_'.$templateKey.'_'.$cId; ?>">
        <?php } else { ?>
            <div class="accordion-body" id="collapse<?php echo $prefix.'_'.$templateKey.'_'.$cId; ?>">
        <?php } ?>
            <div class="accordion-inner">
                <?php // Подкатегории
                if(isset($categorie['children']) && count($categorie['children']) > 0) {
                    foreach($categorie['children'] as $key => $childCategorie) {
                        $this->drawHistoryCategorie($childCategorie, $key, $form, $model, $prefix, $templateKey);
                    }
                }

                if(count($categorie['elements']) > 0) {
                    foreach($categorie['elements'] as $element) { ?>
                        <div class="form-group">
                            <div class="col-xs-3">
                                <?php
                                echo $form->labelEx($model,'f'.$element['id'], array(
                                    'class' => 'col-xs-12 control-label'
                                )); ?>
                            </div>
                            <div class="col-xs-9">
                                <?php
                                if($element['type'] == 0) {
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'value' => $element['value'],
                                        'disabled' => 'disabled'
                                    );
                                    echo $form->textField($model,'f'.$element['id'], $options);
                                } elseif($element['type'] == 1) {
                                    $options =  array(
                                        'id' => 'f_'.$prefix.'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'disabled' => 'disabled',
                                        'value' => $element['value']
                                    );
                                    echo $form->textArea($model,'f'.$element['id'], $options);
                                } elseif($element['type'] == 2) {
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'options' => $element['selected'],
                                        'disabled' => 'disabled'
                                    );

                                    echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
                                } elseif($element['type'] == 3) {
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'options' => $element['selected'],
                                        'multiple' => 'multiple',
                                        'disabled' => 'disabled'
                                    );

                                    echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
                                } ?>
                            </div>
                        </div>
                    <?php  } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>