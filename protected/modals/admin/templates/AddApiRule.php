<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-10
 * Time: 11:52
 */

class AddApiRule extends ModalWindow {

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
		$rows = Api::model()->getRows(false);
		$apiList = array();
		foreach ($rows as $i => $row) {
			$apiList[$row["key"]] = $row["key"];
		}
        return array(
			'apiList' => $apiList,
            'model' => new FormApiRuleAdd()
        );
    }

    /**
     * @return string - Header's title, which will be in header
     */
    function getTitle() {
        return "Открыть внешний доступ";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "addApiRulePopup";
    }
}