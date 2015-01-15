<?php

class IndexController extends Controller {

    public $layout = 'application.modules.admin.views.layouts.index';
    public $defaultAction = 'index';

    public function actionIndex() {
        $this->render('index', array());
    }
}