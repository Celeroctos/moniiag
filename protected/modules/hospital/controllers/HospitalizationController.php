<?php 
class HospitalizationController extends Controller {
	public $layout = 'application.modules.hospital.views.layouts.index';
	public function actionView() {
		$this->render('index', array(
            'model' => new FormHospitalizationDateChange('view'),
            'writeTypes' => array('По записи', 'Живая очередь')
        ));
	}

    public function actionChangeHospitalizationDate() {
        $formModel = new FormHospitalizationDateChange('edit');
        $formModel->attributes = Yii::app()->request->getParam('FormHospitalizationDateChange');
        if(!$formModel->validate()) {
            echo CJSON::encode(array(
                'success' => false,
                'errors' => $formModel->errors
            ));
            exit();
        }
        $answer['gridId'] =  $formModel->grid_id;

        $model = MDirection::model()->findByPk($formModel->id);
        if($model) {
            $model->hospitalization_date = $formModel->hospitalization_date;
            $model->write_type = $formModel->write_type;
            echo CJSON::encode($answer + array(
                'success' => $model->save()
            ));
            exit();
        }
        echo CJSON::encode($answer + array(
            'success' => false
        ));
    }

    public function actionDismissHospitalization() {
        $formModel = new FormHospitalizationDateChange('edit');
        $form = Yii::app()->request->getParam('FormHospitalizationDateChange');

        $model = MDirection::model()->findByPk($form['id']);
        if($model) {
            $model->is_refused = 1;
            echo CJSON::encode(array(
                'success' => $model->save()
            ));
        } else {
            echo CJSON::encode(array(
                'success' => false
            ));
        }
    }

    public function actionGetDirectionData($id) {
        echo CJSON::encode(array(
            'success' => true,
            'data' => MDirection::model()->findByPk($id)
        ));
    }
}

?>