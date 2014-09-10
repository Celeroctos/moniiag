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
	
	public function actionGetOnlineData() {
		$answer = array();
		if(Yii::app()->user->getState('isActiveSession', -1) != -1) {
			$answer['isActiveSession'] = Yii::app()->user->getState('isActiveSession');
			Yii::app()->user->setState('isActiveSession', 0);
		}
		echo CJSON::encode(
			array('success' => true,
				  'data' => $answer
			)
		);
	}
}
?>