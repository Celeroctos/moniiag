<?php
class IndexController extends Controller {
   public $layout = 'application.views.layouts.index';

   // Стартовая модуля
   public function actionIndex() {
       $this->render('index', array());
   }

   // Поиск пациента и его запись
   public function actionSearchPatient() {
       $this->render('searchPatient', array());
   }

   // Запись пациента
   public function actionWritePatientStepOne() {
        $this->render('writePatient', array());
   }
}


?>