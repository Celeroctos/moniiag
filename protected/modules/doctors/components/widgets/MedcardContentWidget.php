<?php
class MedcardContentWidget extends CWidget {
    public $formModel = null;
    public $medcard=null;
    public $historyPoints=null;
    public $primaryDiagnosis=null;
    public $secondaryDiagnosis=null;
    public $currentPatient = '';
    public $currentSheduleId = '';
    public $note = '';
    public $canEditMedcard = 1;
    public $currentDate = null;
                
    public function run() {
        if($this->medcard) {
            echo $this->render('application.modules.doctors.components.widgets.views.MedcardContentWidget', array(
                'medcard' => $this->medcard,
                'historyPoints' => $this->historyPoints,
                'primaryDiagnosis' => $this->primaryDiagnosis,
                'secondaryDiagnosis' => $this->secondaryDiagnosis,
                'currentPatient' => $this->currentPatient,
                'currentSheduleId' => $this->currentSheduleId,
                'note' => $this->note,
                'canEditMedcard' => $this->canEditMedcard,
                'currentDate' => date('Y-m-d h:m')
            ));
        }
    }
    
    public function createFormModel() {
        $this->formModel = new FormTemplateDefault();
    }
}
?>