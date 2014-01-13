<?php
class TasuController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';
    // Просмотр страницы интеграции с ТАСУ
    public function actionView() {
        $this->render('view', array());
    }

    // Загрузка ОМС
    public function actionUploadOms() {
        // Режим подсчёта, сколько осталось грузить файла
        if(isset($_GET['onlysayuploadedpart']) && $_GET['onlysayuploadedpart'] == 1) {
            echo CJSON::encode(array('success' => true,
                                     'data' => array(
                                         'uploaded' => 100,
                                         'filesize' => 100
                                     )));
            exit();
        }
    }
}
?>