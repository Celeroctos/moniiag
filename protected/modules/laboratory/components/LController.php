<?php

class LController extends CController {

    /**
     * Get session instance with current session
     * @return CHttpSession - Yii http session
     */
    public function getSession() {
        if ($this->session == null) {
            $this->session = new CHttpSession();
        }
        return $this->session;
    }

    /**
     * Try to get received data via GET method or throw an exception
     * with error message
     * @param $name string - Name of parameter to get
     * @return mixed - Some received stuff
     * @throws LError - If parameter hasn't been declared in _GET array
     */
    public function get($name) {
        if (!isset($_GET[$name])) {
            throw new LError("GET.${name}");
        }
        return $_GET[$name];
    }

    /**
     * Try to get received data via POST method or throw an exception
     * with error message
     * @param $name string - Name of parameter to get
     * @return mixed - Some received stuff
     * @throws LError - If parameter hasn't been declared in _POST array
     */
    public function post($name) {
        if (!isset($_POST[$name])) {
            throw new LError("POST.${name}");
        }
        return $_POST[$name];
    }

    /**
     * Post error message and terminate script evaluation
     * @param $message String - Message with error
     */
    public function error($message) {
        $this->leave([
            "message" => $message,
            "status" => false
        ]);
    }

    /**
     * Leave script execution and print server's response
     * @param $parameters array - Array with parameters to return
     */
    public function leave($parameters) {
        if (!isset($parameters["status"])) {
            $parameters["status"] = true;
        }
        die(json_encode($parameters));
    }

    /**
     * Post error message and terminate script evaluation
     * @param $exception Exception - Exception
     */
    public function exception($exception) {
        $this->leave([
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine(),
            "status" => false
        ]);
    }

    private $session = null;
} 