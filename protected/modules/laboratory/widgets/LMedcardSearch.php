<?php

class LMedcardSearch extends LWidget {

	/**
	 * @var string - Search mode (lis or mis)
	 * @see LMedcardTable::mode
	 */
	public $mode = "mis";

    public function run() {
        $this->render(__CLASS__);
    }
} 