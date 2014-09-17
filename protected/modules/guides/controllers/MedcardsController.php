<?php
class MedcardsController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'viewprefixes';

    public function actionViewPrefixes() {
		$this->render('viewprefixes', array(
		));
    }
	
	public function actionViewPostfixes() {
		$this->render('viewpostfixes', array(
		));
    }
	
	public function actionViewRules() {
		$this->render('viewrules', array(
		));
    }
	
	public function actionGetPrefixes() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
	public function actionGetPostfixes() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
	
	public function actionGetRules() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>