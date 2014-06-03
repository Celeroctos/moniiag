<?php
class oneCommentBlock extends CWidget {

    public $doctorComment;
    public $numberDoctorComments;

    public function run() {
        $this->printComment(false);
    }

    private function printComment($returningHtml)
    {
        $result = $this->render('application.modules.doctors.components.widgets.views.oneCommentBlock', array(
                'doctorComment' => $this->doctorComment,
                'numberDoctorComments' =>$this->numberDoctorComments
        ),$returningHtml);
        return $result;
    }

    public function getCommentBlock($_doctorComment,$_numberDoctorComments)
    {
        $this->doctorComment = $_doctorComment;
        $this->numberDoctorComments = $_numberDoctorComments;
        return $this->printComment(true);
    }
}
?>