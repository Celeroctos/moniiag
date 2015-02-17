<?php
/**
 * Основной шаблон модуля платных услуг
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
	<?php Yii::app()->clientScript->registerPackage('paid')?>
	<title><?= CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="b-nav nav nav-pills nav-justified">
						<li role="presentation" class="active"><a class="b-nav__href" href="#">Касса</a></li>
						<li role="presentation"><a class="b-nav__href" href="#">Журнал</a></li>
						<li role="presentation"><a class="b-nav__href" href="#">Отчёты</a></li>
						<li role="presentation"><a class="b-nav__href" href="#">Прайс-лист</a></li>
						<li role="presentation"><a class="b-nav__href" href="#">Настройки</a></li>
						<li role="presentation"><a class="b-nav__href" href="#">Пациенты</a></li>
				</ul>
			</div>
		<?php $this->widget('FlashMessager'); ?>
		<?= $content; ?>
	</div>
</body>
</html>