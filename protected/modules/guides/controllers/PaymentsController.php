<?php
class PaymentsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormPaymentAdd;
			
            $this->render('view', array(
                'model' => $formAddEdit
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormPaymentAdd();
        if(isset($_POST['FormPaymentAdd'])) {
            $model->attributes = $_POST['FormPaymentAdd'];
            if($model->validate()) {
                $payment = Payment::model()->findByPk($model->id);
                $this->addEditModel($payment, $model, 'Тип оплаты успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }
	
    public function actionDelete($id) {
        try {
            $payment = Payment::model()->deleteByPk($id);
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Тип оплаты успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModel($payment, $model, $msg) {
        $payment->name = $model->name;
        $payment->tasu_string = $model->tasuString;

        if($payment->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionAdd() {
        $model = new FormPaymentAdd();
        if(isset($_POST['FormPaymentAdd'])) {
            $model->attributes = $_POST['FormPaymentAdd'];
            if($model->validate()) {
                $payment = new Payment();
                $this->addEditModel($payment, $model, 'Тип оплаты успешно добавлен.');
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

            $model = new Payment();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $payments = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $payments,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Payment();
        $payment = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $payment)
        );
    }
}

?>