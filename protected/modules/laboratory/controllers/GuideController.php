<?php

class GuideController extends LController {

	/**
	 * Default view action, which renders page with guides
	 */
	public function actionView() {
		$this->render("view");
	}

	/**
	 * That action will register new guide by it's model
	 * in database and returns True on success
	 *
	 * @in (GET):
	 *  + model - String with encoded and serialized form's data
	 * @out (JSON):
	 *  + model - Just received decode model (redundant)
	 *  + message - Result message
	 *  + status - True if everything ok
	 */
	public function actionRegister() {
		try {
			$model = $this->getFormModel();
			if (!($model instanceof LGuideForm)) {
				$this->error("Model must be an instance of LGuideForm");
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

	/**
	 * That action will update guide's columns and save it in
	 * database. It will update all current columns and remove
	 * unused
	 *
	 * @in (GET):
	 *  + $model - String with encode serialized form or array with models, it that way
	 * 			it will take model with index 0 as LGuideForm model and others as LGuideColumn model
	 * @out (JSON):
	 *  + model - Just received decode model or array with models (redundant)
	 *  + message - Response message with error or success
	 *  + status - True if everything ok
	 */
	public function actionUpdate() {
		$save = function($model) {
			if (!empty($model->id) && $model->id) {
				$table = LGuideColumn::model()->find("id = :id", [
					":id" => $model->id
				]);
				if (!$table) {
					throw new CException("Can't resolve guide column's identification number \"{$model->id}\"");
				}
			} else {
				$table = new LGuideColumn();
				unset($model->id);
			}
			foreach ($model as $key => $value) {
				$table->$key = $value;
			}
			$table->save();
		};
		try {
			$model = $this->getFormModel();
			if (is_array($model)) {
				$id = $model[0]->id;
				$columns = LGuideColumn::model()->findIds("guide_id = :guide_id", [
					":guide_id" => $id
				]);
				$table = LGuide::model()->findByPk($id);
				foreach ($model[0] as $key => $value) {
					$table->$key = $value;
				}
				$table->save();
				array_splice($model, 0, 1);
				foreach ($model as $m) {
					self::arrayInDrop($m, $columns);
					$save($m);
				}
				foreach ($columns as $c) {
					LGuideColumn::model()->deleteByPk($c);
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

	public function actionApply() {
		$save = function($guideId, $row, $id) {
			if ($id) {
				$table = LGuideRow::model()->findByPk($id);
			} else {
				$table = new LGuideRow();
			}
			$table->guide_id = $guideId;
			$table->save();
			foreach ($row as $c) {
				/** @var $column LGuideColumn */
				$column = LGuideColumn::model()->find("guide_id = :guide_id and position = :position", [
					":guide_id" => $guideId,
					":position" => $c["position"]
				]);
				if (!$column) {
					throw new CException("Can't resolve column name for guide \"{$guideId}\" with position \"{$c["position"]}\"");
				}
				if (isset($c["id"])) {
					$value = LGuideValue::model()->find("id = :id", [
						":id" => $c["id"]
					]);
					if (!$value) {
						throw new CException("Can't resolve value from guide with \"{$c["id"]}\" identification number");
					}
				} else {
					$value = new LGuideValue();
					$value->guide_column_id = $column->id;
					$value->guide_row_id = $table->id;
				}
				if (!isset($c["value"])) {
					$c["value"] = "";
				}
				$value->value = $c["value"];
				if (is_array($value->value)) {
					$value->value = json_encode($value->value);
				}
				$value->save();
			}
		};
		try {
			$guide = intval($this->post("guide_id"));
			$data = $this->post("data");
			if (!is_array($data)) {
				throw new CException("Can't resolve received data field as array \"data\"");
			}
			$rows = LGuideRow::model()->findIds("guide_id = :guide_id", [
				":guide_id" => $guide
			]);
			foreach ($data as $row) {
				self::arrayInDrop($row, $rows);
				if (isset($row["id"])) {
					$id = $row["id"];
				} else {
					$id = null;
				}
				$save($guide, $row["model"], $id);
			}
			foreach ($rows as $row) {
				LGuideRow::model()->deleteByPk($row);
			}
			$this->leave([
				"delete" => $rows,
				"data" => $data,
				"message" => "Данные были успешно добавлены"
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