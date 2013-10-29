<?php
class SideMenu extends CWidget {
    public function run() {
        $this->render('application.components.widgets.views.SideMenu', array(
            'controller' => strtolower($this->controller->getId()),
            'module' => $this->controller->getModule() != null ? strtolower($this->controller->getModule()->getId()) : null,
            'action' => $this->controller->getAction() != null ? strtolower($this->controller->getAction()->getId()) : $this->controller->defaultAction
        ));
    }
}

?>