<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-19
 * Time: 16:08
 */

class EditTemplate extends ModalWindow {

    /**
     * @return string - path to modal window's view (renderer)
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

        /*
         * Categories
         */

        $categories = $categoriesModel->getRows(false, 'name', 'asc');
        $categoriesList = array();

        foreach($categories as $index => $categorie) {
            $categoriesList[$categorie['id']] = $categorie['name'];
        }

        /*
         * Guides
         */

        $guides = $guidesModel->getRows(false);
        $guidesList = array('-1' => 'Нет');

        foreach($guides as $index => $guide) {
            $guidesList[$guide['id']] = $guide['name'];
        }

        return array(
            'categoriesList' => $categoriesList,
            'pagesList' => $this->getController()->getPagesList(),
            'model' => new FormTemplateAdd()
        );
    }

    /**
     * @return string - Header's title, which will be in header
     */
    function getTitle() {
        return "Редактировать шаблон";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "editTemplatePopup";
    }
}