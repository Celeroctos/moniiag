<?php
class PatientListWidget extends CWidget {

    public $patients = array();
    public $currentSheduleId;
    public $currentPatient = false;
    public $filterModel;
    public $isWaitingLine = false;
    public $tableId = 'doctorPatientList';


    public function run() {
            echo $this->render('application.modules.doctors.components.widgets.views.PatientListWidget', array(
                'patients' => $this->patients,
                'currentSheduleId' => $this->currentSheduleId,
                'currentPatient' => $this->currentPatient,
                'filterModel'=> $this->filterModel,
                'isWaitingLine' => $this->isWaitingLine,
                'tableId' => $this->tableId
            ));
    }

/*    public function createFormModel() {
        $this->formModel = new FormTemplateDefault();
    }*/

    public function getPatientList($patients, $currentSheduleId = false, $currentPatient = false)
    {
//        $this->filterModel = new FormSheduleFilter();
        $result = $this->render('application.modules.doctors.components.widgets.views.PatientListWidget', array(
            'patients' => $patients,
            'currentSheduleId' => $currentSheduleId,
            'currentPatient' => $currentPatient,
            'filterModel'=> $this->filterModel,
            'isWaitingLine' => $this->isWaitingLine,
            'tableId' => $this->tableId
        ),true);

        return $result;

    }

}
?>