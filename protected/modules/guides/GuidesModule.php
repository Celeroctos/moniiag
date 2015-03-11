<?php

class GuidesModule extends CWebModule {
    public function init() {
        $this->setModules(
            array(
                'laboratory' => array(
                    'class' => 'application.modules.guides.modules.laboratory.LaboratoryModule',
                    'import'=>array(
                        'application.modules.guides.modules.laboratory.components.*',
                        'application.modules.guides.modules.laboratory.controllers.*'
                    )
                )
            )
        );
    }	
}
?>