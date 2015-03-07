<?php

abstract class LController extends Controller {

    /**
     * Override that method to add your chains, if path will be
     * api validation, than access won't be denied
     * @param $filterChain CFilterChain - Chain filter
     */
    public function filterGetAccessHierarchy(CFilterChain $filterChain) {

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
     * Override that method to return controller's model
     * @return LModel - Controller's model instance
     */
    public abstract function getModel();

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
     * Render widget
     * @param string $class - Path to widget to render
     * @param array $properties - Widget's properties
     * @param bool $return - Should widget return response or print to output stream
     * @return mixed|void
     */
    public function widget($class, array $properties = [], $return = false) {
        if ($properties === true) {
            $return = true;
            $properties = [];
        }
        $widget = $this->createWidget($class, $properties);
        if ($return !== false) {
            ob_start();
            $widget->run();
            return ob_get_clean();
        } else {
            $widget->run();
        }
        return null;
    }

    /**
     * Render widget and return it's just rendered component
     * @param string $class - Path to widget to render
     * @param array $properties - Widget's properties
     * @return mixed|void
     */
    public function getWidget($class, array $properties = []) {
        return $this->widget($class, $properties, true);
    }

    /**
     * That method will help to remove row from an array. Why do you need it? For example you
     * have table with rows and also you have user interface with forms to edit/add new rows
     * into that table. After sending request for data update you can remove all stuff
     * from db and reappend rows but you might crash foreign keys, so you can use
     * basic method <code>LModel::findIds</code> to fetch list with identification
     * numbers (primary keys) and look though every received row and invoke that method. The
     * result array of your iterations will be array with row's ids to remove from db.
     *
     * <pre>
     * // Fetch rows identifications from database by some condition
     * $rows = MyTable::model()->findIds();
     *
     * // look though each row in received data
     * foreach ($_GET['data'] as $row) {
     *     self::arrayInDrop($row, $rows);
     *     // provide your actions with row
     * }
     *
     * // remove remaining rows
     * foreach ($rows as $id) {
     *     MyTable::model()->deleteByPk($id);
     * }
     * </pre>
     *
     * @param $row mixed - Current row with "id" field
     * @param $rows array - Array with rows
     * @param string $id - Primary key name
	 * @see LModel::findIds
     */
    public static function arrayInDrop($row, array &$rows, $id = "id") {
        if (is_array($row)) {
            if (!isset($row[$id])) {
                return ;
            }
            $id = $row[$id];
        } else {
            if (!isset($row->$id)) {
                return ;
            }
            $id = $row->$id;
        }
        if ($id && ($index = array_search(intval($id), $rows)) !== false) {
            array_splice($rows, $index, 1);
        }
    }

    /**
     * Decode url and convert it into array
     * @param string $url - Encoded url
     * @param string $form - Form's name
     * @return array - Array with values
     */
    public function decode($url, &$form = "") {
        $result = [];
        if (!is_string($url)) {
            return null;
        }
        $url = urldecode($url);
        $array = explode("&", $url);
        foreach ($array as $str) {
            $match = [];
            preg_match("/\\[[a-zA-Z0-9_]+\\]/", $str, $match);
            if (!count($match)) {
                continue;
            }
            $value = substr($str, strpos($str, "=") + 1);
            if ($value === false) {
                $value = "";
            }
            if (!is_string($form) || !strlen($form)) {
                $form = substr($str, 0, strpos($str, "["));
            }
            $result[substr($match[0], 1, strlen($match[0]) - 2)] = $value;
        }
        return $result;
    }

    /**
     * Decode form's url, convert it to array try to validate it's form
     * @param string $form - String with encode form's url
     * @param bool $error - Set that flag to false to store errors in array
     * @param string $name - Form's name will be in that field
     * @return LFormModel - Form's model with attributes
     * @throws CException
     */
    public function getUrlForm($form, $error = true, &$name = "") {
        if (!is_string($form)) {
            throw new CException("Form's model must be serialized form string");
        }
        $array = $this->decode($form, $name);
        $form = new $name();
        if (!($form instanceof LFormModel)) {
            throw new CException("Form must be instance of LFormModel class");
        }
		$form->setAttributes($array);
		$form->attributes = $array;
        if (!$form->validate()) {
            if ($error) {
                $this->leave([
                    "message" => "Произошли ошибки во время валидации формы",
                    "errors" => $form->getErrors(),
                    "status" => false
                ]);
            } else {
                $this->errors += $form->getErrors();
            }
        }
        return $form;
    }

    /**
     * Get model via GET method, it will check it for array and decode if model
     * is simply serialized string
     * @param string $model - Model's name from GET/POST arrays
     * @param string $method - Receive method type
     * @return LFormModel|Array - Model with attributes or array with founded forms
     * @throws CException - If form's model instance don't extends LFormModel
     */
    public function getFormModel($model = "model", $method = "get") {
        $form = $this->$method($model);
        if (!is_array($form)) {
            return $this->getUrlForm($form);
        }
        $array = [];
        foreach ($form as $f) {
            $array[] = $this->getUrlForm($f, false);
        }
        if (count($this->errors) > 0) {
            $this->leave([
                "message" => "Произошли ошибки во время валидации формы",
                "errors" => $this->errors,
                "status" => false
            ]);
        }
        return $array;
    }

    /**
     * That action will catch widget update and returns
     * new just rendered component. Override that method
     * to check necessary privileges and invoke super method
     */
    public function actionGetWidget() {
        try {
            // Get widget's class component and unique identification number and method
            $class = $this->getAndUnset("class");

            if (isset($_GET["model"])) {
                $model = $this->getAndUnset("model");
            } else {
                $model = null;
            }

            if (isset($_GET["method"])) {
                $method = $this->getAndUnset("method");
            } else {
                $method = "GET";
            }

            if (isset($_GET["form"])) {
                $form = $this->getAndUnset("form");
                if (is_string($form)) {
                    $form = $this->decode($form);
                }
            } else {
                $form = null;
            }

            if (strtoupper($method) == "POST") {
                foreach ($_GET as $key => $value) {
                    $_POST[$key] = $value;
                }
                $parameters = $_POST;
            } else {
                $parameters = $_GET;
            }

            if ($model != null) {
                $parameters += [
                    "model" => new $model(null)
                ];
            }

            // Create widget, check for LWidget instance and copy parameters
            $widget = $this->createWidget($class, $parameters);

            if (!($widget instanceof LWidget)) {
                throw new CException("Can't update widget which don't extends LWidget component");
            }

            if ($form != null && $widget instanceof LForm && is_array($form)) {
                foreach ($form as $key => $value) {
                    $widget->model->$key = $value;
                }
            }

            $this->leave([
                "id" => isset($widget->id) ? $widget->id : null,
                "component" => $widget->call(),
                "model" => $form
            ]);
        } catch (Exception $e) {
            $this->exception($e);
        }
    }

	/**
	 * Register some form's values in database, it will automatically
	 * fetch model from $_POST["model"], decode it, build it's LFormModel
	 * object and save into database. But you must override
	 * LController::getModel and return instance of controller's model else
	 * it will throw an exception
	 *
	 * @in (POST):
	 *  + model - String with serialized client form via $("form").serialize(), if you're
	 * 		using LModal or LPanel widgets that it will automatically find button with
	 * 		submit type and send ajax request
	 * @out (JSON):
	 *  + message - Message with status
	 *  + status - True if everything ok
	 *
	 * @see LController::getModel
	 * @see LModal
	 * @see LPanel
	 */
	protected function actionRegister() {
		try {
			$model = $this->getFormModel("model", "post");
			if (is_array($model)) {
				throw new CException("Forms to register mustn't be array");
			}
			if (($table = $this->getModel()) == null) {
				throw new CException("Your controller must override LController::getModel method");
			}
			$attributes = $model->getContainer();
			if (isset($attributes)) {
				unset($attributes["id"]);
			}
			foreach ($attributes as $key => $value) {
				$table->$key = $value;
			}
			$table->attributes = $attributes;
			$table->save(false);
			$this->leave([
				"message" => "Данные успешно сохранены"
			]);
		} catch (Exception $e) {
			$this->exception($e);
		}
	}

    /**
     * Override that method to remove element from model, by default
     * it will try to find controller's model and remove it
	 *
	 * @in (POST):
	 *  + id - Element's identification number
	 * @out (JSON):
	 *  + message - Response message
	 *  + status - True if everything ok
     */
    protected function actionDelete() {
        try {
            $this->getModel()->deleteByPk($this->post("id"), "id = :id", [
                ":id" => $this->post("id")
            ]);
            $this->leave([
                "message" => "Элемент был успешно удален"
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
     * @throws CException - If parameter hasn't been declared in _GET array
     */
    public function get($name) {
        if (!isset($_GET[$name])) {
            throw new CException("GET.$name");
        }
        return $_GET[$name];
    }

    /**
     * Try to get and unset variable from GET method or throw an exception
     * @param String $name - Name of parameter in GET array
     * @return Mixed - Some received value
     * @throws CException - If parameter hasn't been declared in _GET array
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
     * @throws CException - If parameter hasn't been declared in _POST array
     */
    public function post($name) {
        if (!isset($_POST[$name])) {
            throw new CException("POST.$name");
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
    public function leave(array $parameters) {
        if (!isset($parameters["status"])) {
            $parameters["status"] = true;
        }
        die(json_encode($parameters));
    }

    /**
     * Post error message and terminate script evaluation
     * @param $exception Exception - Exception
     */
    public function exception(Exception $exception) {
        $method = $exception->getTrace()[0];
        $this->leave([
            "message" => basename($method["file"])."[".$method["line"]."] ".$method["class"]."::".$method["function"]."(): \"".$exception->getMessage()."\"",
            "file" => basename($method["file"]),
            "method" => $method["class"]."::".$method["function"]."()",
            "line" => $method["line"],
            "status" => false,
            "trace" => $exception->getTrace()
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
    private $errors = [];
} 