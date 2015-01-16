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

    public function actionLogout() {
        try {
            $this->get("session");
        } catch (Exception $e) {
            $this->exception($e);
        }
    }

    public function actionDo() {
        try {
            // Check access for current session's ID
            if (!$this->checkAccess($this->get("key"))) {
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

        // Close current session, set new session's identifier and reopen it
        $this->getSession()->close();
        $this->getSession()->setSessionID($sessionID);
        $this->getSession()->open();

        // Check for login and password existence
        if (!$this->getSession()->hasProperty("L_API/USER_LOGIN") ||
            !$this->getSession()->hasProperty("L_API/USER_PASSWORD")
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