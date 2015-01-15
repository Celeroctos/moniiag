<?php

class LController extends CController {

    /**
     * Get session instance with current session
     * @return CHttpSession - Yii http session
     */
    public function getSession() {

        // Check session for existence
        if ($this->session == null) {
            $this->session = new CHttpSession();
        }

        // Return yii session object
        return $this->session;
    }

    /**
     * Get session identifier
     */
    public function getSessionID() {
        return @session_id() !== "" ? @session_id() : die([
                "message" => "Session hasn't been started",
                "status" => false
            ]);
    }

    /**
     * Try to get received data via GET method or throw an exception
     * with error message
     * @param $name string - Name of parameter to get
     * @return mixed - Some received stuff
     * @throws Exception - If parameter hasn't been declared in _GET array
     */
    public function get($name) {
        if (!isset($_GET[$name])) {
            throw new Exception("GET.${name}");
        }
        return $_GET[$name];
    }

    /**
     * Try to get received data via POST method or throw an exception
     * with error message
     * @param $name string - Name of parameter to get
     * @return mixed - Some received stuff
     * @throws Exception - If parameter hasn't been declared in _POST array
     */
    public function post($name) {
        if (!isset($_POST[$name])) {
            throw new Exception("POST.${name}");
        }
        return $_POST[$name];
    }

    /**
     * Post error message and terminate script evaluation
     * @param $message String - Message with error
     */
    public function postError($message) {
        die(json_encode([
            "status" => false,
            "message" => $message
        ]));
    }

    /**
     * Post error message and terminate script evaluation
     * @param $exception Exception - Exception
     */
    public function postException($exception) {
        die(json_encode([
            "status" => false,
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]));
    }

    private $session = null;
} 