<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-20
 * Time: 12:08
 */

class DesignTemplate extends ModalWindow {

    /**
     * @return bool - If modal window should have custom width then override
     *      this method and return your value (for example, 1000px or 70%)
     */
    public function customWidth() {
        return "90%";
    }

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



        return array(
            'categories' => array(),
            'elements' => array()
        );
    }

    /**
     * @return string - Header's title, which will be in header
     */
    function getTitle() {
        return "Дизайнер шаблонов";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "designTemplatePopup";
    }
}