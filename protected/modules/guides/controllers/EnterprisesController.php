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

    }

    public function actionDelete() {


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
            $connection = Yii::app()->db;
            $enterprises = $connection->createCommand()
                            ->select('ep.*, et.name as enterprise_type')
                            ->from('mis.enterprise_params ep')
                            ->join('mis.enterprise_types et', 'ep.type = et.id')
                            ->queryAll();

            foreach($enterprises as $key => &$enterprise) {
                $enterprise['requisits'] = 'Банк '.$enterprise['bank'].', '.$enterprise['bank_account'].', ИНН '.$enterprise['inn'].', КПП '.$enterprise['kpp'];
            }
            echo CJSON::encode($enterprises);

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>