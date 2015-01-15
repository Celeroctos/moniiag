<?php
/**
 * Created by PhpStorm.
 * User: Savonin
 * Date: 2014-12-01
 * Time: 16:23
 */

class DeleteWard extends ModalWindow {

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
		return "В удаляемом найдены врачи";
	}

	/**
	 * @return string - Header's modal window identifier (for javascript)
	 */
	function getModalID() {
		return "noticeIssetDoctorPopup";
	}
}