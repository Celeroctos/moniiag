<?php
class PrintController extends Controller {
    public $layout = 'print';
    public $responseData = array();

    public function actionGetForDayFish()
    {

        // Выводим шапку с надписью "отчёт за день"
        $this->render('workForDayPrint', array(
        ));
    }

}