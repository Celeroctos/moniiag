<?php
class IndexController extends Controller {
   public $layout = 'application.views.layouts.index';

   // Стартовая модуля
   public function actionIndex() {
       $this->render('index', array());
   }

   // Добавление пациента
   public function actionAddPatient() {
       $this->render('addPatient', array());
   }

   // Запись пациента
   public function actionWritePatientStepOne() {
        $this->render('writePatient', array());
   }
}


?>