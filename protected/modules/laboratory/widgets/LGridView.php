<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2015-03-04
 * Time: 15:42
 */

class LGridView extends LWidget {

    /**
     * @var string - Primary client's component identifier
     */
    public $id = null;

    /**
     * @var LModel - Model to render, it must be instance of LModel class
     */
    public $model = null;

    /**
     * @var string - Basic table class
     */
    public $class = "table table-bordered table-striped table-condensed";

    /**
     * @var array - Array with columns to display, if its null, then it
     *      will take array with columns from getKeys method
     * @see LModel::getKeys
     */
    public $columns = null;

    /**
     * Executes the widget.
     * This method is called by {@link CBaseController::endWidget}.
     */
    public function run() {
        if (!($this->model instanceof LModel)) {
            throw new CException("Model must be instance of LModel class");
        }
        if (empty($this->columns)) {
            $this->columns = [];
            foreach ($this->model->getKeys() as $key => $ignored) {
                $this->columns[] = [
                    "name" => $key
                ];
            }
        }
        $this->render(__CLASS__, [
            "model" => $this->model
        ]);
    }
}