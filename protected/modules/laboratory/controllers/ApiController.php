<?php

class ApiController extends LController {

    /**
     * Override that method to add your chains, if path will be
     * api validation, than access won't be denied
     * @param $filterChain CFilterChain - Chain filter
     */
    public function filterGetAccessHierarchy($filterChain) {
        if ($this->route != "laboratory/api/test" && !$this->checkAccess()) {
            $this->error("Session hasn't been started or validated, access denied");
        } else {
            $filterChain->run();
        }
    }

    /**
     * That action validate user's login and password and return
     * session's identifier on true. It also regenerate session's
     * identifier and browser will receive new session in cookie
     * and session will be opened (if validation will be ok)
     *
     * @in (GET):
     *  + login - User's login
     *  + password - User's password
     * @out (JSON):
     *  + message - Response message
     *  + session - Session's identifier
     *  + status - Response status (true/false)
     */
    public function actionLogin() {
        try {
            // Authenticate user
            $userIdentity = new UserIdentity(
                $this->get("login"),
                $this->get("password")
            );

            // Open session if it hasn't been started
            if (!$this->getSession()->getIsStarted()) {
                $this->getSession()->open();
            }

            // Regenerate session's identifier
            $this->getSession()->regenerateID();

            // Authenticate user
            if (!$userIdentity->authenticateInOneStep()) {
                throw new LError("Can't resolve user's login or password");
            }

            // Copy states to new just generated session
            Yii::app()->user->login($userIdentity);

            // Save session user's login and password
            $this->getSession()->add("L_API/USER_LOGIN", $this->get("login"));
            $this->getSession()->add("L_API/USER_PASSWORD", $this->get("password"));

            // Send response
            $this->leave([
                "message" => "User has successfully logged in",
                "session" => $this->getSession()->getSessionID(),
                "status" => true
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * That action will close session. Use it after login method to load
     * session and destroy it. Session's identifier won't be valid after
     * action execution
     *
     * @in (GET):
     *  + session - Session's identifier
     * @out (JSON):
     *  + message - Response message or error message
     *  + status - True on success and false on error
     */
    public function actionLogout() {
        try {
            // Remove API parameters (redundant)
            $this->getSession()->remove("L_API/USER_LOGIN");
            $this->getSession()->remove("L_API/USER_PASSWORD");

            // Destroy session
            $this->getSession()->destroy();

            // Send response
            $this->leave([
                "message" => "Session has been successfully closed"
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * That action will test session status and returns true in status if
     * session still active and can be validated else it returns false
     *
     * @in (GET):
     *  + session - Session's identifier
     * @out (JSON):
     *  + session - Just received session identifier
     *  + status - True on valid session id and false on invalid
     */
    public function actionTest() {
        try {
            $this->leave([
                "session" => $this->get("session"),
                "status" => $this->checkAccess()
            ]);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * That action will execute some controller's action with
     * necessary arguments and different method type
     *
     * @in (GET/POST):
     *  + session - Session's identifier
     *  + method - Send method (GET/POST) for controller's action
     *  + path - Path to controller's action to execute
     * @out (JSON):
     *  + message - Message with error text
     *  + status - Response status (true or false)
     *  + session - Session's identifier
     *  +
     */
    public function actionDo() {
        try {
            $path = strtolower($this->get("path"));

            if (isset($_GET["method"])) {
                $method = $this->get("method");
            } else {
                $method = "GET";
            }

            unset($_GET["method"]);
            unset($_GET["session"]);

            if (strtoupper($method) == "POST") {
                foreach ($_GET as $key => $value) {
                    $_POST[$key] = $value;
                }
            } else if (strtoupper($method) != "GET") {
                throw new LError("Invalid method type ({$this->get("method")})");
            }

            while (strlen($path) > 0 && $path[0] == "/") {
                $path = substr($path, 1);
            }

            // Invoke controller's action
            try {
                $this->forward($path, true);
            } catch (CException $e) {
                $this->error("Path ({$path}) doesn't exist in laboratory scope");
            }

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * If access denied, then print error message
     */
    protected function accessDenied() {
        $this->error("Session hasn't been started, access denied");
    }

    /**
     * Check current user's session id for access
     * @return bool - True if user has access to API
     * @throws Exception
     * @throws LError
     */
    protected function checkAccess() {
        try {
            // Don't check access for login action
            if ($this->route == "laboratory/api/login") {
                return true;
            }

            // Get session's identifier
            $sessionID = $this->get("session");

            // Close current session, set new session's identifier and reopen it
            $this->getSession()->close();
            $this->getSession()->setSessionID($sessionID);
            $this->getSession()->open();

            // Check for login and password existence
            if (!$this->getSession()->contains("L_API/USER_LOGIN") ||
                !$this->getSession()->contains("L_API/USER_PASSWORD")
            ) {
                return false;
            }

            // Fetch user's model from database
            if (!User::model()->fetchByLoginAndPassword(
                $this->getSession()->get("L_API/USER_LOGIN"),
                $this->getSession()->get("L_API/USER_PASSWORD")
            )) {
                return false;
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        return true;
    }
} 