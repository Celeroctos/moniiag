<?php
class DiagnosisController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    // Получить страницу с шаблоном "любимых" диагнозов
    public function actionAllView() {
        $this->render('index', array(

        ));
    }

    public function actionGetLikes($id) {
        $model = new LikeDiagnosis();
        $diagnosisRows = $model->getRows(false, $id); // Получить предпочтения по врачу
        echo CJSON::encode(array('success' => true,
                                 'data' => $diagnosisRows)
        );
    }

    public function actionSetLikes() {
        if(!isset($_GET['medworker_id'], $_GET['diagnosis_ids'])) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array())
            );
            exit();
        }
        // В противном случае, устанавливаем все, которые могут быть установлены
        // Удаляем все, уже установленные
        LikeDiagnosis::model()->deleteAll('medworker_id = :medworker_id', array(':medworker_id' => $_GET['medworker_id']));
        $diagnosis = CJSON::decode($_GET['diagnosis_ids']);
      //  var_dump($diagnosis);
     //  exit();
        foreach($diagnosis as $dia) {
            $like = new LikeDiagnosis();
            $like->medworker_id = $_GET['medworker_id'];
            $like->mkb10_id = $dia['id'];
            if(!$like->save()) {
                echo CJSON::encode(array('success' => false,
                                         'error' => 'Не могу сохранить любимый диагноз!')
                );
                exit();
            }
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => array())
        );
    }

    public function actionGetone($id) {

    }
}