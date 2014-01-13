<?php
class EmployeesController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormEmployeeAdd;
            // Модель формы для фильтра
            $formFilter = new FormEmployeeFilter;

            // Список учреждений
            $connection = Yii::app()->db;
            $enterprisesListDb = $connection->createCommand()
                ->select('ep.*')
                ->from('mis.enterprise_params ep')
                ->queryAll();

            $enterprisesList = array('-1' => 'Нет');
            foreach($enterprisesListDb as $value) {
                $enterprisesList[(string)$value['id']] = $value['shortname'];
            }

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

            $wardsList = array('-1' => 'Нет');
            $wardsListForAdd = array();
            foreach($wardsListDb as $value) {
                $wardsListForAdd[(string)$value['id']] = $value['name'];
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
                'modelFilter' => $formFilter,
                'titulsList' => $titulsList,
                'postsList' => $postsList,
                'wardsList' => $wardsList,
                'wardsListForAdd' => $wardsListForAdd,
                'degreesList' => $degreesList,
                'enterprisesList' => $enterprisesList,
                'canEdit' => Yii::app()->user->checkAccess('editGuides')
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

    public function actionFilter() {
        echo CJSON::encode(array('success' => 'true',
                                 'data' => array()));
    }

    public function actionDelete($id) {
        try {
            $employee = Employee::model()->findByPk($id);
            $employee->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Сотрудник успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModel($employee, $model, $msg) {

        $employee->first_name = $model->firstName;
        $employee->middle_name = $model->middleName;
        $employee->last_name = $model->lastName;
        $employee->post_id = $model->postId;
        $employee->tabel_number = $model->tabelNumber;
        $employee->degree_id = $model->degreeId;
        $employee->titul_id = $model->titulId;
        $employee->date_begin = $model->dateBegin;
        if(!isset($_POST['notDateEnd'])) {
            $employee->date_end = $model->dateEnd;
        } else {
            $employee->date_end = null;
        }
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
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];

            // Фильтры поиска
            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new Employee();
            if(isset($_GET['enterpriseid'], $_GET['wardid'])) {
                $num = $model->getRows($_GET['enterpriseid'], $_GET['wardid'], $filters);
            } else {
                $num = $model->getRows(-1, -1, $filters);
            }

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            if(isset($_GET['enterpriseid'], $_GET['wardid'])) {
                $employees = $model->getRows($_GET['enterpriseid'], $_GET['wardid'], $filters, $sidx, $sord, $start, $rows);
            } else {
                $employees = $model->getRows(-1, -1, $filters, $sidx, $sord, $start, $rows);
            }

            foreach($employees as $key => &$employee) {
                $employee['fio'] = $employee['first_name'].' '.$employee['middle_name'].' '.$employee['last_name'];
                $employee['more_info'] = '<a href="#'.$employee['id'].'" class="more_info" title="Посмотреть подробную информацию по '.$employee['fio'].'"><span class="glyphicon glyphicon-share-alt"></span>
</a>';
                $employee['contact_see'] = '<a href="'.CHtml::normalizeUrl(Yii::app()->request->baseUrl.'/index.php/guides/contacts/view').'?enterpriseid='.$employee['enterprise_id'].'&wardid='.$employee['ward_id'].'&employeeid='.$employee['id'].'" class="more_info" title="Посмотреть контакты '.$employee['fio'].'"><span class="glyphicon glyphicon-earphone"></span>
</a>';
            }

            echo CJSON::encode(
                array('success' => true,
                      'rows' => $employees,
                      'total' => $totalPages,
                      'records' => count($num))
            );
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

    public function actionGetByWard($id) {
        $model = new Employee();
        $employees = $model->getByWard($id);

        echo CJSON::encode(array('success' => true,
                                 'data' => $employees)
        );
    }

}

?>