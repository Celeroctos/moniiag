<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-08
 * Time: 16:20
 */

class ApiController extends Controller {

    public function actionGet() {

        // Test basic GET fields
        if (!isset($_GET["key"])) {
            $this->postError("GET.key");
        }

        // Initialize variables
        $key = $_GET["key"];

        // Unset api key's and path
        unset($_GET["key"]);

        // Find API info in db
        $apiInfo = Api::model()->findByPk($key);

        if ($apiInfo == null) {
            $this->postError("Invalid API key or access denied");
        }
        $path = $apiInfo->path;

        // Explode path to get action
        $exploded = explode('/', $path);

        // Check path elements count
        if (count($exploded) <= 1) {
            $this->postError("Empty controller's path");
        }

        // Get action from exploded string
        $action = $exploded[count($exploded) - 1];

        // Get path to controller
        $controller = substr($path, 0, strpos($path, $action) - 1);

        // TODO Create new user with necessary privileges for API
        $userIdentifier = new UserIdentity("SYSTEM", "123456");

        // Authenticate user for API actions (skip second step)
        if(!$userIdentifier->authenticateStep1(true)) {
            $this->postError("Unresolved user's login or invalid password");
        }

        // Add get parameters to url
        foreach ($_GET as $i => $variable) {
            if ($path[strlen($path) - 1] != "?") {
                $path .= "?";
            }
            $path .= $i."=".$variable."&";
        }

        // Remove last '&' if exists
        if ($path[strlen($path) - 1] == "&") {
            $path = substr($path, 0, strlen($path) - 1);
        }

        // Set absolute path to controller with action to leave 'api' directory
        $classPath = $controller."Controller";
        $className = $exploded[count($exploded) - 2]."Controller";

        // Convert first letter to upper case (It won't work without it)
        $className[0] = strtoupper($className[0]);

        // Replace '/' with '.'
        $classPath = str_replace("/", ".", $classPath);

        // Extend action with 'action' prefix
        $methodName = "action".$action;

        // Import controller
        Yii::import($classPath);

        // Invoke controller's action
        call_user_func_array(array(
            new $className($className), $methodName
        ), $_GET);

        // Destroy session
        Yii::app()->session->destroy();
    }
} 