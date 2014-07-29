<?php
//var_dump("!");
		//exit();
		// Перебираем элементы категории - смотрим, если текущий узел дерева - элемент, то выводим его в соответствии с его типом
		//		Иначе вызываем функцию контроллера вызывающая отрисовку детей
		//var_dump($category);
		//exit();
?>
<?php
        $elementNumber = 0;
		foreach($category as $key => $child) 
		{	
			if (!isset ($child['element'])) continue;
            $elementNumber++;
			if ($child['element']['element_id']==-1)
			{
				// Выводим название категории
				?>
				<div style="margin-left:20px;">
				<strong style="text-decoration: underline"><?php echo $child['element']['name']; ?></strong>
				<p class ="print-elements">
				<?php
                // Вызываем виджет для вложенной категории
                $printCategorieWidget = CWidget::createWidget('application.modules.doctors.components.widgets.printCategory', array(
                    'categoryToPrint' => $child
                ));
                $printCategorieWidget->run();

				?>
                    </p>
				</div>
				<?php
			}
			else
			{
				$element = $child['element'];
					if ($element['value']!='' && $element['value']!=null)
					{
						if ($element['type']=='4')
						{
							$configOfTable =  $element['config'];
							// Редактируемая таблица. Её надо раздербанить по ячейкам и вывести
							?>
							<br></br>
							<table class="tableForPrint">
							<tbody>
							<?php 
							if(isset($configOfTable['cols']) && count($configOfTable['cols']) > 0) 
							{
							?>
								<tr>
								<?php
								if(isset($configOfTable['rows']) && count($configOfTable['rows']) > 0) 
								{
								?>
									<td></td>
									<?php
								}
								for($i = 0; $i < count($configOfTable['cols']); $i++)
								{
								?>
									<td>
									<?php
									echo $configOfTable['cols'][$i];
									?>
									</td>
									<?php
								   }
								   ?>
								</tr>
								<?php
							   }
							   $valuesArr = CJSON::decode($element['value']);
							   for($i = 0; $i < $configOfTable['numRows']; $i++)
							   {
							   ?>
								<tr>
								<?php
								if(isset($configOfTable['rows'][$i]))
								{
								?>
									<td>
									<?php 
									echo $configOfTable['rows'][$i]; 
									?>
									</td>
									<?php
								   } 
								   ?>
								<?php
								for($j = 0; $j < $configOfTable['numCols']; $j++)
								{
								?>
									<td class="content-<?php echo $i.'_'.$j; ?>">
									<?php
									if(isset($valuesArr[$i][$j]))
									{
                            			echo $valuesArr[$i][$j];
									}
									?>
									</td>
									<?php
								} 
								?>
								</tr>
								<?php
							} 
							?>
							</tbody>
							</table>
							<?php
						   }
						   // Всё, что кроме таблицы - выводим значение
						   else
						   {
                               // Если дата - то её нужно перевернуть.
                               if ($element['type']==6 && $element['value']!='' && $element['value']!=NULL)
                               {
                                   $dateParts = array();
                                   $dateParts = explode('-',$element['value']);
                                   if (count($dateParts)==3)
                                   {
                                        $element['value'] = $dateParts[2].'.'.$dateParts[1].'.'.$dateParts[0];
                                   }
                               }

                                if ($element['is_wrapped']=='1' && $elementNumber!=1)
                                {

                                    ?>
                                    <br></br>
                                    <?php

                                }
                                    ?>
                                <strong><?php echo $element['label']; ?></strong> <?php echo $element['value']; ?> <strong><?php echo $element['label_after']; ?></strong>
                                <?php
						}
					}
				}
			}