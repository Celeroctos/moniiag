<!DOCTYPE html>
<head>
    <title>Амбулаторная карта больного</title>
    <link href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.less" rel="stylesheet/less" media="screen">
    <script type="text/javascript">
        var globalVariables = {
            baseUrl : '<?php echo Yii::app()->request->baseUrl; ?>'
        };
    </script>
    <style>
        @media print {
            .printBtnTr {
                display: none;
            }
        }
    </style>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/less-1.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/libs/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/doctors/print.js"></script>
</head>
<body>
<?php echo $content; ?>
</body>
</html>