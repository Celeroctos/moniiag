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
	
	public function tableName()
	{
		return 'mis.medpersonal_templates';
	}
	
	public function primaryKey()
	{
		return 'id';
	}
	
	public function rules()
	{
		return [
			['id_medpersonal', 'unique', 'criteria'=>[
							'condition'=>'"id_template"=:id_template',
							'params'=>[
								':id_template'=>$this->id_template
							]
					     ]
			],
		];
	}
}