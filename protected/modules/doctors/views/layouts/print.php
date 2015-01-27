<!DOCTYPE html>
<head>
	<title>Амбулаторная карта больного</title><link href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.less" rel="stylesheet/less" media="screen">
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/paper.less" rel="stylesheet/less" media="print">
    <script type="text/javascript">
        var globalVariables = {
            baseUrl : '<?php echo Yii::app()->request->baseUrl; ?>'
        };
    </script>
    <style>
        @media print {
            .printBtn {
                display: none;
            }
        }
    </style>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/less-1.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/print.js"></script>
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
<?php echo $content; ?>
</body>
</html>