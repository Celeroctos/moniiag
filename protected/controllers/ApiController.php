<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-08
 * Time: 16:20
 */

class ApiController2 extends Controller {

    /**
     * Register new API 20 bytes SSL key
     */
    public function actionRegister() {
        if (!isset($_GET['user_id'])) {
            print json_encode(array(
                "message" => "GET.user_id",
                "success" => false
            ));
            return;
        }
        $userID = $_GET['user_id'];
        $userRow = User::model()->getOne($userID);
        $isSuper = false;
        foreach (Yii::app()->user->getState('roleId') as $i => $role) {
            // TODO What the Fuck!!!
            if ($role['name'] == "Супервайзер") {
                $isSuper = true;
                break;
            }
        }
        if ($userRow && $isSuper) {
            $apiRow = Api::model()->findByUser($userRow['id']);
            if (count($apiRow) == 0) {
                print json_encode(array(
                    "key" => Api::model()->register($userRow['id']),
                    "success" => true
                ));
            } else {
                print json_encode(array(
                    "key" => $apiRow[0]["key"],
                    "success" => true
                ));
            }
        } else {
            print json_encode(array(
                "message" => "Недостаточно прав",
                "success" => false
            ));
        }
    }

    /**
     * Validate API and open session
     */
    public function actionValidate() {
        // Check parameters
        if (!isset($_GET['key']) || !isset($_GET['login']) || !isset($_GET['password'])) {
            print json_encode(array(
                "message" => "GET.key, GET.login, GET.password",
                "success" => false
            ));
            return;
        }
        // Get parameters
        $key = $_GET['key'];
        $password = $_GET['password'];
        $login = $_GET['login'];
        // Fetch API by user's login
        $apiRow = Api::model()->findByUserLogin($login);
        // Check row and compare it's keys
        if (!$apiRow || $apiRow[0]['key'] !== $key) {
            print print_r(array(
                "message" => "Invalid login or password or key",
                "success" => false
            ));
        }
        // Identify user
        $userIdent = new UserIdentity($login, $password);
        // First step with skipped second step
        if($userIdent->authenticateStep1(true)) {
            Yii::app()->user->login($userIdent);
            echo CJSON::encode(array(
                'session' => Yii::app()->getSession()->getSessionId(),
                'success' => 'true'
            ));
            exit();
        } else {
            $resultCode = 'loginError';
            if ($userIdent->wrongLogin()) {
                $resultCode = 'notFoundLogin';
            }
            if ($userIdent->wrongPassword()) {
                $resultCode = 'wrongPassword';
            }
            echo CJSON::encode(array(
                'success' => $resultCode,
                'errors' => $userIdent->errorMessage
            ));
        }
    }

    /**
     * Get row for current API key
     * @param $key string - 20 bytes SLL API key
     */
    public function actionGet($key) {
        print json_encode(array(
            "key" => ApiModel::model()->get($key),
            "success" => true
        ));
    }

    /**
     * Delete API key from database
     * @param $key string - 20 bytes SLL API key
     */
    public function actionDelete($key) {
        ApiModel::model()->delete($key);
        print json_encode(array(
            "success" => true
        ));
    }
} 