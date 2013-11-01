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

            $this->render('view', array(
                'model' => $formAddEdit,
                'contactsTypesList' => $this->contactTypes
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
                $ward = Contact::model()->find('id=:id', $_POST['FormContactAdd']['id']);

                $this->addEditModel($ward, $model, 'Контакт успешно отредактирован.');
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
            $connection = Yii::app()->db;
            $contacts = $connection->createCommand()
                ->select('c.*, d.first_name, d.middle_name, d.last_name')
                ->from('mis.contacts c')
                ->leftJoin('mis.doctors d', 'd.contact_code = c.id')
                ->queryAll();

            foreach($contacts as $key => &$contact) {
                $contact['fio'] = $contact['first_name'].' '.$contact['middle_name'].' '.$contact['last_name'];
                $contact['type'] = $this->contactTypes[$contact['type']];
            }


            echo CJSON::encode($contacts);
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
}

?>