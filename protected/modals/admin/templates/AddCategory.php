<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-11-19
 * Time: 16:40
 */

class AddCategory extends ModalWindow {

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
        $categoriesList = array('-1' => 'Нет');
        $categories = $categoriesModel->getRows(false,  'name', 'asc', false, false);
        foreach($categories as $index => $category) {
            $categoriesList[$category['id']] = $category['name'];
        }
        return array(
            'model' => new FormCategorieAdd(),
            'categoriesList' => $categoriesList
        );
    }

    /**
     * @return string - Header's title, which will be in header
     */
    function getTitle() {
        return "Добавить категорию";
    }

    /**
     * @return string - Header's modal window identifier (for javascript)
     */
    function getModalID() {
        return "addCategoriePopup";
    }
}