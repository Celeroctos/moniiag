<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-19
 * Time: 16:54
 */

class AddGuide extends ModalWindow {

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
            'model' => new FormGuideAdd()
        );
    }

    /**
     * @return string - Header's title, which will be in header
     */
    function getTitle() {
        return "Добавить справочник";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "addGuidePopup";
    }
}