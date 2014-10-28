<?php
class FooterPanel extends CWidget {
    public function run() {
        $this->render('application.components.widgets.views.FooterPanel', array(
            'controller' => strtolower($this->controller->getId()),
            'module' => $this->controller->getModule() != null ? strtolower($this->controller->getModule()->getId()) : null,
            'action' => $this->controller->getAction() != null ? strtolower($this->controller->getAction()->getId()) : $this->controller->defaultAction,
			'employees' => $this->getEmployeesForCurrentUser()
		));
    }
	
	// Получить список сотрубников для текущего пользователя
	private function getEmployeesForCurrentUser() {
		$employeesIds = array();
		foreach(Doctor::model()->findAll('user_id = :user_id', array(':user_id' => Yii::app()->user->id)) as $key => $doctor) {
			$employeesIds[] = $doctor['id'];
		}
		$employees = array();
		foreach($employeesIds as $id) {
			$employee = Doctor::model()->findByPk($id);
			if($employee != null) {
				$employees[(string)$id] = $employee->last_name.' '.$employee->first_name.' '.($employee->middle_name == null ? '' : $employee->middle_name).', табельный номер '.$employee->tabel_number;
			}
		}
		return $employees;
	}
}

?>