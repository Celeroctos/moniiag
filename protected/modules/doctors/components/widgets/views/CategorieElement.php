<?php
if(isset($categorie['id'])) {
?>
<div id="accordion<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>" class="accordion medcard-accordion">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a href="#collapse<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>" data-parent="#accordion<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>" data-toggle="collapse" class="accordion-toggle"><?php echo $categorie['name']; ?>
                <?php if(count($categorie['elements']) == 0 && ((isset($categorie['children']) && count($categorie['children']) == 0) || !isset($categorie['children']))) { ?>
                    (пустая категория)
                <?php }
                else
                {
                	?> <div class ="accordeonToggleAlt"> <?php echo $categorie['config']['isWrapped'] == 1 ? '(Свернуть)' : '(Раскрыть)'; ?></div> <?php
                }	
				
			 ?>
            </a>
            <?php if($categorie['is_dynamic'] == 1 || isset($categorie['pr_key']))  { ?>
            <button class="btn btn-default btn-sm accordion-clone-btn" type="button">
                <span class="glyphicon glyphicon-plus"></span>
                <span class="no-display pr-key"><?php echo $categorie['pr_key']; ?></span>
            </button>
            <?php} ?>
        </div>
         <?php if(count($categorie['elements']) == 0 && ((isset($categorie['children']) && count($categorie['children']) == 0) || !isset($categorie['children']))) { ?>
            <div class="accordion-body collapse" id="collapse<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>">
        <?php } else {
            ?>
            <div class="accordion-body <?php echo isset($categorie['config']) && $categorie['config']['isWrapped'] == 1 ? 'in' : 'collapse'; ?>" id="collapse<?php echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; ?>">
        <?php } ?>
        <!--<div class="accordion-body collapse" id="collapse--><?php /* echo '_'.$templatePrefix.'_'.$prefix.'_'.$categorie['undotted_path'].'_'.$categorie['id']; */?><!--">-->
            <div class="accordion-inner">
                <?php // Подкатегории
                $nextWithNewRow = true;
                $counter = 0;
                foreach($categorie['childrenElementsOrder'] as $item) {
                    $counter++;
                    if ($item['arrayNumber'] == '1')
					{
						// Выводим категорию
                        $this->drawCategorie(
                        	$categorie['children'][$item['numberInArray']],
                        	$form,
                        	$model,
                        	$lettersInPixel,
                        	$templatePrefix
                        );
					} else {

						$element = $categorie['elements'][$item['numberInArray']];

                        //var_dump($model);
                        //exit();

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
                        <?php if($nextWithNewRow) { ?>
                        <div class="form-group col-xs-12">
                            <?php } ?>
                                <?php


                                // Заменяем пробелы на символ nbsp, чтобы они выводились
                                $model->setAttributeLabels('f'.$element['undotted_path'].'_'.$element['id'],
                                    str_replace(' ','&nbsp;',$model->attributeLabels['f'.$element['undotted_path'].'_'.$element['id']])
                                );

                                // Добавляем звёздочку к метке, если элемент обязателен для заполнения
                                if ($element["is_required"] == 1)
                                {
                                    $model->setAttributeLabels('f'.$element['undotted_path'].'_'.$element['id'],
                                        $model->attributeLabels['f'.$element['undotted_path'].'_'.$element['id']].
                                        " <span class=\"required\">*</span>");
                                }
                               ?>
                                <?php


                                    echo $form->labelEx($model,'f'.$element['undotted_path'].'_'.$element['id'], array(
                                    'class' => 'control-label label-before '.(($element['type'] == 6) ? 'medcard-date' : '')
                                )); ?>
                        	<?php
                                /*if (isset($element['element_id']))
                                    if ($element['element_id']==95)
                                    {
                                        var_dump($element);
                                        exit();
                                    }*/
                       	        if($element['type'] == 0) {
									if(isset($element['config']['showDynamic'])) {
									?>
										<span class="showDynamicWrap">
											<span class="showDynamicIcon glyphicon glyphicon-eye-open" title="Динамика изменения параметра"></span>
									<?php	
									}
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'title' => 'ID '.$element['id'].', путь '.$element['path']
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    } else {
                                        $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
									
									if(isset($element['config'], $element['config']['directLink']) && $element['config']['directLink'] == 1) {
										echo "<div class=\"input-group linkfieldGroup\">";
										$options['style'] += ';float: left;';
									}
                                
                                    echo $form->textField($model,'f'.$element['undotted_path'].'_'.$element['id'], $options);
									if(isset($element['config']['showDynamic'])) {
									?>
										</span>
									<?php	
									}
									
									if(isset($element['config'], $element['config']['directLink']) && $element['config']['directLink'] == 1) {
										echo "<span class=\"input-group-addon glyphicon glyphicon-arrow-right greeting-glyphicon-arrow-right\" title=\"Перейти по ссылке\" style=\"padding-top: 0;\"></span>
										</div>";
									}
									
                                    if($element['label_after'] != null) {
                                    ?>
                                        <label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                } elseif($element['type'] == 1) {
									if(isset($element['config']['showDynamic'])) {
									?>
										<span class="showDynamicWrap">
											<span class="showDynamicIcon glyphicon glyphicon-eye-open" title="Динамика изменения параметра"></span>
									<?php	
									}
                                    $options =  array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'title' => 'ID '.$element['id'].', путь '.$element['path']
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    } else {
                                        $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    echo $form->textArea($model,'f'.$element['undotted_path'].'_'.$element['id'], $options);
                                    if(isset($element['config']['showDynamic'])) {
									?>
										</span>
									<?php	
									}
									if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                } elseif($element['type'] == 2) {
									if(isset($element['config']['showDynamic'])) {
									?>
										<span class="showDynamicWrap">
											<span class="showDynamicIcon glyphicon glyphicon-eye-open" title="Динамика изменения параметра"></span>
									<?php	
									}
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'options' => $element['selected'],
                                        'title' => 'ID '.$element['id'].', путь '.$element['path']
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    } else {
                                        $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        <?php
                                    }
                                    // Добавим пустое значение к выпадающему списку
                                    $element['guide'][""] = 'Не выбрано';
                                    echo $form->dropDownList($model,'f'.$element['undotted_path'].'_'.$element['id'], $element['guide'], $options);
                                    if(isset($element['config']['showDynamic'])) {
									?>
										</span>
									<?php	
									}
									if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        <?php
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        <button type="button" id="ba<?php echo '_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'];  ?>" class="btnAddValue btn btn-default btn-sm">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                        <?php
                                    }
                                } elseif($element['type'] == 3) {
									if(isset($element['config']['showDynamic'])) {
									?>
										<span class="showDynamicWrap">
											<span class="showDynamicIcon glyphicon glyphicon-eye-open" title="Динамика изменения параметра"></span>
									<?php	
									}
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'options' => $element['selected'],
                                        'multiple' => 'multiple',
                                        'title' => 'ID '.$element['id'].', путь '.$element['path']
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    } else {
                                        $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                    <?php
                                    }
                                    echo $form->dropDownList($model,'f'.$element['undotted_path'].'_'.$element['id'], $element['guide'], $options);
                                    if(isset($element['config']['showDynamic'])) {
									?>
										</span>
									<?php	
									}
									if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label>
                                    <?php
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                    <?php
                                    }
                                    if($element['allow_add'] && $canEditMedcard) {
                                        ?>
                                        <button type="button" id="ba<?php
                                            //echo '_'.$prefix.'_'.$element['guide_id'];
                                            echo '_'.$prefix.'_'.$element['id'];
                                        ?>" class="btnAddValue btn btn-default btn-sm">
                                            <span class="glyphicon glyphicon-plus"></span>
                                        </button>
                                    <?php
                                    }
                                } elseif($element['type'] == 4) {
								?>

                                    <table class="controltable" title="<?php echo 'ID '.$element['id'].', путь '.$element['path']; ?>">
                                        <tbody>
                                            <?php if(isset($element['config']['cols']) && count($element['config']['cols']) > 0) {
                                                ?>
                                            <tr>
                                                <?php if(isset($element['config']['rows']) && count($element['config']['rows']) > 0) {
                                                ?>
                                                    <td></td>
                                                <?php
                                                }
                                                // Пробегаем по всей ширине таблицы.
                                                //for($i = 0; $i < count($element['config']['cols']); $i++) {
                                                //var_dump($element['config']['cols']);

                                                for($i = 0; $i < $element['config']['numCols']; $i++) {
                                                    // Если i>=длины массива cols - выводим пустую ячейку
                                                    //   Иначе выводим содержимое массива cols из i-той ячейки
                                                    if ( $i>=count($element['config']['cols']) )
                                                    {
                                                        ?><td></td><?php
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <td>
                                                            <?php echo $element['config']['cols'][$i]; ?>
                                                        </td>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <?php
                                            }
                                            for($i = 0; $i < $element['config']['numRows']; $i++) {
                                            ?>
                                            <tr>
                                                <?php
                                                //if(isset($element['config']['rows'][$i])) {
                                                if((isset($element['config']['rows'])) && ( count($element['config']['rows']) > 0  )) {
                                                   // Проверим - если счётчик меньше длины rows - выводим знаение из массива rows
                                                   //   Иначе - выведем пустую клеточку в таблице
                                                    //var_dump($element['config']['rows']);
                                                    //var_dump($element['config']['numRows']);
                                                    //var_dump('______');
                                                    //exit();

                                                    if ($i<count($element['config']['rows']))
                                                    {
                                                        ?>
                                                        <td><?php
                                                            echo $element['config']['rows'][$i];
                                                            ?></td>
                                                        <?php
                                                    }
                                                    else
                                                    {

                                                        ?>
                                                        <td></td>
                                                        <?php
                                                    }
                                                }
                                                ?>
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
														<td class="controlTableContentCell content-<?php echo $i.'_'.$j; ?>"><?php
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
                                }  if($element['type'] == 5) { // numberField
									if(isset($element['config']['showDynamic'])) {
									?>
										<span class="showDynamicWrap">
											<span class="showDynamicIcon glyphicon glyphicon-eye-open" title="Динамика изменения параметра"></span>
									<?php	
									}
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'title' => 'ID '.$element['id'].', путь '.$element['path']
                                    );
                                    if(isset($element['config'])) {
                                        if(trim($element['config']['maxValue']) != '') {
                                            $options['max'] = $element['config']['maxValue'];
                                        }
                                        if(trim($element['config']['minValue']) != '') {
                                            $options['min'] = $element['config']['minValue'];
                                        }
                                        if(trim($element['config']['step']) != '') {
                                            $options['step'] = $element['config']['step'];
                                        }
                                    }
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    } else {
                                        $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    echo $form->numberField($model,'f'.$element['undotted_path'].'_'.$element['id'], $options);
									if(isset($element['config']['showDynamic'])) {
									?>
										</span>
									<?php	
									}
									if($element['label_after'] != null) {
										?><label class="control-label label-after"><?php echo ' '.$element['label_after'] ?></label><?php
                                    }
                                } if($element['type'] == 6) { // dateField
									if(isset($element['config']['showDynamic'])) {
									?>
										<span class="showDynamicWrap">
											<span class="showDynamicIcon glyphicon glyphicon-eye-open" title="Динамика изменения параметра"></span>
									<?php	
									}
                                    $options = array(
                                        'id' => 'f_'.$prefix.'_'.implode('-', explode('|',$element['undotted_path'])).'_'.$element['id'],
                                        'class' => 'form-control',
                                        'placeholder' => '',
                                        'title' => 'ID '.$element['id'].', путь '.$element['path']
                                    );
                                    if(isset($element['size']) && $element['size'] != null) {
                                        $options['style'] = 'width: '.($element['size'] * $lettersInPixel).'px;';
                                    } else {
                                        $options['style'] = 'width: '.(40 * $lettersInPixel).'px';
                                    }
                                    if(!$canEditMedcard) {
                                        $options['disabled'] = 'disabled';
                                    }
                                    ?>
                                    <div id="<?php echo 'f_'.$prefix.'_'.implode('-', explode('|',$element['undotted_path'])).'_'.$element['id']; ?>-cont" class="input-group date null-padding-left">
                                        <?php
                                        echo $form->hiddenField($model,'f'.$element['undotted_path'].'_'.$element['id'], $options);
                                        ?>
                                        <span class="input-group-addon">
                                            <span class="glyphicon-calendar glyphicon">
                                            </span>
                                        </span>
                                        <div class="subcontrol">
                                            <div class="date-ctrl-up-buttons">
                                                <div class="btn-group">
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon up-day-button"></button>
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon month-button up-month-button"></button>
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-up glyphicon year-button up-year-button" ></button>
                                                </div>
                                            </div>
                                            <div class="form-inline subfields">
                                                <input type="text" name="day" placeholder="ДД" class="form-control day">
                                                <input type="text" name="month" placeholder="ММ" class="form-control month">
                                                <input type="text" name="year" placeholder="ГГГГ" class="form-control year">
                                            </div>
                                            <div class="date-ctrl-down-buttons">
                                                <div class="btn-group">
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon down-day-button"></button>
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon month-button down-month-button"></button>
                                                    <button type="button" tabindex="-1" class="btn btn-default btn-xs glyphicon-arrow-down glyphicon year-button down-year-button" ></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                       <?php
                                       // Если конфига нет - инитим контрол без него. Если есть - то подаём ещё и конфиг
                                        if ($element['config']!=null)
                                       {?>
                                            pushDateControl('#'+'<?php echo $options['id']; ?>'+'-cont',<?php echo CJSON::encode($element['config']); ?>);
                                       <?php
                                       }
                                       else
                                       {?>
                                           pushDateControl('#'+'<?php echo $options['id']; ?>'+'-cont');
                                       <?php
                                       }
                                       ?>
                                    </script>
									<?php if(isset($element['config']['showDynamic'])) {
									?>
										</span>
									<?php 
									}
                                    if($element['label_after'] != null) {
                                        ?>
                                        <label class="control-label label-after"><?php echo $element['label_after'] ?></label>
                                    <?php
                                    }
                                }?>

                                <?php
                                // Выведем двухколоночный список
                                if (true)
                                {
                                    if($element['type'] == 7) {
                                      //echo "Привет! я двухколоночный список";
                                      // var_dump($element);
                                      //  exit();
                                        ?>
                                        <div class="twoColumnList">
                                            <?php

                                            $sizeOfTwoColumnList = 0;

                                            if(isset($element['size']) && $element['size'] != null) {
                                                $sizeOfTwoColumnList = ($element['size'] * $lettersInPixel);
                                            } else {
                                                $sizeOfTwoColumnList  = (40 * $lettersInPixel);
                                            }

                                            ?>
                                            <select multiple="multiple" class="form-control twoColumnListFrom" style="width:<?php echo $sizeOfTwoColumnList; ?>px">
                                                <?php
                                              /*  if (!isset($element['guide']))
                                                {
                                                    var_dump($element);
                                                    exit();
                                                }*/
                                                foreach ($element['guide'] as $optionId => $oneOption)
                                                {
                                                    // Проверим - если ли в списке выбранных текущиий опшн
                                                    //  печатаем его если только его нет
                                                    if (!isset($element['selected'][$optionId]))
                                                    {
                                                        ?>
                                                        <option value="<?php echo $optionId; ?>"><?php echo $oneOption;?> </option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <div class="TCLButtonsContainer">
                                            <span class = "glyphicon glyphicon-arrow-right twoColumnAddBtn"></span>
                                            <span class = "glyphicon glyphicon-arrow-left twoColumnRemoveBtn"></span>
                                            </div>
                                            <select multiple="multiple" class="form-control twoColumnListTo" style="width:<?php echo $sizeOfTwoColumnList; ?>px">
                                                <!-- Здесь будут выбранные опции -->
                                                <?php
                                                foreach ($element['guide'] as $optionId => $oneOption)
                                                {
                                                    // А теперь наоборот - выводим только те опшины,
                                                    //    которые помечены как выделенные
                                                    if (isset($element['selected'][$optionId]))
                                                    {
                                                        ?>
                                                        <option value="<?php echo $optionId; ?>"><?php echo $oneOption;?> </option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <?php
                                            $options = array(
                                                'id' => 'f_'.$prefix.'_'.$element['undotted_path'].'_'.$element['id'],
                                                'class' => 'twoColumnHidden'
                                            );
                                            echo $form->hiddenField($model,'f'.$element['undotted_path'].'_'.$element['id'], $options);
                                            ?>
                                        </div>


                                        <?php


                                        if($element['allow_add'] && $canEditMedcard) {
                                            ?>
                                            <button type="button" id="ba<?php
                                            //echo '_'.$prefix.'_'.$element['guide_id'];
                                            echo '_'.$prefix.'_'.$element['id'];
                                            ?>" class="btnAddValue btn btn-default btn-sm">
                                                <span class="glyphicon glyphicon-plus"></span>
                                            </button>
                                        <?php
                                        }
                                        else
                                        {
                                            if($element['label_after'] != null) {
                                                ?>
                                                <label class="control-label label-after"><?php echo $element['label_after'] ?></label>
                                                <?php
                                            }
                                        }


                                    }
                                }
                                ?>
                            <?php
                            if(!$element['is_wrapped'] && $counter < count($categorie['childrenElementsOrder'])) {
                                $nextWithNewRow = false;
                            } else {
                                $nextWithNewRow = true;
                            ?>
                            </div>
                            <?php } ?>
                       	<?php                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php} ?>