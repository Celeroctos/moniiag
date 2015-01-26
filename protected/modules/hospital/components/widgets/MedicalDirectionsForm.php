<?php
class MedicalDirectionsForm extends CWidget {
    protected $data = array();
    public function run() {
        $this->data['model'] = Yii::createComponent('application.modules.hospital.models.forms.FormDirectionForPatientAdd');
        $this->data['wardsList'] = array();

        $wards = Ward::model()->getAll();
        foreach($wards as $key => $ward) {
            $this->data['wardsList'][(string)$ward['id']] = $ward['name'].', '.$ward['enterprise_name'];
        }

        $this->render('application.modules.hospital.components.widgets.views.MedicalDirectionsForm', $this->data);
    }
}

?>