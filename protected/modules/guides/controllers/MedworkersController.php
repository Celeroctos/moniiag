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
                $medworker = Medworker::model()->findByPk($_POST['FormMedworkerAdd']['id']);
                $this->addEditModel($medworker, $model, 'Тип работника успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionDelete($id) {
        try {
            $medworker = Medworker::model()->findByPk($id);
            $medworker->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Медицинский работник успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function addEditModel($medworker, $model, $msg) {
        $medworker->name = $model->name;
        $medworker->type = $model->type;
        $medworker->payment_type = $model->paymentType;
        $medworker->is_for_pregnants = $model->isForPregnants;

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

            $model = new Medworker();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $medworkers = $model->getRows($filters, $sidx, $sord, $start, $rows);
            foreach($medworkers as &$medworker) {
                if($medworker['is_for_pregnants'] == null) {
                    $medworker['is_for_pregnants'] = 0;
                }
                // Тип оплаты
                if($medworker['payment_type'] == 1) {
                    $medworker['payment_type_desc'] = 'Бюджет';
                } elseif($medworker['payment_type'] == 0) {
                    $medworker['payment_type_desc'] = 'ОМС';
                } else {
                    $medworker['payment_type_desc'] = '';
                }
                $medworker['pregnants'] = $medworker['is_for_pregnants'] ? 'Да' : 'Нет';
            }

            echo CJSON::encode(
                array('rows' => $medworkers,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function actionGetone($id) {
        $model = new Medworker();
        $medworker = $model->getOne($id);
        if($medworker['is_for_pregnants'] == null) {
            $medworker['is_for_pregnants'] = 0;
        }
        if($medworker['payment_type'] == 1) {
            $medworker['payment_type_desc'] = 'Бюджет';
        } elseif($medworker['payment_type'] == 0) {
            $medworker['payment_type_desc'] = 'ОМС';
        } else {
            $medworker['payment_type_desc'] = '';
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => $medworker)
        );
    }
}

?>