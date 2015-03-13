<?php

class LMedcardTable extends LWidget {

	use LTableTrait;

	/**
	 * @var string - Default search mode, set it to "lis" if you
	 * 	want fetch rows from laboratory medcards
	 */
	public $mode = "mis";

    public function run() {
		if (!isset(self::$models[$this->mode])) {
			throw new CException("Unresolved search mode \"{$this->mode}\"");
		}
        $this->render(__CLASS__, [
			"model" => new self::$models[$this->mode]()
		]);
    }

	private static $models = [
		"mis" => "LMedcard2",
		"lis" => "LMedcard"
	];
} 