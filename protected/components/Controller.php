<?php
class Controller extends CController {
    /* Неправильное использование, но пока непонятно, как переопределить конструктор */
    // Фильтр для выполнения запроса по поводу прав доступа
    public function filterGetAccessHierarchy($filterChain) {
        $roleModel = new Role();
        $currentRoles = $roleModel->getCurrentUserRoles();
        // Создаём иерархию для текущей роли пользователя
        $auth = Yii::app()->authManager;
        $role = $auth->createRole('r'.$currentRoles['id'], '');
        $result = $auth->assign('r'.$currentRoles['id'], Yii::app()->user->getId()); // Текущему юзеру назначаем эту роль
        foreach($currentRoles['actions'] as $id => $action) {
            $auth->createOperation($action);
            $role->addChild($action);
        }

        $filterChain->run();
    }

    public function filters() {
        return array(
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