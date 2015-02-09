<?php
class PaymentsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

	/*
	 * Просмотр типов оплат
	 */
	public function actionList()
	{
		$model=new Payment('payment.search');
		
		if(isset($_GET['Payment']))
		{
			$model->attributes=Yii::app()->request->getQuery('Payment');
			$model->validate();
		}
		
		$this->render('list', [
			'model'=>$model,
		]);
	}

	public function actionUpdate($id)
	{
		$record=Payment::model()->findByPk($id);
		$record->scenario='payments.update';
		
		if($record===null)
		{
			throw new CHttpException(404, 'Обновляемый объект не найден!');
		}
		elseif(isset($_POST['Payment']))
		{
			$record->attributes=Yii::app()->request->getPost('Payment');
		
			if($record->save())
			{
				Yii::app()->user->setFlash('success', 'Успешное редактирование!');
				$this->refresh();
			}
		}
		
		$this->render('update', [
			'record'=>$record,
		]);
	}
	
	public function actionDelete($id)
	{
		$record=Payment::model()->findByPk($id);
		
		if($record===null)
		{
			throw new CHttpException(404, 'Удаляемый объект не найден');
		}
		elseif(Payment::model()->deleteByPk($id))
		{
			Yii::app()->user->setFlash('success', 'Успешное удаление!');
			$this->redirect(['payments/view']);
		}
	}
	
	public function actionCreate()
	{
		$model=new Payment('payments.create');
		
		if(isset($_POST['Payment']))
		{
			$model->attributes=Yii::app()->request->getPost('Payment');
			if($model->save())
			{
				Yii::app()->user->setFlash('success', 'Успешное добавление!');
				$this->redirect(['payments/view']);
			}
		}
		$this->render('create', ['model'=>$model]);
	}
	
//    public function actionDelete($id) {
//        try {
//            $payment = Payment::model()->deleteByPk($id);
//            echo CJSON::encode(array('success' => 'true',
//                                     'text' => 'Тип оплаты успешно удалён.'));
//        } catch(Exception $e) {
//            // Это нарушение целостности FK
//            echo CJSON::encode(array('success' => 'false',
//                                     'error' => 'На данную запись есть ссылки!'));
//        }
//    }
	
    public function actionView() 
	{
		return $this->actionList();
//        try {
//            // Модель формы для добавления и редактирования записи
//            $formAddEdit = new FormPaymentAdd;
//			
//            $this->render('view', array(
//                'model' => $formAddEdit
//            ));
//        } catch(Exception $e) {
//            echo $e->getMessage();
//        }
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

    private function addEditModel($payment, $model, $msg) {
        $payment->name = $model->name;
        $payment->tasu_string = $model->tasuString;
		$payment->is_default = $model->isDefault;

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
			foreach($payments as &$payment) {
				if($payment['is_default']) {
					$payment['is_default_desc'] = 'Да';
				} else {
					$payment['is_default_desc'] = 'Нет';
				}
			}

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