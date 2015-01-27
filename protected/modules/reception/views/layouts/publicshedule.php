<!DOCTYPE html>
<head>
    <title>МИС МОНИИАГ</title>
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/bootstrap-3.0.0/less/bootstrap.less" rel="stylesheet/less" media="screen">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/publicshedule.less" rel="stylesheet/less" media="screen">
    <script type="text/javascript">
        var globalVariables = {
            baseUrl : '<?php echo Yii::app()->request->baseUrl; ?>'
        };
    </script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/less-1.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/bootstrap-3.0.0/dist/js/bootstrap.min.js"></script>
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
	<div class="container-fluid" id="content">
		<div class="row main-container">
			<div class="col-xs-12">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
</body>
</html>