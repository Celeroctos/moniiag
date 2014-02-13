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

    public function actionGetLikesAndDistrib($id) {
        $model = new LikeDiagnosis();
        $diagnosisRows = $model->getRows(false, $id); // Получить предпочтения по врачу
        $modelDistrib = new DistribDiagnosis();
        $diagnosisDistribRows = $modelDistrib->getRows(false, $id); // Получить предпочтения по врачу
        echo CJSON::encode(array('success' => true,
                                 'data' => array(
                                     'likes' => $diagnosisRows,
                                    // 'distrib' => $diagnosisDistribRows
                                     'employees' => $this->getEmployeesPerSpec($id)
                                 )
                            )
        );
    }


    private function getEmployeesPerSpec($medworkerId) {
        $specEmployees = Employee::model()->getEmployeesPerSpec($medworkerId);
        return $specEmployees;
    }

    public function actionGetDistrib($employeeid) {
        $modelDistrib = new DistribDiagnosis();
        $diagnosisDistribRows = $modelDistrib->getRows(false, $employeeid); // Получить предпочтения по врачу
        echo CJSON::encode(array(
                'success' => true,
                'data' => $diagnosisDistribRows
            )
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

    public function actionSetDistrib() {
        if(!isset($_GET['employee_id'], $_GET['diagnosis_ids'])) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array())
            );
            exit();
        }
        // В противном случае, устанавливаем все, которые могут быть установлены
        // Удаляем все, уже установленные
        DistribDiagnosis::model()->deleteAll('employee_id = :employee_id', array(':employee_id' => $_GET['employee_id']));
        $diagnosis = CJSON::decode($_GET['diagnosis_ids']);

        foreach($diagnosis as $dia) {
            $distrib = new DistribDiagnosis();
            $distrib->employee_id = $_GET['employee_id'];
            $distrib->mkb10_id = $dia;
            if(!$distrib->save()) {
                echo CJSON::encode(array('success' => false,
                                         'error' => 'Не могу сохранить диагноз!')
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

    public function actionDistribView() {
        $this->render('distrib', array(

        ));
    }

    public function actionMkb10View() {
        $this->render('mkb10', array());
    }
}