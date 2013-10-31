<?php
class EmployeesController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormEmployeeAdd;

            // Список должностей
            $connection = Yii::app()->db;
            $postsListDb = $connection->createCommand()
                ->select('m.*')
                ->from('mis.medpersonal m')
                ->queryAll();

            $postsList = array();
            foreach($postsListDb as $value) {
                $postsList[(string)$value['id']] = $value['name'];
            }

            // Список званий
            $titulsListDb = $connection->createCommand()
                ->select('t.*')
                ->from('mis.tituls t')
                ->queryAll();

            $titulsList = array();
            foreach($titulsListDb as $value) {
                $titulsList[(string)$value['id']] = $value['name'];
            }

            // Список отделений
            $wardsListDb = $connection->createCommand()
                ->select('w.*')
                ->from('mis.wards w')
                ->queryAll();

            $wardsList = array();
            foreach($wardsListDb as $value) {
                $wardsList[(string)$value['id']] = $value['name'];
            }


            // Список степеней
            $degreesListDb = $connection->createCommand()
                ->select('d.*')
                ->from('mis.degrees d')
                ->queryAll();

            $degreesList = array();
            foreach($degreesListDb as $value) {
                $degreesList[(string)$value['id']] = $value['name'];
            }

            $this->render('view', array(
                'model' => $formAddEdit,
                'titulsList' => $titulsList,
                'postsList' => $postsList,
                'wardsList' => $wardsList,
                'degreesList' => $degreesList,
                'contactCodesList' => array()
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormEmployeeAdd();
        if(isset($_POST['FormEmployeeAdd'])) {
            $model->attributes = $_POST['FormEmployeeAdd'];
            if($model->validate()) {
                $employee = Employee::model()->find('id=:id', array(':id' => $_POST['FormEmployeeAdd']['id']));
                $this->addEditModel($employee, $model, 'Медицинский персонал успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionDelete() {


    }

    private function addEditModel($employee, $model, $msg) {

        $employee->first_name = $model->firstName;
        $employee->middle_name = $model->middleName;
        $employee->last_name = $model->lastName;
        $employee->post_id = $model->postId;
        $employee->tabel_number = $model->tabelNumber;
        $employee->contact_code = $model->contactCode;
        $employee->degree_id = $model->degreeId;
        $employee->titul_id = $model->titulId;
        $employee->date_begin = $model->dateBegin;
        $employee->date_end = $model->dateEnd;
        $employee->ward_code = $model->wardCode;

        if($employee->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }

    public function actionAdd() {
        $model = new FormEmployeeAdd();
        if(isset($_POST['FormEmployeeAdd'])) {
            $model->attributes = $_POST['FormEmployeeAdd'];
            if($model->validate()) {
                $employee = new Employee();
                $this->addEditModel($employee, $model, 'Медицинский персонал успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }

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
                ->leftJoin('mis.degrees de', 'd.degree_id = de.id')
                ->leftJoin('mis.tituls t', 'd.titul_id = t.id')
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

    public function actionGetone($id) {
        $model = new Employee();
        $employee = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $employee)
        );
    }

}

?>