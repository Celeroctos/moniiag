<?php
class SheduleController extends Controller {
    public $layout = 'application.views.layouts.index';

    public function actionView() {
        $this->render('view', array());
    }

    public function actionGetShedule() {
        if((!isset($_GET['doctors']) && !isset($_GET['patients'])) || !isset($_GET['date']) || trim($_GET['date']) == '') {
            echo CJSON::encode(array('success' => false,
                                     'errors' => array(
                                         'query' => array('Не хватает данных для запроса! Проверьте введённую дату (обязательно) и одно из двух необязательных полей: врач или пациент')
                                     )));
            exit();
        }

        $sheduleElements = array();
        $mediateElements = array();

        $data = CJSON::decode($_GET['patients']);
        if(count($data) > 0) {
            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit($data, array(), $_GET['date']);
        } else {
            $data = CJSON::decode($_GET['doctors']);
            $sheduleElements = SheduleByDay::model()->getGreetingsPerQrit(array(), $data, $_GET['date']);
            $mediateElements = MediatePatient::model()->getGreetingsPerQrit(array(), $data, $_GET['date']);
        }

        $result = array();
        $num = count($sheduleElements);
        foreach($mediateElements as $element) {
           array_push($sheduleElements, $element);
        }

        // Сортируем по времени
        usort($sheduleElements, function($element1, $element2) {
            $time1 = strtotime($_GET['date'].' '.$element1['patient_time']);
            $time2 = strtotime($_GET['date'].' '.$element2['patient_time']);
            if($time1 < $time2) {
                return -1;
            } elseif($time1 > $time2) {
                return 1;
            } else {
                return 0;
            }
        });
        echo CJSON::encode(array('success' => true,
                                 'data' => $sheduleElements));
    }
}
?>