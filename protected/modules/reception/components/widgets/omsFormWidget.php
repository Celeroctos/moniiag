<?php
class OmsFormWidget extends CWidget {
    public $model;
    public $form;
    public function run() {
        $this->render('application.modules.reception.components.widgets.views.OmsFormWidget', array(
            'form' => $this->form,
            'model' => $this->model
        ));
    }
}

?>