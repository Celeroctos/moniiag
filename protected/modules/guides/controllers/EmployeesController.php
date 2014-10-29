<?php
class EmployeesController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';
	private $employeeCategories = array(
		'Нет',
		'Врач второй категории',
		'Врач первой категории',
		'Врач высшей категории'
	);

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormEmployeeAdd;
            // Модель формы для фильтра
            $formFilter = new FormEmployeeFilter;

            // Список учреждений
            $connection = Yii::app()->db;
            $enterprisesListDb = $connection->createCommand()
                ->select('ep.*')
                ->from('mis.enterprise_params ep')
                ->order('ep.shortname asc')
                ->queryAll();

            $enterprisesList = array('-1' => 'Нет',
									 '-2' => 'Без учреждения');
            foreach($enterprisesListDb as $value) {
                $enterprisesList[(string)$value['id']] = $value['shortname'];
            }

            // Список должностей
            $connection = Yii::app()->db;
            $postsListDb = $connection->createCommand()
                ->select('m.*')
                ->from('mis.medpersonal m')
                ->order('m.name asc')
                ->queryAll();

            $postsList = array();
            foreach($postsListDb as $value) {
                $postsList[(string)$value['id']] = $value['name'];
            }

            // Список званий
            $titulsListDb = $connection->createCommand()
                ->select('t.*')
                ->from('mis.tituls t')
                ->order('t.name asc')
                ->queryAll();

            $titulsList = array();
            foreach($titulsListDb as $value) {
                $titulsList[(string)$value['id']] = $value['name'];
            }

            // Список отделений
            $wardsListDb = $connection->createCommand()
                ->select('w.*')
                ->from('mis.wards w')
                ->order('w.name asc')
                ->queryAll();

            $wardsList = array('-1' => 'Нет');
            $wardsListForAdd = array();
            foreach($wardsListDb as $value) {
                $wardsListForAdd[(string)$value['id']] = $value['name'];
            }

            // Список степеней
            $degreesListDb = $connection->createCommand()
                ->select('d.*')
                ->from('mis.degrees d')
                ->order('d.name asc')
                ->queryAll();

            $degreesList = array();
            foreach($degreesListDb as $value) {
                $degreesList[(string)$value['id']] = $value['name'];
            }
			
			// Список галочек прав
			$actionModel = new RoleAction();
			$actionsList = $actionModel->getRows(false);
			$actions =  array();
			foreach($actionsList as $key => $action) {
				if(!isset($actions[$action['groupname']])) {
					$actions[$action['groupname']] = array();
				}
				$actions[$action['groupname']][$action['id']] = $action['name'];
			}

            $this->render('view', array(
                'model' => $formAddEdit,
                'modelFilter' => $formFilter,
                'titulsList' => $titulsList,
                'postsList' => $postsList,
                'wardsList' => $wardsList,
                'wardsListForAdd' => $wardsListForAdd,
                'degreesList' => $degreesList,
                'enterprisesList' => $enterprisesList,
				'categoriesList' => $this->employeeCategories,
                'canEdit' => Yii::app()->user->checkAccess('editGuides'),
				'actions' => $actions
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormEmployeeAdd();
        if(isset($_POST['FormEmployeeAdd'])) {
			
            $model->attributes = $_POST['FormEmployeeAdd'];
            if($model->validate()) {
                if(!isset($_POST['notDateEnd']) && trim($_POST['FormEmployeeAdd']['dateEnd']) == '') {
                    echo CJSON::encode(array('success' => 'false',
                                             'errors' => array(
                                                 'dateEnd' => array('Дата конца действия не может быть пустой!')
                                             )));
                    exit();
                }
                $employee = Employee::model()->find('id=:id', array(':id' => $_POST['FormEmployeeAdd']['id']));
                $this->addEditModel($employee, $model, 'Медицинский персонал успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionFilter() {
        echo CJSON::encode(array('success' => 'true',
                                 'data' => array()));
    }

    public function actionDelete($id) {
        try {
            $employee = Employee::model()->findByPk($id);
            $employee->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Сотрудник успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModel($employee, $model, $msg) {
		if($employee) {
			// Проставляем права
			$actionModel = new RoleAction();
			$actions = $actionModel->getRows(false);
			// Выберем все, которые лежат на данную роль. Если ни одного нет, значит результат будет пуст.
			$checkedModel = new CheckedAction();
			// Обновление экшенов через удаление
			$checkedModel->deleteByEmployee($employee->id);
			// Найдём роль
			$employeeToUser = Doctor::model()->findByPk($employee->id);
			if($employeeToUser->user_id != null) { // К сотруднику может быть не прикреплён юзер..
				$rolesToUser = RoleToUser::model()->findAllRolesByUser($employeeToUser->user_id);
				$rolesIds = array();
				$num = count($rolesToUser);
				for($i = 0; $i < $num; $i++) {
					$rolesIds[] = $rolesToUser[$i]['id'];
				}
				foreach($actions as $key => $action) {
					$criteria = new CDbCriteria;
					$criteria->compare('role_id', $rolesIds);
					$criteria->compare('action_id', $action['id']);
				
					$issetAccess = CheckedAction::model()->find($criteria);
					// Если экшн есть - проставляем
					if(isset($_POST['action'.$action['id']])) {
						// Проверяем, какой это экшн: если он есть у сотрудника, то записывать такой экшн не надо.
						if(!$issetAccess) {
							$checked = new CheckedAction();
							$checked->action_id = $action['id'];
							$checked->role_id = -1;
							$checked->employee_id = $employee->id;
							$checked->mode = 0; // Добавить к роли	

							if(!$checked->save()) {
								echo CJSON::encode(array('success' => false,
														 'text' => 'Невозможно сохранить изменённые права сотрудника.'));
								exit();
							}
						}
					} else { // Если не существует - проверим, есть ли экшн для роли. Если есть, то нужно автоматически записать правило "исключить для сотрудника, но применить для роли в целом"
						if($issetAccess) {
							$checked = new CheckedAction();
							$checked->action_id = $action['id'];
							$checked->employee_id = $employee->id;
							$checked->role_id = -1;
							$checked->mode = 1; // Исключить для сотрудника;
							if(!$checked->save()) {
								echo CJSON::encode(array('success' => false,
														 'text' => 'Невозможно сохранить изменённые права сотрудника.'));
								exit();
							}
						}
					}
				}
			}
		}
	
        $employee->first_name = $model->firstName;
        $employee->middle_name = $model->middleName;
        $employee->last_name = $model->lastName;
        $employee->post_id = $model->postId;
        $employee->tabel_number = $model->tabelNumber;
        $employee->degree_id = $model->degreeId;
        $employee->titul_id = $model->titulId;
        $employee->date_begin = $model->dateBegin;
		$employee->greeting_type = $model->greetingType;
		$employee->categorie = $model->categorie;
        $employee->display_in_callcenter = $model->displayInCallcenter;

        if(!isset($_POST['notDateEnd'])) {
            $employee->date_end = $model->dateEnd;
        } else {
            $employee->date_end = null;
        }
        $employee->ward_code = $model->wardCode;

        if($employee->save()) {

            // Если текущий юзер привязан к изменяемому сотруднику... Может измениться ФИО
            if(Yii::app()->user->doctorId == $employee->id) {
                Yii::app()->user->setState('fio', $employee->last_name.' '.$employee->first_name.' '.$employee->middle_name);
                $updateFio = 1;
            } else {
                $updateFio = 0;
            }

            echo CJSON::encode(array('success' => true,
                                     'data' => array(
                                         'text' => $msg,
                                         'updateFio' => $updateFio,
                                         'fio' => Yii::app()->user->fio
                                     )
                                )
                            );
        }
    }

    public function actionAdd() {
        $model = new FormEmployeeAdd();
        if(isset($_POST['FormEmployeeAdd'])) {
            $model->attributes = $_POST['FormEmployeeAdd'];
            if($model->validate()) {
                if(!isset($_POST['notDateEnd']) && trim($_POST['FormEmployeeAdd']['dateEnd']) == '') {
                    echo CJSON::encode(array('success' => 'false',
                        'errors' => array(
                            'dateEnd' => array('Дата конца действия не может быть пустой!')
                        )));
                    exit();
                }
                $employee = new Employee();
                $this->addEditModel($employee, $model, 'Медицинский персонал успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionGet() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];

            // Фильтры поиска
            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new Employee();
            if(isset($_GET['enterpriseid'], $_GET['wardid'])) {
                $num = $model->getRows($_GET['enterpriseid'], $_GET['wardid'], $filters);
            } else {
                $num = $model->getRows(-1, -1, $filters);
            }

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $order = array(
                'fio' => 'last_name, first_name, middle_name',
                'display_in_callcenter_desc' => 'display_in_callcenter'
            );
            if(isset($order[$sidx])) {
                $sidx = $order[$sidx];
            }

            if(isset($_GET['enterpriseid'], $_GET['wardid'])) {
                $employees = $model->getRows($_GET['enterpriseid'], $_GET['wardid'], $filters, $sidx, $sord, $start, $rows);
            } else {
                $employees = $model->getRows(-1, -1, $filters, $sidx, $sord, $start, $rows);
            }

            foreach($employees as $key => &$employee) {
                $employee['fio'] = $employee['last_name'].' '.$employee['first_name'].' '.$employee['middle_name'];
                $employee['more_info'] = '<a href="#'.$employee['id'].'" class="more_info" title="Посмотреть подробную информацию по '.$employee['fio'].'"><span class="glyphicon glyphicon-share-alt"></span>
</a>';
                $employee['contact_see'] = '<a href="'.CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/guides/contacts/view').'?enterpriseid='.$employee['enterprise_id'].'&wardid='.$employee['ward_id'].'&employeeid='.$employee['id'].'" class="more_info" title="Посмотреть контакты '.$employee['fio'].'"><span class="glyphicon glyphicon-earphone"></span>
</a>';
                if($employee['display_in_callcenter'] == 1) {
                    $employee['display_in_callcenter_desc'] = 'Да';
                } else {
                    $employee['display_in_callcenter_desc'] = 'Нет';
                }
				
				if($employee['categorie'] === null) {
					$employee['categorie'] = 0;
				} 
				$employee['categorie_desc'] = $this->employeeCategories[$employee['categorie']];
            }

            echo CJSON::encode(
                array('success' => true,
                      'rows' => $employees,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Employee();
        $employee = $model->getOne($id);
		if($employee['categorie'] == null) {
			$employee['categorie'] = 0;
		}
		// Проверяем привязку сотрудника хоть к какому-нибудь пользователю
		$doctorModel = Doctor::model()->findByPk($id);
		if($doctorModel->user_id != null) {
			$issetUser = User::model()->findByPk($doctorModel->user_id);
		} else {
			$issetUser = null;
		}
		$employee['user_to_employee'] = $issetUser;
        if($issetUser) {
			// Теперь выбираем все галочки тупо для сотрудника
			$actionsDetached = array(); // Удалённые экшены из роли посредством задания их для сотрудника
			$actionsAttached = array(); // Добавленные экшены на сотрудника
			$actionsToEmployee = CheckedAction::model()->findAll('employee_id = :employee_id', array(
				':employee_id' => $employee['id']
			));
			$num = count($actionsToEmployee);
			for($i = 0; $i < $num; $i++) {
				if($actionsToEmployee[$i]->mode == 0) { // Включить в права
					$actionsAttached[] = $actionsToEmployee[$i]->action_id;
				} elseif($actionsToEmployee[$i]->mode == 1) { // Исключить из прав. Сам экшн кладётся в спец. массив для того ,чтобы можно было отобразить в интерфейсе
					$actionsDetached[] = $actionsToEmployee[$i]->action_id;
				}
			}
			
			$user = User::model()->getOne($issetUser['id']);
			$actionModel = new CheckedAction();
			$actionsArr = array();
			foreach($user['role_id'] as $roleId) {
				$actions = $actionModel->getByRole($roleId);
				foreach($actions as $key => $action) {
					$issetAlready = array_search($action['action_id'], $actionsAttached) !== false || array_search($action['action_id'], $actionsDetached) !== false;

					if(!$issetAlready) { // В противном случае, это либо приаттаченные, либо детаченные экшены для сотрудника
						$actionsArr[] = $action['action_id'];
					}
				}
			}
			
			$employee['actions'] = $actionsArr;
			$employee['actions_detached'] = $actionsDetached;
			$employee['actions_attached'] = $actionsAttached;
		}
		echo CJSON::encode(array('success' => true,
                                 'data' => $employee)
        );
    }

    public function actionGetByWard($id) {
        $model = new Employee();
        $employees = $model->getByWard($id, -1);

        echo CJSON::encode(array('success' => true,
                                 'data' => $employees)
        );
    }
	
	public function actionGetByWardAndMedworker($wardid, $medworkerid) {
		$wardid = CJSON::decode($wardid);
		$medworkerid = CJSON::decode($medworkerid);
		foreach($wardid as $val) {
			if($val == -1) {
				$wardid = -1;
				break;
			}
		}
		foreach($medworkerid as $val) {
			if($val == -1) {
				$medworkerid = -1;
				break;
			}
		}
		
        $model = new Employee();
        $employees = $model->getByWardAndMedworker($wardid, $medworkerid);

        echo CJSON::encode(array('success' => true,
                                 'data' => $employees)
        );
    }

}

?>