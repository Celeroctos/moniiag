<?php

class LGuideColumnEditor extends LForm {

	/**
	 * @var LGuideForm - Guide basic form model with
	 * declared table fields
	 */
	public $model = null;

	/**
	 * @var string - Name of widget's identification value
	 * to render, by default it has id "guide-edit"
	 */
	public $id = "guide-edit";

	/**
	 * @var null - Guide columns with form models. We need form's models, cuz
	 * we use LForm widget to generate and render form
	 */
	public $columns = null;

	/**
	 * @var LGuideColumnForm - Default form model, it will be loaded with default
	 * guide's identification value
	 */
	public $default = null;

	public function run() {
		if (!$this->default) {
			$this->default = new LGuideColumnForm();
		}
		if ($this->model && $this->model->id) {
			$this->columns = [];
			$guide = LGuide::model()->find("id = :id", [
				":id" => $this->model->id
			]);
			if ($guide == null) {
				throw new CException("Can't resolve guide model with key specified \"{$this->model->id}\"");
			}
			foreach ($this->model as $key => $value) {
				$this->model->$key = $guide->$key;
			}
			$columns = LGuideColumn::model()->findOrdered("guide_id = :guide_id", [
				":guide_id" => $this->model->id
			]);
			foreach ($columns as $i => $column) {
				$form = new LGuideColumnForm();
				foreach ($column as $key => $value) {
					$form->$key = $value;
				}
				$form->guide_id = $this->model->id;
				$this->columns[] = $form;
			}
			$this->default->guide_id = $this->model->id;
		} else {
			$this->model = new LGuideForm();
		}
		if ($this->columns == null) {
			$this->columns = [];
		}
		return $this->render(__CLASS__, []);
	}
}