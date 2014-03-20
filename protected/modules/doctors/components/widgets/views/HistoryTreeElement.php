<div id="accordion<?php echo '_'.$prefix.'_'.$templateKey.'_'.$categorie['path'].'_'.$cId; ?>" class="accordion">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapse<?php echo $prefix.'_'.$templateKey.'_'.$categorie['path'].'_'.$cId;; ?>" data-parent="#accordion<?php echo '_'.$prefix.'_'.$templateKey.'_'.$categorie['path'].'_'.$cId; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?>
                <?php if(count($categorie['elements']) == 0 && ((isset($categorie['children']) && count($categorie['children']) == 0) || !isset($categorie['children']))) { ?>
                    (пустая категория)
                <?php } ?>
            </a>
        </div>
        <?php if(count($categorie['elements']) == 0 && ((isset($categorie['children']) && count($categorie['children']) == 0) || !isset($categorie['children']))) { ?>
            <div class="accordion-body collapse" id="collapse<?php echo $prefix.'_'.$templateKey.'_'.$categorie['path'].'_'.$cId; ?>">
        <?php } else { ?>
            <div class="accordion-body in" id="collapse<?php echo $prefix.'_'.$templateKey.'_'.$categorie['path'].'_'.$cId; ?>">
        <?php } ?>
            <div class="accordion-inner">
                <?php // Подкатегории
                if(isset($categorie['children']) && count($categorie['children']) > 0) {
                    foreach($categorie['children'] as $key => $childCategorie) {
                        $this->drawHistoryCategorie($childCategorie, $key, $form, $model, $prefix, $templateKey, $lettersInPixel);
                    }
                }

                if(count($categorie['elements']) > 0) {
                    foreach($categorie['elements'] as $element) {
                        if(isset($element['dependences'])) {
                        ?>
                        <script type="text/javascript">
                            elementsDependences.push({
                                'dependences' : <?php echo CJSON::encode($element['dependences']); ?>,
                                'elementId' : '<?php echo $element['id']; ?>'
                            });
                        </script>
                        <?php } ?>
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
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    }
                                    echo $form->textField($model,'f'.$element['id'], $options);
                                    if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                } elseif($element['type'] == 1) {
                                    $options =  array(
                                        'id' => 'f_'.$prefix.'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'disabled' => 'disabled',
                                        'value' => $element['value']
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    }
                                    echo $form->textArea($model,'f'.$element['id'], $options);
                                    if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                } elseif($element['type'] == 2) {
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'options' => $element['selected'],
                                        'disabled' => 'disabled'
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    }
                                    echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
                                    if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                } elseif($element['type'] == 3) {
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                     //   'options' => $element['selected'],
                                        'multiple' => 'multiple',
                                        'disabled' => 'disabled'
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    }
                                    echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
                                    if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                } elseif($element['type'] == 4) {
                                    ?>
                                    <table class="controltable">
                                        <tbody>
                                        <?php if(isset($element['config']['cols']) && count($element['config']['cols']) > 0) {
                                            ?>
                                            <tr>
                                                <?php if(isset($element['config']['rows']) && count($element['config']['rows']) > 0) {
                                                    ?>
                                                    <td></td>
                                                <?php
                                                }
                                                for($i = 0; $i < count($element['config']['cols']); $i++) {
                                                    ?>
                                                    <td>
                                                        <?php echo $element['config']['cols'][$i]; ?>
                                                    </td>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        <?php
                                        }
                                        $valuesArr = CJSON::decode($element['value']);
                                        for($i = 0; $i < $element['config']['numRows']; $i++) {
                                            ?>
                                            <tr>
                                                <?php if(isset($element['config']['rows'][$i])) {
                                                    ?>
                                                    <td><?php echo $element['config']['rows'][$i]; ?></td>
                                                <?php
                                                } ?>
                                                <?php
                                                for($j = 0; $j < $element['config']['numCols']; $j++) { ?>
                                                    <td class="content-<?php echo $i.'_'.$j; ?>">
                                                        <?php
                                                            if(isset($valuesArr[$i][$j])) {
                                                                echo $valuesArr[$i][$j];
                                                            }
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['id'],
                                        'value' => $element['value']
                                    );
                                    echo $form->hiddenField($model,'f'.$element['id'], $options);
                                    ?>
                                <?php
                                }?>
                            </div>
                        </div>
                    <?php  } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>