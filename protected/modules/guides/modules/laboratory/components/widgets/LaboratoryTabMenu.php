<?php
class LaboratoryTabMenu extends CWidget {
    public function run() {
        $this->render('application.modules.guides.modules.laboratory.components.widgets.views.LaboratoryTabMenu', array(
            'controller' => strtolower($this->controller->getId()),
            'module' => $this->controller->getModule() != null ? strtolower($this->controller->getModule()->getId()) : null,
            'action' => $this->controller->getAction() != null ? strtolower($this->controller->getAction()->getId()) : $this->controller->defaultAction
        ));
    }
}

?>