<?php

class LTable extends LWidget {

	use LTableTrait;

	public $widget = null;
	public $table = null;
	public $header = null;
    public $pk = null;
	public $hideArrow = false;
	public $controls = [];
	public $pages = null;
	public $conditions = "";
	public $parameters = [];
	public $disablePagination = false;

	public function run() {

		// Check table instance
		if (!($this->table instanceof LModel)) {
			throw new CException("Table's model must extends LModel");
		}

		// Copy parameters from parent widget
		if ($this->widget) {
			foreach ($this->widget as $key => $value) {
				if (!empty($value)) {
					$this->$key = $value;
				}
			}
		}

		// Set default order key
		if (empty($this->sort)) {
			$this->sort = "id";
		}

		// Get total rows
		$total = $this->table->getTableCount();

		// Get command for current table
		$command = $this->table->getTable($this->conditions, $this->parameters)->order(
			$this->sort.($this->desc ? " desc" : "")
		);

		// Attach criteria condition to query
		if ($this->criteria && $this->criteria instanceof CDbCriteria) {
			$command->andWhere($this->criteria->condition, $this->criteria->params);
		}

		// Calculate offset
		$this->pages = intval($total / $this->limit + ($total / $this->limit * $this->limit != $total ? 1 : 0));

		// Set limit
		$command->limit($this->limit);
		$command->offset($this->limit * ($this->page - 1));

		// Prevent array offset errors
		foreach ($this->header as $key => &$value) {
			if (!isset($value["id"])) {
				$value["id"] = "";
			}
			if (!isset($value["class"])) {
				$value["class"] = "";
			}
			if (!isset($value["style"])) {
				$value["style"] = "";
			}
		}

		// Set default primary key
        if (!$this->pk) {
            $this->pk = "id";
        }

		// Render widget
		return $this->render(__CLASS__, [
			"data" => $command->queryAll(),
			"parent" => get_class($this->widget)
		]);
	}
}