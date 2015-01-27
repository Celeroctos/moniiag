<?php
class MDirectionsController extends Controller {
    public function actionAdd() {
        if(!isset($_POST['FormDirectionForPatientAdd'])) {
            exit(CJSON::encode(
                array(
                    'success' => false
                )
            ));
        }

        $model = new FormDirectionForPatientAdd();
        $model->attributes = $_POST['FormDirectionForPatientAdd'];
        if(!$model->validate()) {
            exit(CJSON::encode(
                array(
                    'success' => false,
                    'errors' => $model->errors
                )
            ));
        }

        $oms = Oms::model()->findByPk($model->omsId);
        if(!$oms) {
            throw new Exception('Нет ОМС с ID = '.$model->omsId);
        }

        // Create patient from data, if not exists
        $patientModel = new Patient();
        $patient = $patientModel->getPatient($model, $oms);
        // Create medical direction for patient
        $mDirection = new MDirection();
        $mDirection = $mDirection->create($patient, $model);

        echo CJSON::encode(array(
            'success' => true
        ));
    }

    public function actionGet($omsId = false) {
        if(!$omsId) {
            // Get all directions
        } else {
            $patientPerOms = Patient::model()->find('oms_id = :oms_id', array(':oms_id' => $omsId));
            if(!$patientPerOms) {
                throw new Exception('Пациент с ОМС ID = '.$omsId.' не найден ');
            }
            $directions = MDirection::model()->findAllPerPatientId($patientPerOms->id);
            echo CJSON::encode(array(
                'success' => true,
                'directions' => $directions
            ));
        }
    }
}

?>