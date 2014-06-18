<?php
class printCategory extends CWidget {
    public $categoryToPrint = null;

    public function run()
    {
        echo $this->render('application.modules.doctors.components.widgets.views.printCategory', array(
            'category' => $this->categoryToPrint,
        ));
    }

}