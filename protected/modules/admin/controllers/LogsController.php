<?php
class LogsController extends Controller {
    public $layout = 'application.views.layouts.index';
    public function actionView() {
        $this->render('view', array());
    }

    public function actionGet() {

    }

    public function actionDeleteTestCards() {
        $testCards = Medcard::model()->getTestOmsWithCards();
        foreach($testCards as $card) {
            Oms::model()->deleteByPk($card['id']);
            Medcard::model()->deleteByPk($card['card_number']);
        }
    }
}

?>