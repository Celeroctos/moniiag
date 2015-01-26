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

        // Create patient from data, if not exists
        $patient = new Patient();
        $patient->getPatient($model);

        $answer = array();
        echo CJSON::encode(array(
            'success' => true,
            'data' => $answer
        ));
    }
}

?>