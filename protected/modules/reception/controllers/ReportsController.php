<?php
class ReportsController extends Controller {
    public $layout = 'application.views.layouts.index';

    public function actionView() {
        $this->render('view', array());
    }

    public function actionForDayView()
    {
       // var_dump('!');
       // exit();
       // echo ("Здравствуй, мир!");

        $this->render( 'workForDay',array());

    }

    public function actionGetForDayFish()
    {

        // Выводим шапку с надписью "отчёт за день"
        $this->render('workForDay', array(
        ));
    }

    public function actionGetReportForDay()
    {
      //  var_dump($_GET);
      //  exit();


        $rows = $_GET['rows'];
        $page = $_GET['page'];
        $sidx = $_GET['sidx'];
        $sord = $_GET['sord'];

        // Поидее - просто надо не давать нажимать на кнопку "Вывести" при незаполненной дате
        if(!isset($_GET['date'])) {
                exit('Нехватка данных для запроса.');
        }
        $model = new Patient();
        $num = $model->getCardNumbersByDate($_GET['date']);
       // var_dump(count($num));
       // exit();
        if (count($num)!=0)
        {
            $totalPages = 1;
            $start = 0;
            $rows = count($num);

            $reportItems = $model->getRegistryWorkForDay($_GET['date'], $sidx, $sord, $start, $rows);
        }
        else
        {
            $totalPages = 0;
            $start = 0;
            $reportItems = array();

        }
        echo CJSON::encode(
            array('rows' => $reportItems,
                'total' => $totalPages,
                'records' => count($num))
        );


    }
}
?>