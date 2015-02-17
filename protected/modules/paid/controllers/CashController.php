<?php
/**
 * Контроллер для работы кассы
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
class CashController extends Controller
{
	public $layout='index';
	
	public function actionIndex()
	{
		$this->render('index');
	}
}