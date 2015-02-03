<?php

class LConfirmDelete extends LWidget {

	public $title = null;
	public $id = null;

	public function run($return = false) {
		$this->render(__CLASS__, null, $return);
	}
}