<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/footerpanel.js"></script>
<nav class="navbar navbar-fixed-bottom" role="navigation" id="navbarTools">
	<div class="arrow-panel">
		<span class="glyphicon glyphicon-collapse-up"></span>
	</div>
	<div class="mainCont">
		<ul id="footerTabPanel">
			<li class="panel1 active">Пользователь</li>
			<li class="panel2">Инструменты</li>
		</ul>
		<div id="panel1" class="footerPanel active">
			<strong>
				Сейчас я сотрудник
			</strong>
			<select name="currentEmployeeRole" id="currentEmployeeRole" class="form-control">
			<?php
				foreach($employees as $key => $item) {
					echo "<option value=\"{$key}\" ".(Yii::app()->user->doctorId == $key ? "selected=\"selected\" " : "").">{$item}</option>";
				}
			?>
			</select>
			<button id="submitEmployeeRole" class="btn submitEmployeeRole">OK</button>
		</div>
		<div id="panel2" class="footerPanel">
			<ul id="toolsList">
				<li>
					<a href="#" id="calcBBToolLink">
						<img src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/tools_calc_bb.png" width="32" height="32" alt="Калькулятор беременности">
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>