<?php
class allCommentsBlock extends CWidget {

    public $allComments;
    public function run() {
        $this->getCommentsList(false);
    }


    private function printComments($returningHtml)
    {
        $result = $this->render('application.modules.doctors.components.widgets.views.allCommentsBlock', array(
            'allComments'=>$this->allComments
        ),$returningHtml);
        return $result;
    }

    public function getCommentsList($_allComments)
    {
        $this->allComments = $_allComments;
        return $this->printComments(true);
    }
}
?>