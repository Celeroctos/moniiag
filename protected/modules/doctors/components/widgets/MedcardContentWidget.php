<?php
class MedcardContentWidget extends CWidget {
    public $formModel = null;
    public $medcard=null;
    public $historyPoints=null;
    public $primaryDiagnosis=null;
    public $secondaryDiagnosis=null;
	public $primaryClinicalDiagnosis=null;
	public $secondaryClinicalDiagnosis=null;
    public $currentPatient = '';
    public $currentSheduleId = '';
    public $note = '';
    public $canEditMedcard = 1;
    public $currentDate = null;
    public $addCommentModel = null;
    public $doctorComment;
    public $numberDoctorComments;
                
    public function run() {
        //var_dump($this->historyPoints);
        //exit();
        if($this->medcard) {
            $greeting = SheduleByDay::model()->findByPk($this->currentSheduleId);
            echo $this->render('application.modules.doctors.components.widgets.views.MedcardContentWidget', array(
                'medcard' => $this->medcard,
                'historyPoints' => $this->historyPoints,
                'primaryDiagnosis' => $this->primaryDiagnosis,
                'secondaryDiagnosis' => $this->secondaryDiagnosis,
                'currentPatient' => $this->currentPatient,
                'currentSheduleId' => $this->currentSheduleId,
                'note' => $this->note,
                'canEditMedcard' => $this->canEditMedcard,
                'currentDate' => date('Y-m-d h:m'),
                'doctorComment' => $this->doctorComment,
                'numberDoctorComments' => $this->numberDoctorComments,
                'addCommentModel' => $this->addCommentModel,
                'currentDoctorId' => $greeting ? $greeting->doctor_id : null,
                'currentOmsId' => $this->medcard['policy_id']
            ));
        }
    }
    
    public function createFormModel() {
        $this->formModel = new FormTemplateDefault();
    }
}
?>