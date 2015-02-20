<?php

class MedcardController extends LController {

    public function actionView() {
        $this->render("view");
    }

	public function actionSearch() {
		try {
			// Build an array with search parameters
			$parameters = [];
			foreach ($this->getFormModel("model", "post") as $model) {
				foreach ($model->attributes as $key => $value) {
					if (!empty($value)) {
						$parameters[$key] = $value;
					}
				}
			}
			$criteria = new CDbCriteria();
			if (isset($parameters["begin_date"]) && isset($parameters["end_date"])) {
				$criteria->addBetweenCondition("registration_date", $parameters["begin_date"], $parameters["end_date"]);
			}
			unset($parameters["begin_date"]);
			unset($parameters["end_date"]);
			if ($parameters["charged_by"] == -1) {
				unset($parameters["charged_by"]);
			}
			foreach ($parameters as $key => $value) {
				$criteria->addSearchCondition($key, $value);
			}
			$this->leave([
				"component" => $this->getWidget("LMedcardTable", [
					"criteria" => $criteria
				])
			]);
		} catch (Exception $e) {
			$this->exception($e);
		}
	}

    /**
     * Override that method to return controller's model
     * @return LModel - Controller's model instance
     */
    public function getModel() {
        return new LMedcard();
    }
}