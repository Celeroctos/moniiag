<?php

$printGreetingsWidget = CWidget::createWidget('application.modules.doctors.components.widgets.massPrint', array(
    'greetings' => $greetings
));
$printGreetingsWidget->run();

if (false)
{
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
}

if (!$notPrintButton)
{
?>
<button class="printBtn default-margin-left">Напечатать результаты приёма</button>
<?php }?>