<div class="header">
    <h3>Результаты приёма за <?php echo $greeting['date']; ?>, <?php echo $greeting['doctor_fio']; ?> (пациент <?php echo $greeting['patient_fio']; ?>, номер карты <?php echo $greeting['card_number']; ?>)</h3>
</div>
<?php
foreach($categories as $categorie) {
	$printThisCategory = false; // Есть ли в категориях не пустые элементы
	
	// Переберём элементы и посмотрим - есть ли не пустые элементы в категории
	foreach($categorie as $element)
	{
		if ($element['element']['value']!='' &&$element['element']['value']!=null)
		{
			$printThisCategory = true;
		}
	}


	if ($printThisCategory)
	{
		?>
		<div class="categorie">
		<h4><?php echo $categorie[0]['info']['categorie_name']; ?></h4>
		<?php
		foreach($categorie as $element)
		{
			if ($element['element']['value']!='' && $element['element']['value']!=null)
			{
				if ($element['element']['type']=='4')
				{
					$configOfTable =  CJSON::decode($element['element']['config']);
					// Редактируемая таблица. Её надо раздербанить по ячейкам и вывести
					?>
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
                       $valuesArr = CJSON::decode($element['element']['value']);
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
					?>
					<div class="field">
					<strong><?php echo $element['info']['label']; ?></strong> <?php echo $element['element']['value']; ?>
					</div>
					<?php
				}
			}
		}
		?>
		</div>
		<?
	}

}
?>
<button class="printBtn default-margin-left">Напечатать результаты приёма</button>