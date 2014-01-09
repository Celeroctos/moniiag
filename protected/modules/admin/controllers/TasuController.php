<?php
class TasuController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';
    // Просмотр страницы интеграции с ТАСУ
    public function actionView() {
        $this->render('view', array());
    }
}
?>