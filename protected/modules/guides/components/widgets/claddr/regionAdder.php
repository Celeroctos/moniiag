<?php
class regionAdder extends CWidget {
    public $model = null;
    public function run() {
        $this->model = new FormCladrRegionAdd();
        $this->render('application.modules.guides.components.widgets.views.claddr.regionAdder', array(
            'model' => $this->model
        ));
    }
}
?>