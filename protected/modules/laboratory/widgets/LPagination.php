<?php

class LPagination extends LWidget {

	/**
	 * @var int - Current page
	 */
	public $page = 1;

	/**
	 * @var int - Total pages
	 */
	public $pages = 0;

	/**
	 * @var int - Maximum displayed pages
	 */
	public $limit = 10;

	/**
	 * @var string - JavaScript change action
	 */
	public $action = "console.log";

	/**
	 * Get string for <li> with onclick action
	 * @param $condition - Boolean condition result
	 * @param $accumulator - Accumulation value
	 * @return string - Result string
	 */
	public function getClick($condition = true, $accumulator = 0) {
		$page = $this->page + $accumulator;
		if ($condition) {
			return "onclick=\"$this->action(this, {$page})\"".(
				$this->page == $page ? "class=\"active\"" : ""
			);
		} else {
			return "class=\"disabled\"";
		}
	}

	/**
	 * Executes the widget.
	 * This method is called by {@link CBaseController::endWidget}.
	 */
	public function run() {
		$offset = $offset = $this->limit - $this->pages + $this->page;
		$this->render(__CLASS__, [
			"offset" => $offset > 0 ? -$offset : 0,
			"step" => $offset < 0 ? 1 : -1
		]);
	}
}