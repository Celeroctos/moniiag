<?php
class MedcardFormWidget extends CWidget {
    public $model;
    public $form;
    public $privilegesList;
    public $showEditIcon = false;
    public $template = null;
    public function run() {
        if($this->template != null) {
            $this->render($this->template, array(
                'form' => $this->form,
                'model' => $this->model,
                'privilegesList' => $this->privilegesList,
                'showEditIcon' => $this->showEditIcon
            ));
        } else {

        }
    }
}

?>