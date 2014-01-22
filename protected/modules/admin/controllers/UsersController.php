<?php
class UsersController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        $formUserAdd = new FormUserAdd();

        // Список сотрудников
        $employeeModel = new Employee();
        $employees = $employeeModel->getRows(-1, -1, false, false, false, false, false, true);
        $employeesList = array();
        foreach($employees as $key => $employee) {
            $employeesList[$employee['id']] = $employee['last_name'].' '.$employee['first_name'].' '.$employee['middle_name'].' ('.mb_strtolower($employee['ward'], 'UTF-8').' отделение, '.$employee['enterprise'].')';
        }

        // Список ролей
        $roleModel = new Role();
        $roles = $roleModel->getRows(false);
        $rolesList = array();
        foreach($roles as $key => $role) {
            $rolesList[$role['id']] = $role['name'];
        }

        $this->render('index', array(
            'model' => $formUserAdd,
            'employeesList' => $employeesList,
            'rolesList' => $rolesList
        ));
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

            $model = new User();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $users = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $users,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new User();
        $user = $model->getOne($id);
        if($user['employee_id'] != null) {
            // Выбираем ещё и сотрудника вдовесок к юзеру
            $employee = Employee::model()->getOne($user['employee_id']);
            if($employee != null) {
                $user['employee_fio'] = $employee['last_name'].' '.$employee['first_name'].' '.$employee['middle_name'].' ('.$employee['ward'].' отделение, '.$employee['enterprise'].')';
            }
        }

        // Выбираем ещё всех тех сотрудников, которые могут быть ассоциированы
        echo CJSON::encode(array('success' => true,
                                 'data' => array(
                                    'user' => $user,
                                    'associatedEmployees' => $this->getAllForAssociate()
                                 )
                            )
                        );
    }

    public function actionGetAllForAssociate() {
        echo CJSON::encode(array('success' => true,
                'data' => $this->getAllForAssociate()
            )
        );
    }

    private function getAllForAssociate() {
        $associatedEmployees = array();
        $associatedEmployeesDb = Employee::model()->getAllWithoutUsers();
        foreach($associatedEmployeesDb as $associatedEmployee) {
            $associatedEmployees[] = array(
                'employee_fio' => $associatedEmployee['last_name'].' '.$associatedEmployee['first_name'].' '.$associatedEmployee['middle_name'].' ('.$associatedEmployee['ward'].' отделение, '.$associatedEmployee['enterprise'].')',
                'employee_id' => $associatedEmployee['id']
            );
        }
        return $associatedEmployees;
    }

    public function actionEdit() {
        $model = new FormUserAdd();
        if(isset($_POST['FormUserAdd'])) {
            $model->attributes = $_POST['FormUserAdd'];
            if($model->validate()) {
                $user = User::model()->find('id=:id', array(':id' => $_POST['FormUserAdd']['id']));
                $this->addEditModel($user, $model, 'Пользователь успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionAdd() {
        $model = new FormUserAdd();
        if(isset($_POST['FormUserAdd'])) {
            $model->attributes = $_POST['FormUserAdd'];
            if($model->validate()) {
                // Если юзер с таким логином уже есть - выводить сообщение
                $user = User::model()->find('login=:login', array(':login' => $_POST['FormUserAdd']['login']));
                if($user != null) {
                    echo CJSON::encode(array('success' => 'false',
                                            'errors' => array(
                                                array('Пользователь с таким логином уже существует!')
                                            )
                                        )
                                    );
                    exit();
                }

                $user = new User();
                $this->addEditModel($user, $model, 'Пользователь успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }

    }

    public function actionDelete($id) {
        try {
            $user = User::model()->findByPk($id);
            $user->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Пользователь успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionChangepass() {
        if(isset($_POST['FormUserAdd'])) {
            if(isset($_POST['FormUserAdd']['password'], $_POST['FormUserAdd']['passwordRepeat']) && trim($_POST['FormUserAdd']['password']) != '' && $_POST['FormUserAdd']['password'] == $_POST['FormUserAdd']['passwordRepeat']) {
                $user = User::model()->find('id=:id', array(':id' => $_POST['FormUserAdd']['id']));
                if(mb_strlen(trim($_POST['FormUserAdd']['password'])) < 6) {
                    echo CJSON::encode(array('success' => 'false',
                                             'errors' => array(
                                                array('Пароль не может быть меньше 6 символов!')
                                            )
                                        )
                                    );
                    exit();
                }

                $user->password = crypt($_POST['FormUserAdd']['password'], $_POST['FormUserAdd']['password']);
                if($user->save()) {
                    echo CJSON::encode(array('success' => 'true',
                                             'msg' => 'Пароль успешно отредактирован.'));
                } else {
                    echo CJSON::encode(array('success' => 'false',
                                            'errors' => array(
                                                array('Невозможно сохранить отредактированную запись!')
                                            )
                                        )
                                    );
                }
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => array(
                                                        array('Пароль и повтор пароля не совпадают!')
                                         )
                                    )
                                );
            }
        } else {
            echo CJSON::encode(array('success' => 'false',
                                     'errors' => array()));
        }
    }

    private function addEditModel($user, $model, $msg) {
        if($model->password != null) {
            $user->password = crypt($model->password, $model->password);
        }
        $user->username = $model->username;
        $user->login = $model->login;
        $user->role_id = $model->roleId;
        $user->employee_id = $model->employeeId;

        if($user->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }
}

?>