<div id="accordion<?php echo '_'.$prefix.'_'.$categorie['id']; ?>" class="accordion">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapse<?php echo $prefix.'_'.$categorie['id']; ?>" data-parent="#accordion<?php echo '_'.$prefix.'_'.$categorie['id']; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?></a>
        </div>
        <div class="accordion-body collapse" id="collapse<?php echo $prefix.'_'.$categorie['id']; ?>">
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
                                    'id' => 'f_'.$prefix.'_'.$element['id'],
                                    'class' => 'form-control',
                                    'placeholder' => ''
                                ));
                            } elseif($element['type'] == 1) {
                                echo $form->textArea($model,'f'.$element['id'], array(
                                    'id' => 'f_'.$prefix.'_'.$element['id'],
                                    'class' => 'form-control',
                                    'placeholder' => ''
                                ));
                            } elseif($element['type'] == 2) {
                                $options = array(
                                    'id' => 'f_'.$prefix.'_'.$element['id'],
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'options' => $element['selected']
                                );
                                if($element['allow_add']) {
                                    ?>
                                    <div class="col-xs-10 no-padding-left">
                                    <?php
                                }
                                echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
                                if($element['allow_add']) {
                                    ?>
                                    </div>
                                    <?php
                                }
                                if($element['allow_add']) {
                                    ?>
                                    <button type="button" id="ba<?php echo '_'.$prefix.'_'.$element['guide_id'];  ?>" class="btn btn-default btn-sm">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                    <?php
                                }
                            } elseif($element['type'] == 3) {
                                $options = array(
                                    'id' => 'f_'.$prefix.'_'.$element['id'],
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'options' => $element['selected'],
                                    'multiple' => 'multiple'
                                );
                                if($element['allow_add']) {
                                    ?>
                                    <div class="col-xs-10  no-padding-left">
                                <?php
                                }
                                echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
                                if($element['allow_add']) {
                                    ?>
                                    </div>
                                <?php
                                }
                                if($element['allow_add']) {
                                    ?>
                                    <button type="button" id="ba<?php echo '_'.$prefix.'_'.$element['guide_id']; ?>" class="btn btn-default btn-sm">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                <?php
                                }
                            } ?>
                        </div>
                    </div>
                <?php  } ?>
            </div>
        </div>
    </div>
</div>