<?php
?>
<div id="accordion<?php
 echo '_'.$prefix.'_'.$templateKey.'_'.implode('', explode('.', $categorie['element']['path'])).'_'.$cId; ?>" class="accordion medcard-accordion">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapse<?php
			echo '_'.$prefix.'_'.$templateKey.'_'.implode('', explode('.', $categorie['element']['path'])).'_'.$cId; ?>" data-parent="#accordion<?php echo '_'.$prefix.'_'.$templateKey.'_'.implode('', explode('.', $categorie['element']['path'])).'_'.$cId; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['element']['name']; ?>
                <?php 
                 if ((count($categorie)==1) && ($categorie["element"]["element_id"]==-1)) {
                ?>
                    (пустая категория)
                <?php
                } else {
                ?>
                     <div class ="accordeonToggleAlt">(Свернуть)</div>
                <?php
                }
				?>
			</a>
        </div>
        <?php if ((count($categorie)==1) && ($categorie["element"]["element_id"] == -1)) { ?>
        <div class="accordion-body collapse" id="collapse<?php
			/*echo $prefix.'_'.$templateKey.'_'.$categorie['element']['path'].'_'.$cId;*/
			echo '_'.$prefix.'_'.$templateKey.'_'.implode('', explode('.', $categorie['element']['path'])).'_'.$cId;
			?>">
        <?php } else { ?>
            <div class="accordion-body in" id="collapse<?php 
			/*echo $prefix.'_'.$templateKey.'_'.$categorie['element']['path'].'_'.$cId;*/
			echo '_'.$prefix.'_'.$templateKey.'_'.implode('', explode('.', $categorie['element']['path'])).'_'.$cId;
			?>">
        <?php } ?>
            <div class="accordion-inner">
        <?php
//Перебираем элементы. Смотрим - если элемент 
			//   в массиве - категория, то рекурсивно 
			//    вызываем для него вывод категории
			// Иначе выводим его как элемент
                $nextWithNewRow = true;
                $counter = 0;
                foreach($categorie as $key => $child) {
                    $counter++;
					if ($key == 'element') {
                        continue;
                    }
					if ($child['element']['element_id'] == -1) {
						 $this->drawHistoryCategorie(
                            $child,
                            $key,
                            $form,
                            $model,
                            $prefix,
                            $templateKey,
                            $lettersInPixel
                         );
					}
					else {
						$element = $child['element'];
                   		if(isset($element['dependences'])) {
                        	?>
                        <script type="text/javascript">
                            elementsDependences.push({
                                'dependences' : <?php echo CJSON::encode($element['dependences']); ?>,
                                'elementId' : '<?php echo $element['id']; ?>'
                            });
                        </script>
                        <?php }
						?>
                        <?php if($nextWithNewRow) { ?>
                        <div class="form-group col-xs-12">
                        <?php }
                        echo $form->labelEx($model,'f'.$element['id'], array(
                            'class' => 'control-label label-before'
                        ));  ?>
                        <?php
                            if($element['type'] == 0 || $element['type'] == 5 || $element['type'] == 6) {
                                $options = array(
                                    'id' => 'f_'.$prefix.'_'.$element['id'],
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'disabled' => 'disabled'
                                );
                                if($element['type'] == 6 && trim($element['value']) != '') {
                                    $options['value'] = implode('.', array_reverse(explode('-', $element['value'])));
                                } else {
                                    $options['value'] = $element['value'];
                                }

                                if(isset($element['size']) && $element['size'] != null) {
                                    $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                } else {
                                    $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                }

                                echo $form->textField($model,'f'.$element['id'], $options);

                                if($element['label_after'] != null) {
                                ?>
                                    <label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label>
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
                                } else {
                                    $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                }

                                echo $form->textArea($model,'f'.$element['id'], $options);

                                if($element['label_after'] != null) {
                                    ?>
                                    <label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label>
                                <?php
                                }
                            } elseif($element['type'] == 2) {
                                if (isset($element['selected']['']))
                                {
                                    $element['guide'][''] = 'Не выбрано';
                                }
                                $options = array(
                                    'id' => 'f_'.$prefix.'_'.$element['id'],
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'options' => $element['selected'],
                                    'disabled' => 'disabled'
                                );
                                if(isset($element['size']) && $element['size'] != null) {
                                    $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                }  else {
                                    $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                }

                                echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
                                if($element['label_after'] != null) {
                                    ?>
                                    <label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label>
                                <?php
                                }
                            } elseif($element['type'] == 3) {
                                $options = array(
                                    'id' => 'f_'.$prefix.'_'.$element['id'],
                                    'class' => 'form-control',
                                    'placeholder' => '',
                                    'multiple' => 'multiple',
                                    'disabled' => 'disabled'
                                );

                                if(isset($element['size']) && $element['size'] != null) {
                                    $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                }  else {
                                    $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                }

                                if (isset($element['guide'])) {
                                    if ($element['guide'] == null) {
                                        $vals = array (-1 => "Не выбрано");
                                        echo $form->dropDownList($model,'f'.$element['id'], $vals , $options);
                                    }
                                    else {
                                        echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
                                    }
                                }
                                else {
                                    $vals = array (-1 => "Не выбрано");
                                    echo $form->dropDownList($model,'f'.$element['id'], $vals , $options);
                                }

                                if($element['label_after'] != null) {
                                ?>
                                    <label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label>
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
                            }
                            if(!$element['is_wrapped'] && $counter < count($categorie)) {
                                $nextWithNewRow = false;
                            } else {
                                $nextWithNewRow = true;
                                ?>
                            </div>
                            <?php }
                            }
                        }
                ?>
            </div>
        </div>
    </div>
</div>