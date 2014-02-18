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

        $pagesListDb = MenuPage::model()->findAll();
        $pagesList = array('-1' => 'Нет');
        foreach($pagesListDb as $page) {
            if($page->priority != null) {
                $pagesList[$page->id] = $page->name;
            }
        }

        $this->render('index', array(
            'model' => new FormRoleAdd(),
            'actions' => $actions,
            'rolesList' => $rolesList,
            'pagesList' => $pagesList
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
                if($role['startpage_id'] == null) {
                    $role['startpage_id'] = -1;
                    $role['startpage'] = 'Нет';
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
        if($role['startpage_id'] == null) {
            $role['startpage_id'] = -1;
            $role['startpage'] = 'Нет';
        }

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
        $role->startpage_id = $model->pageId;

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


    public function actionStartpagesView() {
        $model = new FormStartpageAdd();
        $this->render('startpages', array(
            'model' => $model
        ));
    }

    public function actionGetStartpages() {
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

            $model = new Startpage();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $startpages = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $startpages,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetOneStartpage($id) {
        $model = new Startpage();
        $startpage = $model->getOne($id);

        echo CJSON::encode(array('success' => true,
                                 'data' => $startpage)
        );
    }

    public function actionEditStartpage() {
        $model = new FormStartpageAdd();
        if(isset($_POST['FormStartpageAdd'])) {
            $model->attributes = $_POST['FormStartpageAdd'];
            if($model->validate()) {
                $startpage = Startpage::model()->find('id=:id', array(':id' => $_POST['FormStartpageAdd']['id']));
                $this->addEditModelStartpage($startpage, $model, 'Стартовая страница успешно отредактирована.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    private function addEditModelStartpage($startpage, $model, $msg) {
        if($model->priority < 0) {
            echo CJSON::encode(array('success' => false,
                'errors' => array(
                    'priority' => array(
                        'Приоритет должен быть больше нуля!'
                    )
                )));
            exit();
        }
        // Смотрим, нет ли страницы с таким приоритетом
        $issetStartPage = Startpage::model()->find('priority = :priority', array(':priority' => $model->priority));
        if($issetStartPage != null) {
            echo CJSON::encode(array('success' => false,
                                     'errors' => array(
                                         'priority' => array(
                                            'Такой приоритет страницы уже существует!'
                                         )
                                     )));
            exit();
        }

        $startpage->name = $model->name;
        $startpage->url = $model->url;
        $startpage->priority = $model->priority;

        if($startpage->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionAddStartpage() {
        $model = new FormStartpageAdd();
        if(isset($_POST['FormStartpageAdd'])) {
            $model->attributes = $_POST['FormStartpageAdd'];
            if($model->validate()) {
                $startpage = new Startpage();
                $this->addEditModelStartpage($startpage, $model, 'Стартовая страница успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionDeleteStartpage($id) {
        try {
            $issetStartpageRole = Role::model()->find('startpage_id = :id', array(':id' => $id));
            if($issetStartpageRole != null) {
                throw new Exception();
            }

            $startpage = Startpage::model()->findByPk($id);
            $startpage->delete();

            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Стартовая страница успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

}

?>