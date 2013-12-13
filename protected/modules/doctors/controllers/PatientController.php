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
}
?>