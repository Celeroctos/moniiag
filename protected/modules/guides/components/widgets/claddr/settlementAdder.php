<?php
class settlementAdder extends CWidget {
    public $model = null;
    public function run() {
        $this->model = new FormCladrSettlementAdd();
        $this->render('application.modules.guides.components.widgets.views.claddr.settlementAdder', array(
            'model' => $this->model
        ));
    }
}
?>