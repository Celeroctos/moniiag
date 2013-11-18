<?php
class RolesController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        $roleModel = new Role();
        $roles = $roleModel->getRows(false);
        $rolesList = array('-1' => 'Нет');
        foreach($roles as $key => $role) {
            $rolesList[$role['id']] = $role['name'];
        }

        $actionModel = new RoleAction();
        $actionsList = $actionModel->getRows(false);
        $actions =  array();
        foreach($actionsList as $key => $action) {
            if(!isset($actions[$action['groupname']])) {
                $actions[$action['groupname']] = array();
            }
            $actions[$action['groupname']][$action['id']] = $action['name'];
        }

        $this->render('index', array(
            'model' => new FormRoleAdd(),
            'actions' => $actions,
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

            $model = new Role();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $roles = $model->getRows($filters, $sidx, $sord, $start, $rows);
            foreach($roles as $index => &$role) {
                if($role['parent_id'] == -1) {
                    $role['parent'] = 'Нет';
                }
            }

            echo CJSON::encode(
                array('rows' => $roles,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Role();
        $role = $model->getOne($id);

        $actionModel = new CheckedAction();
        $actions = $actionModel->getByRole($id);
        $role['actions'] = array();
        foreach($actions as $key => $action) {
            $role['actions'][] = $action['action_id'];
        }

        echo CJSON::encode(array('success' => true,
                                 'data' => $role)
        );
    }


    public function actionEdit() {
        $model = new FormRoleAdd();
        if(isset($_POST['FormRoleAdd'])) {
            $model->attributes = $_POST['FormRoleAdd'];
            if($model->validate()) {
                $role = Role::model()->find('id=:id', array(':id' => $_POST['FormRoleAdd']['id']));
                $this->addEditModel($role, $model, 'Роль успешно отредактирована.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }


    public function actionAdd() {
        $model = new FormRoleAdd();
        if(isset($_POST['FormRoleAdd'])) {
            $model->attributes = $_POST['FormRoleAdd'];
            if($model->validate()) {
                $role = new Role();
                $this->addEditModel($role, $model, 'Роль успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }

    }


    private function addEditModel($role, $model, $msg) {
        $role->name = $model->name;
        $role->parent_id = $model->parentId;

        $success = true;
        if(!$role->save()) {
            $success = false;
        }

        // Проставляем права
        $actionModel = new RoleAction();
        $actions = $actionModel->getRows(false);
        // Выберем все, которые лежат на данную роль. Если ни одного нет, значит результат будет пуст.
        $checkedModel = new CheckedAction();
        // Обновление экшенов через удаление
        $checkedModel->deleteByRole($role->id);

        foreach($actions as $key => $action) {
            // Если экшн есть - проставляем
            if(isset($_POST['action'.$action['id']])) {
                $checked = new CheckedAction();
                $checked->action_id = $action['id'];
                $checked->role_id = $role->id;
                if(!$checked->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'text' => 'Невозможно сохранить действие.'));
                }
            }
        }

        if($success) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionDelete($id) {
        try {
            $role = Role::model()->findByPk($id);
            $role->delete();

            $checked = new CheckedAction();
            $checked->deleteByRole($id);

            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Роль успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

}

?>