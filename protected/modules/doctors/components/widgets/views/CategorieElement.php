<div id="accordion<?php echo $categorie['id']; ?>" class="accordion">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapse<?php echo $categorie['id']; ?>" data-parent="#accordion<?php echo $categorie['id']; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?></a>
        </div>
        <div class="accordion-body collapse" id="collapse<?php echo $categorie['id']; ?>">
            <div class="accordion-inner">
                <?php // Подкатегории
                if(isset($categorie['children'])) {
                    foreach($categorie['children'] as $key => $childCategorie) {
                        $this->drawCategorie($childCategorie, $form, $model);
                    }
                }
                ?>
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