<?php

class LTable extends LWidget {

	public $table = null;
	public $header = null;
	public $sort = null;
	public $desc = null;
    public $pk = null;
    public $limit = null;
    public $disableControl = null;

	public function run() {
		if (!($this->table instanceof LModel)) {
			throw new CException("Table's model must implements LTableProtocol");
		}
		if ($this->sort == null) {
			$this->sort = "id";
		}
		$command = $this->table->getTable()->order(
			$this->sort.($this->desc ? "desc" : "")
		);
        if ($this->limit && is_int($this->limit)) {
            $command->limit($this->limit);
        } else {
            $command->limit(25);
        }
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
        if (!$this->pk) {
            $this->pk = "id";
        }
		return $this->render(__CLASS__, [
			"data" => $command->queryAll()
		]);
	}
}