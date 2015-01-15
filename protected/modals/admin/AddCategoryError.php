<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-19
 * Time: 16:47
 */

class AddCategoryError extends ModalWindow {

    /**
     * @return bool - If modal window is error, then override this method
     *      and return true
     */
    public function isError() {
        return true;
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
        return "Ошибка!";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "errorAddCategoriePopup";
    }
}