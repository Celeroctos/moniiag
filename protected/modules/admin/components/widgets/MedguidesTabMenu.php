<?php
class MedguidesTabMenu extends CWidget {
    // Получить список созданных в админке справочников контролов
    public function getGuidesList() {
       $guidesList = array();
       $medguideModel = new MedcardGuide();
       $guidesList = $medguideModel->getRows(false, 'name', 'asc');
       return $guidesList;
    }

    public function run() {
        $guidesList = $this->getGuidesList();
        $currentGuide = $this->getCurrentGuide($guidesList);

        $this->render('application.modules.admin.components.widgets.views.MedguidesTabMenu', array(
            'controller' => strtolower($this->controller->getId()),
            'module' => $this->controller->getModule() != null ? strtolower($this->controller->getModule()->getId()) : null,
            'action' => $this->controller->getAction() != null ? strtolower($this->controller->getAction()->getId()) : $this->controller->defaultAction,
            'tabs' => $guidesList,
            'current' => $currentGuide
        ));
    }

    public function getCurrentGuide($guidesList) {
        if(isset($_GET['guideid'])) {
            $currentGuide = -1;
            foreach($guidesList as $index => $guide) {
                if($guide['id'] == $_GET['guideid']) {
                    $currentGuide = $guide['id'];
                    break;
                }
            }
        } else {
//            if(count($guidesList) > 0) {
//                $currentGuide = $guidesList[0]['id']; // Отмеченный первый справочник
//            } else {
                $currentGuide = -1;
//            }
        }
        return $currentGuide;
    }
}

?>