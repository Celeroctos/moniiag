<?php
class regionAdder extends CWidget {
    public $model = null;
    public $printCodeField = 1;
    public function run() {
        $this->model = new FormCladrRegionAdd();
        $this->render('application.modules.guides.components.widgets.views.claddr.regionAdder', array(
            'model' => $this->model,
            'printCodeField' => $this->printCodeField
        ));
    }
}
?>