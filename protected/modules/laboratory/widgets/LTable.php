<?php

class LTable extends LWidget {

	/**
	 * @var $table LModel
	 * @var $header Array
	 */
	public $table = null;
	public $header = null;
	public $sort = null;
	public $desc = null;

	public function run($return = false) {
		if (!($this->table instanceof LModel)) {
			throw new CException("Table's model must implements LTableProtocol");
		}
		if ($this->sort == null) {
			$this->sort = "id";
		}
		$command = $this->table->getTable()->order(
			$this->sort.($this->desc ? "desc" : "")
		);
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
		return $this->render(__CLASS__, [
			"data" => $command->queryAll()
		], $return);
	}
}