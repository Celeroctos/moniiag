<?php

class GuideController extends LController {

	public function actionView() {
		$this->render("view");
	}

	public function actionRegister() {
		try {
			$model = $this->getFormModel();
			if (!($model instanceof LGuideForm)) {
				$this->error("Model must instance of LGuideForm");
			}
			$name = trim($model->name);
			$row = LGuide::model()->find("lower(name) = lower(:name)", [
				":name" => $name
			]);
			if ($row != null) {
				$this->error("Справочник с таким именем уже существует");
			}
			$guide = new LGuide();
			$guide->name = $name;
			$guide->save();
			$this->leave([
				"model" => $model,
				"message" => "Справочник был успешно добавлен",
				"status" => true,
			]);
		} catch (Exception $e) {
			$this->exception($e);
		}
	}

	public function actionUpdate() {
		$save = function($model) {
			$table = new LGuideColumn();
			foreach ($model as $key => $value) {
				$table->$key = $value;
			}
			unset($table->id);
			$table->save();
		};
		try {
			$model = $this->getFormModel();
			if (is_array($model)) {
				$id = $model[0]->id;
				LGuideColumn::model()->deleteAll("guide_id = :guide_id", [
					":guide_id" => $id
				]);
				$table = LGuide::model()->findByPk($id);
				foreach ($model[0] as $key => $value) {
					$table->$key = $value;
				}
				$table->save();
				array_splice($model, 0, 1);
				foreach ($model as $m) {
					$save($m);
				}
			} else {
				$save($model);
			}
			$this->leave([
				"model" => $model,
				"message" => "Справочник был успешно сохранен"
			]);
		} catch (Exception $e) {
			$this->exception($e);
		}
	}

	/**
	 * Override that method to remove element from model, by default
	 * it will try to find controller's model and remove it
	 */
	public final function actionDelete() {
		parent::actionDelete();
	}

	/**
	 * Override that method to return controller's model
	 * @return LModel - Controller's model instance
	 */
	public function getModel() {
		return new LGuide();
	}
}