<?php
class ContactsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';
    protected $contactTypes = array(
        'Электронная почта',
        'Домашний телефон',
        'Мобильный телефон',
        'Домашний адрес'
    );

    public function actionView() {
        try {
            // Модель формы для добавления и редактирования записи
            $formAddEdit = new FormContactAdd;
            $formFilter = new FormContactFilter;

            $enterprises = new Enterprise();
            $enterprisesArr = array('-1' => 'Нет');
            foreach($enterprises->getRows(false) as $enterprise) {
                $enterprisesArr[$enterprise['id']] = $enterprise['shortname'];
            }

            if(isset($_GET['employeeid'], $_GET['enterpriseid'], $_GET['wardid']) && trim($_GET['employeeid']) != '' && trim($_GET['wardid']) != '' && trim($_GET['enterpriseid']) != '') {
                $ward = new Ward();
                $employee = new Employee();

                $wardsList = array('-1' => 'Нет');
                $employeesList = array('-1' => 'Нет');

                $wardsListDb = $ward->getByEnterprise($_GET['enterpriseid']);
                foreach($wardsListDb as $ward) {
                    $wardsList[$ward['id']] = $ward['name'];
                }
                $employeesListDb = $employee->getByWard($_GET['wardid']);
                foreach($employeesListDb as $employee) {
                    $employeesList[$employee['id']] = $employee['last_name'].' '.$employee['first_name'].' '.$employee['middle_name'];
                }

                $selectedEnterprise = $_GET['enterpriseid'];
                $selectedEmployee = $_GET['employeeid'];
                $selectedWard = $_GET['wardid'];
            } else {
                $wardsList = array('-1' => 'Нет');
                $employeesList = array('-1' => 'Нет');
                $selectedEnterprise = -1;
                $selectedEmployee = -1;
                $selectedWard = -1;
            }

            $this->render('view', array(
                'model' => $formAddEdit,
                'modelFilter' => $formFilter,
                'contactsTypesList' => $this->contactTypes,
                'enterprisesList' => $enterprisesArr,
                'wardsList' => $wardsList,
                'employeesList' => $employeesList,
                'selectedEnterprise' => $selectedEnterprise,
                'selectedWard' => $selectedWard,
                'selectedEmployee' => $selectedEmployee
            ));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormContactAdd();
        if(isset($_POST['FormContactAdd'])) {
            $model->attributes = $_POST['FormContactAdd'];
            if($model->validate()) {
                $contact = Contact::model()->findByPk($_POST['FormContactAdd']['id']);
                $this->addEditModel($contact, $model, 'Контакт успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionDelete($id) {
        try {
            $contact = Contact::model()->findByPk($id);
            $contact->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Контакт успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionAdd() {
        $model = new FormContactAdd();
        if(isset($_POST['FormContactAdd'])) {
            $model->attributes = $_POST['FormContactAdd'];
            if($model->validate()) {
                $contact = new Contact();
                $contact->employee_id = $model->employeeId;

                $this->addEditModel($contact, $model, 'Новый контакт успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    private function addEditModel($contact, $model, $msg) {
        $contact->type = $model->type;
        $contact->contact_value = $model->contactValue;

        if($contact->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
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

            // Фильтры "в разрезе"
            // Учреждение
            if(isset($_GET['enterpriseid']) && trim($_GET['enterpriseid']) != '' && trim($_GET['enterpriseid']) != -1) {
                $enterpriseId = CJSON::decode($_GET['enterpriseid']);
            } else {
                $enterpriseId = false;
            }

            // Отделение
            if(isset($_GET['wardid']) && trim($_GET['wardid']) != '' && trim($_GET['wardid']) != -1) {
                $wardId = CJSON::decode($_GET['wardid']);
            } else {
                $wardId = false;
            }

            // Сотрудник
            if(isset($_GET['employeeid']) && trim($_GET['employeeid']) != '' && trim($_GET['employeeid']) != -1) {
                $employeeId = CJSON::decode($_GET['employeeid']);
            } else {
                $employeeId = false;
            }


            $model = new Contact();
            $num = $model->getRows($filters, false, false, false, false, $enterpriseId, $wardId, $employeeId);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $contacts = $model->getRows($filters, $sidx, $sord, $start, $rows, $enterpriseId, $wardId, $employeeId);

            foreach($contacts as $key => &$contact) {
                $contact['fio'] = $contact['first_name'].' '.$contact['middle_name'].' '.$contact['last_name'];
                $contact['type'] = $this->contactTypes[$contact['type']];
            }

            echo CJSON::encode(
                array('rows' => $contacts,
                      'total' => $totalPages,
                      'records' => count($num))
            );

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Contact();
        $contact = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $contact)
        );
    }

    public function actionFilter() {
        echo CJSON::encode(array('success' => 'true',
                                 'data' => array()));
    }
}

?>