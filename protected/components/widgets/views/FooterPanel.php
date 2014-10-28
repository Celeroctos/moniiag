<nav class="navbar navbar-fixed-bottom" role="navigation" id="navbarTools">
	<div class="arrow">
		<span class="glyphicon glyphicon-collapse-up"></span>
	</div>
	<div class="mainCont">
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
</nav>