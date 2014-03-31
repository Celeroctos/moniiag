<?php
?>
<div id="accordion<?php echo '_'.$prefix.'_'.$templateKey.'_'.$categorie['element']['path'].'_'.$cId; ?>" class="accordion">
       <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapse<?php echo $prefix.'_'.$templateKey.'_'.$categorie['element']['path'].'_'.$cId;; ?>" data-parent="#accordion<?php echo '_'.$prefix.'_'.$templateKey.'_'.$categorie['element']['path'].'_'.$cId; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['element']['name']; ?>
                <?php 
                 if ((count($categorie)==1) && ($categorie["element"]["element_id"]==-1)
                 )         
                 {
                 ?>
                    (пустая категория)
                <?php } ?>
            </a>
        </div>
                <?php if ((count($categorie)==1) && ($categorie["element"]["element_id"]==-1)
                 )    
                 { ?>
            <div class="accordion-body collapse" id="collapse<?php echo $prefix.'_'.$templateKey.'_'.$categorie['element']['path'].'_'.$cId; ?>">
        <?php } else { ?>
            <div class="accordion-body in" id="collapse<?php echo $prefix.'_'.$templateKey.'_'.$categorie['element']['path'].'_'.$cId; ?>">
        <?php } ?>
        <div class="accordion-inner">
        <?php
//Перебираем элементы. Смотрим - если элемент 
			//   в массиве - категория, то рекурсивно 
			//    вызываем для него вывод категории
			// Иначе выводим его как элемент
                foreach($categorie as $key => $child) 
				{	
					if ($key=='element') continue;		
					if ($child['element']['element_id']==-1)
					{
						 $this->drawHistoryCategorie(
	                        		$child, 
	                        		$key,
	                        		$form, 
	                        		$model, 
	                        		$prefix, 
	                        		$templateKey, 
	                        		$lettersInPixel);			
					}
					else
					{		
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
                        <div class="form-group">
                            <div class="col-xs-3">
                                <?php 
                                echo $form->labelEx($model,'f'.$element['id'], array(
                                    'class' => 'col-xs-12 control-label'
                                ));  ?>
                            </div>
                            <div class="col-xs-9">
                            <!-- -->
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
                                    //var_dump($element['selected']);
                                    //exit();
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
                                    }
                                 
                                   // var_dump($options);
                                    //exit();
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
									
									if (isset($element['guide']))
									{
										if ($element['guide']==NULL)
										{
											?><!--<label class="control-label">--><?php /*echo ("Не выбрано");*/?><!--</label>--><?php	
											$vals = array (-1 => "Не выбрано");
											echo $form->dropDownList($model,'f'.$element['id'], $vals , $options);
										}
										else
										{
											echo $form->dropDownList($model,'f'.$element['id'], $element['guide'], $options);
										}
									}
									else
									{
										$vals = array (-1 => "Не выбрано");
										echo $form->dropDownList($model,'f'.$element['id'], $vals , $options);
									}
									
									if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                }
                                
                                //----------------
                                elseif($element['type'] == 4) {
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
                                //----------------
                                 ?>
                            <!-- -->
                            </div>
                        </div>
						<?php			
					}
				}
        ?>
        </div>
        </div>
        </div>
</div>