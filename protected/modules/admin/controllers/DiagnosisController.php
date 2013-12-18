<?php
class DiagnosisController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    // Получить страницу с шаблоном "любимых" диагнозов
    public function actionAllView() {
        $this->render('index', array(

        ));
    }

    public function actionGetone($id) {
        $model = new LikeDiagnosis();
        $diagnosisRow = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $diagnosisRow)
        );
    }
}