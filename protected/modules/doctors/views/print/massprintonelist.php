<?php
foreach($greetings as $greeting) {


?><div class="bottom-border-dotted"><?php
// Вызываем виджет печати одного приёма
$this->render('greeting', array(
	'templates' => $greeting['templates'],
	'greeting' => $greeting['greeting'],
	'notPrintPrintBtn' => '1'
	));
?></div><?php

}
?>
<button class="printBtn default-margin-left">Напечатать результаты приёма</button>