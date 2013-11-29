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
        if(Yii::app()->user->isGuest) {
            $req = new CHttpRequest();
            $req->redirect(CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/'));
        }
        $this->render('index', array(
            'currentPatient' => $this->currentPatient,
            'patients' => $this->getPregnantPatients(),
            'currentPatient' => $this->currentPatient
        ));
    }

    public function getPregnantPatients() {
        $filters = array(
            'groupOp' => 'AND',
            'rules' => array(
                array(
                    'field' => 'userid',
                    'op' => 'eq',
                    'data' => Yii::app()->user->id
                )
            )
        );
        $pregnantModel = new Pregnant();
        $pregnants = $pregnantModel->getRows($filters);

        return $pregnants;
    }


}
?>