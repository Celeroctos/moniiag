<?php

class LController extends Controller {

    /**
     * Override that method to add your chains, if path will be
     * api validation, than access won't be denied
     * @param $filterChain CFilterChain - Chain filter
     */
    public function filterGetAccessHierarchy($filterChain) {

        // Get access result
        $this->access = $this->checkAccess();

        // If access is null, then run parent's filter, cuz
        // we can't allow access to every element as default and
        // can't prevent any actions, so we will give parent
        // right to decide. Else we can check access status
        // and invoke access denied method on false or simply
        // run next filter on true
        if ($this->access == null) {
            parent::filterGetAccessHierarchy($filterChain);
        } else if ($this->access == false) {
            $this->onAccessDenied();
        } else {
            $filterChain->run();
        }
    }

    /**
     * Override that method to provide access denied action, for example you can
     * return json response with error or something else
     */
    protected function onAccessDenied() {
        $this->redirect(Yii::app()->baseUrl);
    }

    /**
     * Override that method to update some widget if you want
     * @return null|LWidget - Widget component
     */
    protected function onWidgetUpdate() {
        return null;
    }

    /**
     * Check access to controller, if it will return null, then access will be
     * checked via filterGetAccessHierarchy action in current or parent's controller
     * @param mixed ... - Arguments to check
     * @return bool|null - True if access allowed and false if access denied
     */
    protected function checkAccess() {
        return null;
    }

    /**
     * @param string $class - Path to widget to render
     * @param array $properties - Widget's properties
     * @param bool $return - Should widget return response or print to output stream
     * @return mixed|void
     */
    public function widget($class, $properties = [], $return = false) {
        $widget = $this->createWidget($class, $properties);
        if ($return) {
            return $widget->run(true);
        }
        $widget->run(false);
    }

    /**
     * That action will catch widget update and returns
     * new just rendered component
     */
    public function actionGetWidget() {
        try {
            // Get widget's class component and unique identification number and method
            $class = $this->getAndUnset("class");
            $unique = $this->getAndUnset("unique");
            $method = $this->getAndUnset("method");

            if (strtoupper($method) == "POST") {
                foreach ($_GET as $key => $value) {
                    $_POST[$key] = $value;
                }
                $parameters = $_POST;
            } else {
                $parameters = $_GET;
            }

            // Create widget
            $widget = $this->createWidget($class, $parameters);

            if (!($widget instanceof LWidget)) {
                throw new LError("Can't update widget that don't extends LWidget component");
            }

            $this->leave([
                "unique" => $unique,
                "component" => $widget->run(true)
            ]);
        } catch (Exception $e) {
            $this->exception($e);
        }
    }

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
            throw new LError("GET.$name");
        }
        return $_GET[$name];
    }

    /**
     * Try to get and unset variable from GET method or throw an exception
     * @param String $name - Name of parameter in GET array
     * @return Mixed - Some received value
     * @throws LError - If parameter hasn't been declared in _GET array
     */
    public function getAndUnset($name) {
        $value = $this->get($name);
        unset($_GET[$name]);
        return $value;
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
            throw new LError("POST.$name");
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
        $method = $exception->getTrace()[0];
        $this->leave([
            "message" => $exception->getMessage(),
            "file" => basename($method["file"]),
            "method" => $method["class"]."/".$method["function"],
            "line" => $method["line"],
            "status" => false
        ]);
    }

    /**
     * Get access, if returns null, then it has been set by
     * one of parents controller
     * @return bool|null - Access status
     */
    public function getAccess() {
        return $this->access;
    }

    private $session = null;
    private $access = null;
} 