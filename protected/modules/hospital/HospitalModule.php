<?php
class HospitalModule extends CWebModule {
	public $modules = array(
		'class' => 'application.modules.hospital.modules.components.ComponentsModule',
		'import'=>array(
			'application.modules.hospital.modules.components.models.ar.*',
			'application.modules.hospital.modules.components.models.forms.*',
			'application.modules.hospital.modules.components.components.*',
			'application.modules.hospital.modules.components.controllers.*'
		),
	);
}

?>