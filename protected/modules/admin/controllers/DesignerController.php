<?php
class DesignerController extends Controller {

    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
		$categoriesList = array('-1' => 'Нет');
		// Получить все категории 
		$categoriesModel = new MedcardCategorie();
        $categories = $categoriesModel->getRows(false,  'name', 'asc', false, false);
        foreach($categories as $index => $categorie) {
            $categoriesList[$categorie['id']] = $categorie['name'];
        }
        $this->render('designerView', array(
            'model' => new FormCategorieAdd(),
			'categoriesList' => $categoriesList
        ));
    }
}

?>