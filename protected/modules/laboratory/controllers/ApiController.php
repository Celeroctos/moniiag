<?php

class ApiController extends LController {

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
            // Fetch user's model
            $user = User::model()->fetchByLoginAndPassword(
                $this->get("login"),
                $this->get("password")
            );

            // Condition is redundant, cuz that exception throws from model
            if ($user == null) {
                throw new LNoSuchUserException("Can't resolve user's login or password");
            }

            // Open session if it hasn't been started
            if (!$this->getSession()->getIsStarted()) {
                $this->getSession()->open();
            }

            // Regenerate session's identifier
            $this->getSession()->regenerateID(true);

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
            $this->exception($e);
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
            // Load session
            $this->getSession()->close();
            $this->getSession()->setSessionID($this->get("session"));
            $this->getSession()->open();

            // Validate session
            if (!$this->checkAccess($this->get("session"))) {
                $this->error("Session hasn't been started or validated");
            }

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
            $this->exception($e);
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
                "status" => $this->checkAccess(
                    $this->get("session")
                )
            ]);
        } catch (Exception $e) {
            $this->exception($e);
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
            // Check access for current session's ID
            if (!$this->checkAccess($this->get("session"))) {
                throw new LAccessDeniedException();
            }

        } catch (Exception $e) {
            $this->exception($e);
        }
    }

    /**
     * Check current user's session id for access
     * @param $sessionID string - Session's identifier
     * @return bool - True if user has access to API
     */
    private function checkAccess($sessionID) {

        if (!$sessionID || !is_string($sessionID)) {
            return false;
        }

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

        // Return success
        return true;
    }
} 