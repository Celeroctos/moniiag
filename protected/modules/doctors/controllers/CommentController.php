<?php
class CommentController extends Controller {
    public $layout = 'index';


    public function actionAdd()
    {
        $model = new FormCommentAdd();
        if(isset($_POST['FormCommentAdd'])) {
            $model->attributes = $_POST['FormCommentAdd'];
            if($model->validate()) {
                $comment = new CommentOms();
                $this->addEditModel($comment, $model, 'Комментарий успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    private function addEditModel($comment, $model, $msg) {
        // Вычленяем номер ОМС
        // По номеру карточки найдём номер полиса
        $medcard = Medcard::model()->find('card_number = :card',array(':card'=>$model->forPatientId));
        $omsNumber = $medcard['policy_id'];

        $comment->comment = $model->commentText;
        $comment->id_oms =  $omsNumber;
        // Это заполняем если даты
        if ($comment->create_date=='' || $comment->create_date==null)
        {
            $comment->create_date =  date('Y-m-d H:i:s');;
        }
        $user = User::model()->find('id=:id', array(':id' => Yii::app()->user->id));
        $comment->employer_id = $user['employee_id'];

        if($comment->save()) {
            // Здесь нужно получить комментарий
            $doctorComment = CommentOms::getTopComment(isset($medcard) ? $medcard : null);
            $doctorNumberComments = count(CommentOms::getComments(isset($medcard) ? $medcard : null));

            $newTopComment = '';


            $commentWidget = $this->createWidget('application.modules.doctors.components.widgets.oneCommentBlock');
            //var_dump("!");
            //exit();
            ob_end_clean();
            $result = $commentWidget->getCommentBlock( $doctorComment, $doctorNumberComments );

            echo CJSON::encode(array('success' => true,
                'text' => $msg,
                'newCommentSection' => $result
            ));
        }
    }

    public function actionEdit()
    {
        $model = new FormCommentAdd();
        if(isset($_POST['FormCommentAdd'])) {
            $model->attributes = $_POST['FormCommentAdd'];
            if($model->validate()) {

                $comment = CommentOms::model()->find('id=:id', array(':id' => $_POST['FormCommentAdd']['commentId']));
                $this->addEditModel($comment, $model, 'Комментарий успешно отредактирован.');

            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }


}