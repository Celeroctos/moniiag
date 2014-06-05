<?php
class EnterprisesController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormEnterpriseAdd;

            // Список вариантов для типов учреждений
            $connection = Yii::app()->db;
            $typesListDb = $connection->createCommand()
                ->select('et.*')
                ->from('mis.enterprise_types et')
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
        $model = new FormEnterpriseAdd();
        if(isset($_POST['FormEnterpriseAdd'])) {
            $model->attributes = $_POST['FormEnterpriseAdd'];
            if($model->validate()) {
                $enterprise = Enterprise::model()->find('id=:id', array(':id' => $_POST['FormEnterpriseAdd']['id']));
                $this->addEditModel($enterprise, $model, 'Учреждение успешно отредактировано.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    private function addEditModel($enterprise, $model, $msg) {

        $enterprise->address_fact = $model->addressFact;
        $enterprise->address_jur = $model->addressJur;
        $enterprise->phone = $model->phone;
        $enterprise->shortname = $model->shortName;
        $enterprise->fullname = $model->fullName;
        $enterprise->bank = $model->bank;
        $enterprise->bank_account = $model->bankAccount;
        $enterprise->inn = $model->inn;
        $enterprise->kpp = $model->kpp;
        $enterprise->type = $model->type;
		$enterprise->ogrn = $model->ogrn;

        if($enterprise->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionDelete($id) {
        try {
            $enterprise = Enterprise::model()->findByPk($id);
            $enterprise->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Учреждение успешно удалено.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionAdd() {
        $model = new FormEnterpriseAdd();
        if(isset($_POST['FormEnterpriseAdd'])) {
            $model->attributes = $_POST['FormEnterpriseAdd'];
            if($model->validate()) {
                $enterprise = new Enterprise();

                $enterprise->address_fact = $model->addressFact;
                $enterprise->address_jur = $model->addressJur;
                $enterprise->phone = $model->phone;
                $enterprise->shortname = $model->shortName;
                $enterprise->fullname = $model->fullName;
                $enterprise->bank = $model->bank;
                $enterprise->bank_account = $model->bankAccount;
                $enterprise->inn = $model->inn;
                $enterprise->kpp = $model->kpp;
                $enterprise->type = $model->type;
				$enterprise->ogrn = $model->ogrn;
                if($enterprise->save()) {
                    echo CJSON::encode(array('success' => true,
                                             'text' => 'Новое учреждение успешно добавлено.'));
                }
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

           // var_dump($filters);
            $model = new Enterprise();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $order = array(
                'requisits' => 'bank, bank_account, inn, kpp'
            );
            if(isset($order[$sidx])) {
                $sidx = $order[$sidx];
            }

            $enterprises = $model->getRows($filters, $sidx, $sord, $start, $rows);

            foreach($enterprises as $key => &$enterprise) {
                $enterprise['requisits'] = 'Банк '.$enterprise['bank'].', '.$enterprise['bank_account'].', ИНН '.$enterprise['inn'].', КПП '.$enterprise['kpp'];
            }

            echo CJSON::encode(
                array('rows' => $enterprises,
                      'total' => $totalPages,
                      'records' => count($num))
            );

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Enterprise();
        $enterprise = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' =>$enterprise)
                        );
    }
}

?>