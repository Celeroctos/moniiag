<?php
class PrechildController extends CController {
    public $layout = 'index';
    public $currentPatient = false;
    public function actionView() {
        if(isset($_GET['cardid']) && trim($_GET['cardid']) != '') {
            // Проверим, есть ли такая медкарта вообще
            $medcardFinded = Medcard::model()->findByPk($_GET['cardid']);
            if($medcardFinded != null) {
                $this->currentPatient = trim($_GET['cardid']);
            }
        }
        $this->render('index', array(
            'currentPatient' => $this->currentPatient
        ));
    }
}
?>