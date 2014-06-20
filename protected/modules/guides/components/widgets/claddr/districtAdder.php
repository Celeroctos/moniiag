<?php
class districtAdder extends CWidget {
    public $model = null;
    public function run() {
        $this->model = new FormCladrDistrictAdd();
        $this->render('application.modules.guides.components.widgets.views.claddr.districtAdder', array(
            'model' => $this->model
        ));
    }
}
?>