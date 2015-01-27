<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/libs/jquery-json.js" ></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/reception/motionHistory.js" ></script>
<span id = 'oms-id' class = 'no-display'><?php echo ($omsid)?></span>
<h4>История медицинской карты</h4>
<?php echo ($fio)?>
<div id="cardMotionHistory" class="row">                
    <table id="motion-history"></table>
    <div id="motion-historyPager"></div>
</div>