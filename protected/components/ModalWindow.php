<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-19
 * Time: 14:39
 */

/**
 * Class ModalWindow - Abstract component for modal windows, it
 *      will render modal's header and wrapper. You'll have to only
 *      implement getHeader and getViewPath methods, which will
 *      return necessary information about modal window
 */

abstract class ModalWindow extends CWidget {

    /**
     * @return bool - If modal window is error, then override this method
     *      and return true
     */
    public function isError() {
        return false;
    }

    /**
     * @return string - path to modal window's view (renderer). You can simply
     *      return class's name via __CLASS__ macros
     */
    abstract function getView();

    /**
     * @return array - data, which wll be sent into view renderer
     */
    abstract function getData();

    /**
     * @return string - Header's title, which will be in header
     */
    abstract function getTitle();

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    abstract function getModalID();

    /**
     * @return string - Name of error modal window class, if isError method returns
     *      true, then element's class will be 'error-popup';
     */
    public function getErrorClass() {
        return $this->isError() ? "error-popup" : "";
    }

    /**
     *   Every widget must implements run method, which provides it's
     * initialization and rendering widget/modal elements
     */
    public function run() {

        $action = $this->getController()->getAction() != null ? $this->getController()->getAction()->getId()
            : $this->controller->defaultAction;

        $this->render("application.components.views.ModalWindow", array(
            'controller' => strtolower($id = $this->getController()->getId()),
            'self' => $this,
            'action' => strtolower($action)
        ));
    }
} 