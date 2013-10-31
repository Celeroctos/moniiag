<?php
class MedworkersController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormMedworkerAdd;

            // Список вариантов для типов медработников
            $connection = Yii::app()->db;
            $typesListDb = $connection->createCommand()
                ->select('mt.*')
                ->from('mis.medpersonal_types mt')
                ->queryAll();

            $typesList = array();
            foreach($typesListDb as $value) {
                $typesList[(string)$value['id']] = $value['name'];
            }

            $this->render('view', array(
                'model' => $formAddEdit,
                'typesList' => $typesList
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormMedworkerAdd();
        if(isset($_POST['FormMedworkerAdd'])) {
            $model->attributes = $_POST['FormMedworkerAdd'];
            if($model->validate()) {
                $medworker = Medworker::model()->find('id=:id', $_POST['FormMedworkerAdd']['id']);

                $this->addEditModel($medworker, $model, 'Тип работника успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionDelete() {


    }

    public function addEditModel($medworker, $model, $msg) {
        $medworker->name = $model->name;
        $medworker->type = $model->type;

        if($medworker->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionAdd() {
        $model = new FormMedworkerAdd();
        if(isset($_POST['FormMedworkerAdd'])) {
            $model->attributes = $_POST['FormMedworkerAdd'];
            if($model->validate()) {
                $medworker = new Medworker();

                $this->addEditModel($medworker, $model, 'Новый тип работника успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionGet() {
        try {
            $connection = Yii::app()->db;
            $medpersonals = $connection->createCommand()
                ->select('m.*, mt.name as medpersonal_type')
                ->from('mis.medpersonal m')
                ->join('mis.medpersonal_types mt', 'm.type = mt.id')
                ->queryAll();

            echo CJSON::encode($medpersonals);

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function actionGetone($id) {
        $model = new Medworker();
        $medworker = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $medworker)
        );
    }
}

?>