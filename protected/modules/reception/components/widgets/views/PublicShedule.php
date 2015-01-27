<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/publicshedule.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-json.js"></script>
<div class="navbar-fixed-top" id="sheduleNavbar">
    <div class="col-xs-2">
		<a href="http://moniiag.ru" title="МОНИИАГ" id="logo">
			<img src="http://moniiag.ru/assets/templates/med/images/logo-4-1.png" alt="" height="100" />
		</a>
	</div>
	<div class="col-xs-8">
		<div id="dateCont">
		</div>
	</div>
	<div class="col-xs-2">
		<div id="timeCont"></div>
		<span class="glyphicon glyphicon-cog"></span>
	</div>
</div>
<div class="row col-xs-12" id="sheduleRow">
</div>
<nav class="navbar navbar-default navbar-fixed-bottom" role="navigation">
	<div class="marquee col-xs-12">
		<span>Текст бегущей строки...</span>
	</div>
</nav>
