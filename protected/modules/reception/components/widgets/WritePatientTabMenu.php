<?php
class WritePatientTabMenu extends CWidget {
    public $callcenter = false;
    public $waitingLine = false;

    public function run() {
        if(isset($_GET['waitingline']) && $_GET['waitingline'] == 1) {
            $this->waitingLine = true;
        }
        $this->render('application.modules.reception.components.widgets.views.WritePatientTabMenu', array(
            'controller' => strtolower($this->controller->getId()),
            'module' => $this->controller->getModule() != null ? strtolower($this->controller->getModule()->getId()) : null,
            'action' => $this->controller->getAction() != null ? strtolower($this->controller->getAction()->getId()) : $this->controller->defaultAction
        ));
    }
}

?>