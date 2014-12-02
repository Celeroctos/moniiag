<?php
/**
 * Created by PhpStorm.
 * User: Savonin
 * Date: 2014-12-01
 * Time: 14:40
 */

class SavingTemplate extends ModalWindow {

	/**
	 * @return string - path to modal window's view (renderer). You can simply
	 *      return class's name via __CLASS__ macros
	 */
	function getView() {
		return __CLASS__;
	}

	/**
	 * @return array - data, which wll be sent into view renderer
	 */
	function getData() {
		return array();
	}

	/**
	 * @return string - Header's title, which will be in header
	 */
	function getTitle() {
		return "Сохранение...";
	}

	/**
	 * @return string - Header's modal window identifier (for javascript)
	 */
	function getModalID() {
		return "savingTemplatePopup";
	}
}