<?php
class MedcardFormWidget extends CWidget {
    public $model;
    public $form;
    public $privilegesList;
    public $showEditIcon = false;
    public function run() {
        $this->render('application.modules.reception.components.widgets.views.MedcardFormWidget', array(
            'form' => $this->form,
            'model' => $this->model,
            'privilegesList' => $this->privilegesList,
            'showEditIcon' => $this->showEditIcon
        ));
    }
}

?>