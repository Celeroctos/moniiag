<?php

trait LTableTrait {

	/**
	 * Default order table column name
	 * @var string
	 */
	public $sort;

	/**
	 * Order direction
	 * @var bool
	 */
	public $desc = false;

	/**
	 * Maximum displayed rows per page
	 * @var int
	 */
	public $limit = 25;

	/**
	 * Current displayed page
	 * @var int
	 */
	public $page = 1;

	/**
	 * Search criteria
	 * @var CDbCriteria
	 */
	public $criteria;
}