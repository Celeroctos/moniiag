<?php
class CabinetsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormCabinetAdd;

            // Список учреждений
            $connection = Yii::app()->db;
            $enterprisesListDb = $connection->createCommand()
                ->select('ep.*')
                ->from('mis.enterprise_params ep')
                ->queryAll();

            $enterprisesList = array('-1' => '');
            foreach($enterprisesListDb as $value) {
                $enterprisesList[(string)$value['id']] = $value['shortname'];
            }

            // Список отделений появляется только для конкретного учреждения
            $wardsList = array();

            $this->render('view', array(
                'model' => $formAddEdit,
                'wardsList' => $wardsList,
                'enterprisesList' => $enterprisesList
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormCabinetAdd();
        if(isset($_POST['FormCabinetAdd'])) {
            $model->attributes = $_POST['FormCabinetAdd'];
            if($model->validate()) {
                $cabinet = Cabinet::model()->find('id=:id', array(':id' => $_POST['FormCabinetAdd']['id']));
                $this->addEditModel($cabinet, $model, 'Кабинет успешно отредактирован.');
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
            $cabinet = Cabinet::model()->findByPk($id);
            $cabinet->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Кабинет успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModel($cabinet, $model, $msg) {

        $cabinet->enterprise_id = $model->enterpriseId;
        $cabinet->ward_id = $model->wardId;
        $cabinet->description = $model->description;
        $cabinet->cab_number = $model->cabNumber;

        if($cabinet->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionAdd() {
        $model = new FormCabinetAdd();
        if(isset($_POST['FormCabinetAdd'])) {
            $model->attributes = $_POST['FormCabinetAdd'];
            if($model->validate()) {
                $cabinet = new Cabinet();
                $this->addEditModel($cabinet, $model, 'Кабинет успешно добавлен.');
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

            $model = new Cabinet();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $cabinets = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $cabinets,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Cabinet();
        $cabinet = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $cabinet)
        );
    }
}

?>