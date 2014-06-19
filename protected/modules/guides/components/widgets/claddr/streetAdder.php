<?php
class streetAdder extends CWidget {
    public $model = null;
    public function run() {
        $this->model = new FormCladrStreetAdd();
        $this->render('application.modules.guides.components.widgets.views.claddr.streetAdder', array(
            'model' => $this->model
        ));
    }
}
?>