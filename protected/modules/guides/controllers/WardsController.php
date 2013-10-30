<?php
class WardsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormWardAdd;

            // Список учреждений
            $connection = Yii::app()->db;
            $enterprisesListDb = $connection->createCommand()
                ->select('ep.*')
                ->from('mis.enterprise_params ep')
                ->queryAll();

            $enterprisesList = array();
            foreach($enterprisesListDb as $value) {
                $enterprisesList[(string)$value['id']] = $value['fullname'];
            }

            $this->render('view', array(
                'model' => $formAddEdit,
                'typesList' => $enterprisesList
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {

    }

    public function actionDelete() {


    }

    public function actionAdd() {
        $model = new FormWardAdd();
        if(isset($_POST['FormWardAdd'])) {
            $model->attributes = $_POST['FormWardAdd'];
            if($model->validate()) {
                $ward = new Ward();

                $ward->enterprise_id = $model->enterprise;
                $ward->name = $model->name;

                if($ward->save()) {
                    echo CJSON::encode(array('success' => true,
                                             'text' => 'Новое отделение успешно добавлено.'));
                }
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionGet() {
        try {
            $connection = Yii::app()->db;
            $wards = $connection->createCommand()
                ->select('mw.*, e.shortname as enterprise_name')
                ->from('mis.wards mw')
                ->join('mis.enterprise_params e', 'mw.enterprise_id = e.id')
                ->queryAll();
            echo CJSON::encode($wards);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>