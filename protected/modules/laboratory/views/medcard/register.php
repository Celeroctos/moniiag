<?php
/**
 * @var MedcardController $this - Self instance
 */
$this->widget('application.modules.reception.components.widgets.MedcardFormWidget', array(
    'form' => $form,
    'model' => new FormPatientAdd(),
    'privilegesList' => $privilegesList,
    'showEditIcon' => 1,
    'template' => 'application.modules.reception.components.widgets.views.addressEditPopup'
));