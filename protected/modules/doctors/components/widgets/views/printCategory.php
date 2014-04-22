<?php
//var_dump("!");
		//exit();
		// Перебираем элементы категории - смотрим, если текущий узел дерева - элемент, то выводим его в соответствии с его типом
		//		Иначе вызываем функцию контроллера вызывающая отрисовку детей
		//var_dump($category);
		//exit();
		foreach($category as $key => $child) 
		{	
			if (!isset ($child['element'])) continue;
			//var_dump($child);
			//exit();
			//var_dump("!");
			//exit();	
			if ($child['element']['element_id']==-1)
			{
				// Выводим название категории
				?>
				<div style="margin-left:20px;">
				<h4><?php echo $child['element']['name']; ?></h4>
				<p class ="print-elements">
				<?php
				$this->drawPrintCategorie($child);			
				?></p>
				</div>
				<?php
			}
			else
			{	
				//var_dump("!");
				//exit();
				$element = $child['element'];
					//if (!isset ($element['element'])) continue;
					//var_dump("!");
					//exit();
					if ($element['value']!='' && $element['value']!=null)
					{
						if ($element['type']=='4')
						{
							//$configOfTable =  CJSON::decode($element['config']);
							$configOfTable =  $element['config'];
							// Редактируемая таблица. Её надо раздербанить по ячейкам и вывести
							?>
							<br>
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
                   			if ($element['is_wrapped']=='1')
                   			{
						   ?>
								<br>
								<?php
							}
							
							//var_dump($element);
							//exit();
							
								?>
							<!--<div class="field inline-element">-->
							<!--<div class="inline-element">-->
							<strong><?php echo $element['label']; ?></strong> <?php echo $element['value']; ?> <strong><?php echo $element['label_after']; ?></strong>
					
							<!--</div>-->
					
							<?php
						}
					}
				}
			}