<?php
class Controller extends CController {
	protected $sessionActiveActions = array(
		array(
			'module' => 'admin',
			'controller' => 'tasu',
			'action' => 'syncoms'
		),
		array(
			'module' => 'admin',
			'controller' => 'tasu',
			'action' => 'syncpatients'
		),
		array(
			'module' => 'admin',
			'controller' => 'tasu',
			'action' => 'syncdoctors'
		),
		array(
			'module' => 'admin',
			'controller' => 'tasu',
			'action' => 'syncinsurances'
		)
	);
    /* Неправильное использование, но пока непонятно, как переопределить конструктор */
    // Фильтр для выполнения запроса по поводу прав доступа
    public function filterGetAccessHierarchy($filterChain) {
        // Здесь нужно писать логи...

        $action = Yii::app()->getController()->getAction();
        if ($action->id=='acceptdata')
        {
            $filterChain->run();
            return;
        }
		
        if((Yii::app()->user->isGuest && mb_strtolower($this->route) != 'index/index' && mb_strtolower($this->route) != 'users/login' && mb_strtolower($this->route) != 'users/loginstep2') || Yii::app()->user->getState('authStep', -1) != -1) {
			if(Yii::app()->user->getState('authStep', -1) != -1 && !Yii::app()->request->getIsAjaxRequest()) {
				Yii::app()->user->logout();
				Yii::app()->user->setState('authStep', -1);
			}
            // Если гость, то не давать заходить куда-то. Только если это не второй шаг логина
			if(mb_strtolower($this->route) != 'users/loginstep2') {
				$this->redirect('/');
			}
        } elseif(!Yii::app()->user->isGuest && $this->route == 'index/index') {
			$this->redirect(Yii::app()->request->baseUrl.''.Yii::app()->user->startpageUrl);
        } 

        $roleModel = new Role();
        $currentRoles = $roleModel->getCurrentUserRoles();
		
		// Выясняем права пользователя относительно текущего сотрудника. Что добавить, что не учитывать
		$actionsDetached = array(); // Удалённые экшены из роли посредством задания их для сотрудника
		$actionsAttached = array(); // Добавленные экшены на сотрудника
		$actionsToEmployee = isset(Yii::app()->user->doctorId) ? CheckedAction::model()->findAllWithKeysByEmployee(Yii::app()->user->doctorId) : array();

		$num = count($actionsToEmployee);
		for($i = 0; $i < $num; $i++) {
			if($actionsToEmployee[$i]['mode'] == 0) { // Включить в права
				$actionsAttached[(string)$actionsToEmployee[$i]['action_id']] = $actionsToEmployee[$i]['accessKey'];
			} elseif($actionsToEmployee[$i]['mode'] == 1) { // Исключить из прав. Сам экшн кладётся в спец. массив для того ,чтобы можно было отобразить в интерфейсе
				$actionsDetached[(string)$actionsToEmployee[$i]['action_id']] = $actionsToEmployee[$i]['accessKey'];
			}
		}

        // Создаём иерархию для текущей роли пользователя
        $auth = Yii::app()->authManager;
        $role = $auth->createRole('r'.$currentRoles['id'], '');
        $result = $auth->assign('r'.$currentRoles['id'], Yii::app()->user->getId()); // Текущему юзеру назначаем эту роль
		foreach($currentRoles['actions'] as $id => $action) {
			if(array_key_exists($id, $actionsDetached) === false && array_key_exists($id, $actionsAttached) === false) {
				$auth->createOperation($action);
				$role->addChild($action);
			}
        }

		// Создаём иерархию дополнительно для сотрудника
		foreach($actionsAttached as $key => $action) {
			$auth->createOperation($action);
			$role->addChild($action);
		}
		
		// Теперь пишем лог
		$logModel = new Log();
		$logModel->user_id = Yii::app()->user->id;
		$logModel->url = Yii::app()->request->url;
		$logModel->changedate = date('Y-n-j');
		$logModel->changetime = date('h:i:s');
		$logModel->save();
		
        $filterChain->run();
    }
	
	public function filterSessionTimerHandler($filterChain) {
		if(Yii::app()->request->isAjaxRequest) {
			$module = $this->getModule();
			if($module != null) {
				$module = $module->getId();
			}
			$controller = $this->getId();
			$action = $this->getAction()->getId();
			foreach($this->sessionActiveActions as $element) {
				if(strtolower($controller) == $element['controller'] && strtolower($module) == $element['module'] && strtolower($action) == $element['action']) {
					Yii::app()->user->setState('currentSessionPeriod', time());
					Yii::app()->user->setState('isActiveSession', 1);
				}
			}
		}

		$filterChain->run();
	}

    public function filters() {
        return array(
			'SessionTimerHandler',
            'GetAccessHierarchy'
        );
    }

    public function actionError() {
        if($error = Yii::app()->errorHandler->error)  {
            if(Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', array('error' => $error));
            }
        }
    }
}