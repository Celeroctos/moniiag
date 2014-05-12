<?php
class MedcardFormWidget extends CWidget {
    public $model;
    public $form;
    public $privilegesList;
    public $showEditIcon = false;
    public $notEditPassport = false; // Флаг о том, позволять ли редакьтировать некоторые данные паспорта
    public $template = null;
    public function run() {
        if($this->template != null) {
            $this->render($this->template, array(
                'form' => $this->form,
                'model' => $this->model,
                'privilegesList' => $this->privilegesList,
                'showEditIcon' => $this->showEditIcon,
                'notEditPassport' => $this->notEditPassport
            ));
        } else {

        }
    }
}

?>