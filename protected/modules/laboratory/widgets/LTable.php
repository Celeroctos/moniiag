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

		if (is_string($this->params)) {
			$this->params = unserialize(urldecode($this->params));
		}
		if (!is_object($this->criteria)) {
			$this->criteria = new CDbCriteria();
		}

		if (is_string($this->condition) && is_array($this->parameters)) {
			$this->criteria->condition = $this->condition;
			$this->criteria->params = $this->params;
		}

		// Get total rows
		$total = $this->table->getTableCount($this->criteria);

		// Get command for current table
		$command = $this->table->getTable($this->conditions, $this->parameters)->order(
			$this->sort.($this->desc ? " desc" : "")
		);

		// Attach criteria condition to query
		if ($this->criteria) {
			$command->andWhere($this->criteria->condition, $this->criteria->params);
		}

		// Calculate offset
		$this->pages = intval($total / $this->limit + ($total / $this->limit * $this->limit != $total ? 1 : 0));

		if (!$this->pages) {
			$this->pages = 1;
		}

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