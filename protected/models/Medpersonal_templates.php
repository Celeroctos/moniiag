<?php
/**
 * Класс для работы с шаблонами медперсонала
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
class Medpersonal_templates extends MisActiveRecord
{
	public $id;
	public $id_medpersonal;
	public $id_template;
	
	public function getTableName()
	{
		return 'mis.medpersonal_templates';
	}
}