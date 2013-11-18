<?php
class ElementsController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
        $this->render('elementsView', array(
            
        ));
    }
}

?>