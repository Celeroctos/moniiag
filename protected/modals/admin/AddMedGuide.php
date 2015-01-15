<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-19
 * Time: 17:27
 */

class AddMedGuide extends ModalWindow {

    private $layout = 'application.modules.admin.views.layouts.medguides';

    /**
     * @return string - path to modal window's view (renderer). You can simply
     *      return class's name via __CLASS__ macros
     */
    function getView() {
        return __CLASS__;
    }

    /**
     * @return array - data, which wll be sent into view renderer
     */
    function getData() {
        $medguidesTabWidget = CWidget::createWidget('application.modules.admin.components.widgets.MedguidesTabMenu');
        $currentGuide = $medguidesTabWidget->getCurrentGuide($medguidesTabWidget->getGuidesList());
        return array(
            'model' => new FormValueAdd(),
            'currentGuideId' => $currentGuide
        );
    }

    /**
     * @return string - Header's title, which will be in header
     */
    function getTitle() {
        return "Добавить значение справочника";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "addMedGuidePopup";
    }
}