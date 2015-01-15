<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-19
 * Time: 17:09
 */

class EditElement extends ModalWindow {

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

        $categoriesModel = new MedcardCategorie();
        $guidesModel = new MedcardGuide();
        $elementsModel = new MedcardElement();

        // Categories

        $categories = $categoriesModel->getRows(false, 'name', 'asc');
		$categoriesList = array('-1' => 'Нет');

        foreach($categories as $index => $categorie) {
            $categoriesList[$categorie['id']] = $categorie['name'];
        }

        // Guides

        $guides = $guidesModel->getRows(false, 'name', 'asc');
        $guidesList = array('-1' => 'Нет');

        foreach($guides as $index => $guide) {
            $guidesList[$guide['id']] = $guide['name'];
        }

        return array(
            'model' => new FormElementAdd(),
            'typesList' => $elementsModel->getTypesList(),
            'categoriesList' => $categoriesList,
            'guidesList' => $guidesList,
            'guideValuesList' => array('-1' => 'Нет')
        );
    }

    /**
     * @return string - Header's title, which will be in header
     */
    function getTitle() {
        return "Редактировать элемент управления";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "editElementPopup";
    }
}