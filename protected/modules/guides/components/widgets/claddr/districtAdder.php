<?php
class districtAdder extends CWidget {
    public $model = null;
    public $printCodeField = 1;
    public function run() {
        $this->model = new FormCladrDistrictAdd();
        $this->render('application.modules.guides.components.widgets.views.claddr.districtAdder', array(
            'model' => $this->model,
            'printCodeField' => $this->printCodeField
        ));
    }
}
?>