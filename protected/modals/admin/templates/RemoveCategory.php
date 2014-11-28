<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-27
 * Time: 12:28
 */

class RemoveCategory extends ModalWindow {

    /**
     * @return bool - If modal window should have custom width then override
     *      this method and return your value (for example, 1000px or 70%)
     */
    public function customWidth() {
        return "350px";
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
        return array();
    }

    /**
     * @return string - Header's title, which will be in header
     */
    function getTitle() {
        return "Удалить категорию из базы?";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "removeCategoryPopup";
    }
}