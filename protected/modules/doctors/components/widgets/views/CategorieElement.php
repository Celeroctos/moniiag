<?php
if(isset($categorie['id'])) {
?>
<div id="accordion<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>" class="accordion">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapse<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>" data-parent="#accordion<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?>
                <?php if(count($categorie['elements']) == 0 && ((isset($categorie['children']) && count($categorie['children']) == 0) || !isset($categorie['children']))) { ?>
                    (пустая категория)
                <?php }
                else
                {
                	?> <div class ="accordeonToggleAlt"> (Свернуть)</div> <?php	
                }	
				
			 ?>
            </a>
            <?php if(($categorie['is_dynamic'] == 1 || isset($categorie['pr_key'])) && false ) { ?>
            <button class="btn btn-default btn-sm accordion-clone-btn" type="button">
                <span class="glyphicon glyphicon-plus"></span>
                <span class="no-display pr-key"><?php echo $categorie['pr_key']; ?></span>
            </button>
            <? } ?>
        </div>
         <?php if(count($categorie['elements']) == 0 && ((isset($categorie['children']) && count($categorie['children']) == 0) || !isset($categorie['children']))) { ?>
            <div class="accordion-body collapse" id="collapse<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>">
        <?php } else { ?>
            <div class="accordion-body in" id="collapse<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>">
        <?php } ?>
        <!--<div class="accordion-body collapse" id="collapse--><?php /* echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; */?><!--">-->
            <div class="accordion-inner">
                <?php // Подкатегории
                foreach($categorie['childrenElementsOrder'] as $item)
                {
					if ($item['arrayNumber']=='1')
					{
						// Выводим категорию
                        $this->drawCategorie(
                        	$categorie['children'][$item['numberInArray']], 
                        	$form,
                        	$model,
                        	$lettersInPixel,
                        	$templatePrefix
                        );
					}
					else
					{
						$element = $categorie['elements'][$item['numberInArray']];
						// Выведем зависимости, если они есть
						if(isset($element['dependences'])) {
                        ?>
	                        <script type="text/javascript">
	                            globalVariables.elementsDependences.push({
	                                'path' : '<?php echo $element['path']; ?>',
	                                'dependences' : <?php echo CJSON::encode($element['dependences']); ?>,
	                                'elementId' : '<?php echo $element['id']; ?>'
	                            });
	                        </script>
                        <?php } 
                        // Выводим сам элемент
                       ?>
                        <div class="form-group">
                        	<!-- Выводим метку элемента -->
                            <div class="col-xs-3">
                                <?php
                                // Добавляем звёздочку к метке, если элемент обязателен для заполнения
                                if ($element["is_required"]==1)
                                {
                                    $model->setAttributeLabels('f'.$element['undotted_path'].'_'.$element['id'],
                                        $model->attributeLabels['f'.$element['undotted_path'].'_'.$element['id']].
                                        " <span class=\"required\">*</span>");
                                }   
                               ?>
                                <?php echo $form->labelEx($model,'f'.$element['undotted_path'].'_'.$element['id'], array(
                                    'class' => 'col-xs-12 control-label'
                                )); ?>
                            </div>
                           	<!-- Выводим сам элемент -->
                            <div class="col-xs-9">
                        
                        	<?php
                       	 if($element['type'] == 0) {
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => ''
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    echo $form->textField($model,'f'.$element['undotted_path'].'_'.$element['id'], $options);
                                    if($element['label_after'] != null) {
                                    ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                } elseif($element['type'] == 1) {
                                    $options =  array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => ''
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    echo $form->textArea($model,'f'.$element['undotted_path'].'_'.$element['id'], $options);
                                    if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                } elseif($element['type'] == 2) {
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'options' => $element['selected']
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        <div class="col-xs-10 no-padding-left">
                                        <?php
                                    }
                                    // Добавим пустое значение к выпадающему списку
                                    $element['guide'][""] = 'Не выбрано';
                                    echo $form->dropDownList($model,'f'.$element['undotted_path'].'_'.$element['id'], $element['guide'], $options);
                                    if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        </div>
                                        <?php
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        <button type="button" id="ba<?php echo '_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'];  ?>" class="btn btn-default btn-sm">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                        <?php
                                    }
                                } elseif($element['type'] == 3) {
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'options' => $element['selected'],
                                        'multiple' => 'multiple'
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        <div class="col-xs-10  no-padding-left">
                                    <?php
                                    }
                                    echo $form->dropDownList($model,'f'.$element['undotted_path'].'_'.$element['id'], $element['guide'], $options);
                                    if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        </div>
                                    <?php
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        <button type="button" id="ba<?php echo '_'.$prefix.'_'.$element['guide_id']; ?>" class="btn btn-default btn-sm">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                    <?php
                                    }
                                } 
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

                                            for($i = 0; $i < $element['config']['numRows']; $i++) {
                                            ?>
                                            <tr>
                                                <?php if(isset($element['config']['rows'][$i])) {
                                                   ?>
                                                    <td><?php echo $element['config']['rows'][$i]; ?></td>
                                                   <?php
                                                } ?>
                                                <?php
                                                for($j = 0; $j < $element['config']['numCols']; $j++) {
													// Вывод значений по умолчанию
													$cellDefaultVal = '';	
                                                	if (isset($element['config']['values']))
                                                	{
                                                		if (isset($element['config']['values'][$i."_".$j]))
                                                		{
                                                			$cellDefaultVal = $element['config']['values'][(string)$i."_".(string)$j ];
														}	
                                                	}
                                                    if($canEditMedcard)
													{
														?>
														<td class="content-<?php echo $i.'_'.$j; ?>"><?php 
														echo $cellDefaultVal; 
														?></td>
														<?php
													} else 
													{ 
														?>
														<td><?php
														echo $cellDefaultVal; 

														?></td>
														<?php
													}
                                                } ?>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                    );
                                    echo $form->hiddenField($model,'f'.$element['undotted_path'].'_'.$element['id'], $options);
                                    ?>
                                <?php
                                }?>
                   
                       
                       		</div>
                       	</div>
                       	<? 
                     }
                }      
                ?>    
            </div>
        </div>
    </div>
</div>    
<? } ?>