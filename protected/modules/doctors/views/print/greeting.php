<div class="header">
    <h3>Результаты приёма за <?php echo $greeting['date']; ?>, <?php echo $greeting['doctor_fio']; ?> (пациент <?php echo $greeting['patient_fio']; ?>, номер карты <?php echo $greeting['card_number']; ?>)</h3>
</div>
<?php
foreach ($templates as $oneTemplate)
{
	// Печатаем название шаблона
	?><h3><?php echo $oneTemplate['name']; ?></h3><?php
		foreach($oneTemplate['cats']  as $index => $categorie) {
		$this->drawPrintCategorie($categorie);
	}
	
}
?><br>
<?php
if (!isset($notPrintPrintBtn))
{
	?><button class="printBtn default-margin-left">Напечатать результаты приёма</button><?php
}