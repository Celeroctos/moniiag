<?php
class PatientController extends Controller {
    public function actionGetHistoryMedcard() {
        if(!Yii::app()->request->isAjaxRequest) {
            exit('Error!');
        }
        if(!isset($_GET['date'], $_GET['medcardid'])) {
            echo CJSON::encode(array('success' => true,
                                     'data' => 'Не хватает данных для запроса!'));
        }
        $categorieWidget = $this->createWidget('application.modules.doctors.components.widgets.CategorieViewWidget');
        $categorieWidget->createFormModel();
        $historyArr = $categorieWidget->getFieldsHistoryByDate($_GET['date'], $_GET['medcardid']); // Получаем поля для всех полей относительно хистори
        //var_dump($historyArr);
        echo CJSON::encode(array('success' => 'true',
                                 'data' => $historyArr));
    }


    public function actionSaveDiagnosis() {
        if(!isset($_GET['greeting_id'])) {
            exit('Не выбран приём!');
        }
        // Удалить предыдущие поставленные диагнозы
        PatientDiagnosis::model()->deleteAll('greeting_id = :greeting_id', array(':greeting_id' => $_GET['greeting_id']));

        if(isset($_GET['primary'])) {
            $primary = CJSON::decode($_GET['primary']);
            foreach($primary as $id) {
                $row = new PatientDiagnosis();
                $row->mkb10_id = $id;
                $row->greeting_id = $_GET['greeting_id'];
                $row->type = 0; // Первичный диагноз
                if(!$row->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'error' => 'Не могу сохранить первичный диагноз!'));
                    exit();
                }
            }
        }
        if(isset($_GET['secondary'])) {
            $secondary = CJSON::decode($_GET['secondary']);
            foreach($secondary as $id) {
                $row = new PatientDiagnosis();
                $row->mkb10_id = $id;
                $row->greeting_id = $_GET['greeting_id'];
                $row->type = 1; // Сотпутствующий диагноз
                if(!$row->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'error' => 'Не могу сохранить сопутствующий диагноз!'));
                    exit();
                }
            }
        }
        if(isset($_GET['note']) && trim($_GET['note']) != '') {
            $greeting = SheduleByDay::model()->findByPk($_GET['greeting_id']);
            if($greeting != null) {
                $greeting->note = $_GET['note'];
                $greeting->save();
            }
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => array()));
    }
}
?>