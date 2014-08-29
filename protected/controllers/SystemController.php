<?php
class SystemController extends Controller {
	public function actionGetSettings() {
		echo CJSON::encode(
			array('success' => true,
				  'data' => array(
					array(
						'func' => 'setSessionTimer',
						'value' => Setting::model()->find('module_id = -1 AND name = :name', array(':name' => 'sessionStandbyTime'))->value
					)
				)
			)
		);
	}
}
?>