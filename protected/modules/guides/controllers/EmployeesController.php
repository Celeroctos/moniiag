<?php
class EmployeesController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        $this->render('view', array());
    }

    public function actionEdit() {

    }

    public function actionDelete() {


    }

    public function actionGet() {
        try {
            $connection = Yii::app()->db;
            $employees = $connection->createCommand()
                ->select('d.*,
                          m.name as post,
                          de.name as degree,
                          t.name as titul,
                          w.name as ward
                          ')
                ->from('mis.doctors as d')
                ->join('mis.medpersonal m', 'd.post_id = m.id')
                ->join('mis.degrees de', 'd.degree_id = de.id')
                ->join('mis.tituls t', 'd.titul_id = t.id')
                ->join('mis.wards w', 'd.ward_code = w.id')
                ->queryAll();

            foreach($employees as $key => &$employee) {
                $employee['fio'] = $employee['first_name'].' '.$employee['middle_name'].' '.$employee['last_name'];
            }

            echo CJSON::encode($employees);

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>