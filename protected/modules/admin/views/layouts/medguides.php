<!DOCTYPE html>
<head>
    <title>МИС МОНИИАГ</title>
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/bootstrap-3.0.0/less/bootstrap.less" rel="stylesheet/less" media="screen">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstrap-datetimepicker.min.css" rel="stylesheet/less" media="screen">
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-ui-bootstrap/css/custom-theme/jquery-ui-1.10.0.custom.css" rel="stylesheet" type="text/css" media="screen"  />
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jqGrid/css/ui.jqgrid.css" rel="stylesheet" type="text/css" media="screen"  />
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.less" rel="stylesheet/less" media="screen">

    <script type="text/javascript">
        var globalVariables = {
            baseUrl : '<?php echo Yii::app()->request->baseUrl; ?>'
        };
    </script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/less-1.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/767e5633/jquery.yiiactiveform.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/bootstrap-3.0.0/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/bootstrap-datetimepicker.ru.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery.selection.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jqGrid/js/i18n/grid.locale-ru.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jqGrid/js/jquery.jqGrid.src.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-browser.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery.keyfilter-1.7.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/main.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/datecontrol.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/timecontrol.js"></script>
        <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/keyboard.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/keyboardcnf.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/pagination.js"></script>
    <style>
        body {
            font-size: <?php echo Yii::app()->user->fontSize; ?>px !important;
        }
        .errorText {
            font-size: <?php echo Yii::app()->user->fontSize + 2; ?>px;
        }
    </style>
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
<?php $this->widget('application.components.widgets.MainNavBar') ?>
<div class="container-fluid" id="content">
    <div class="row main-container">
        <div class="col-xs-2">
            <?php $this->widget('application.components.widgets.SideMenu') ?>
        </div>
        <div class="col-xs-9">
            <h4>Справочники врачей для выбора</h4>
            <p>Здесь Вы можете задать необходимые значения для созданных справочников в разделе "Рабочее место врача" -> "Справочники".</p>
            <?php echo $content; ?>
        </div>
    </div>
</div>
<div class ="buttonUpContainer">
		<nobr><span class="buttonUp"><span class ="glyphicon glyphicon-chevron-up buttonUpSign"></span><span class="buttonUpText">Наверх</span></span><nobr>
</div>
<?php $this->widget('application.components.widgets.FooterPanel'); ?>
</body>
</html>