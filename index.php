<?php
ob_start();
// change the following paths if necessary
$yii1Path = '/../yii';
$yii1 = dirname(__FILE__).$yii1Path.'/yii.php';

$yii2Path = '/../yii2/';
$yii2 = dirname(__FILE__).$yii2Path.'/framework/yii.php';

$configYii1 = dirname(__FILE__).'/protected/config/main.php';
//$configYii2 = dirname(__FILE__).'/protected/config/main_yii2.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii1);
//require_once($yii2);

//new yii\web\Application($yii2Config);
Yii::createWebApplication($configYii1)->run();
