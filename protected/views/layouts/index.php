<!DOCTYPE html>
<head>
    <title>МИС МОНИИАГ</title>
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/bootstrap-3.0.0/less/bootstrap.less" rel="stylesheet/less" media="screen">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-datetimepicker.min.css" rel="stylesheet/less" media="screen">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.less" rel="stylesheet/less" media="screen">
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/less-1.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/bootstrap-3.0.0/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/bootstrap-datetimepicker.ru.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery.selection.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-browser.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-keyboard-layout.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery.keyfilter-1.7.js"></script>
    <script type="text/javascript">
        var globalVariables = {
            baseUrl : '<?php echo Yii::app()->request->baseUrl; ?>'
        };
    </script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/main.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js"></script>
  <!--  <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/keyboard.js"></script> -->
</head>
<body>
<?php $this->widget('application.components.widgets.MainNavBar') ?>
<div class="container-fluid" id="content">
    <div class="row main-container">
        <div class="col-xs-3">
            <?php $this->widget('application.components.widgets.SideMenu') ?>
        </div>
        <div class="col-xs-8">
            <?php echo $content; ?>
        </div>
    </div>
</div>
</body>
</html>