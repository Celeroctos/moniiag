<?php
class CommentOms extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.comments_oms';
    }

    public function primaryKey()
    {
        return 'id';
        // Для составного первичного ключа следует использовать массив:
        // return array('pk1', 'pk2');
    }

    public static function treatComment (&$oneComment)
    {
        $commentDateTimeArr = explode(' ', $oneComment['create_date']);
        $commentDateArr= explode('-', $commentDateTimeArr [0]);
        $oneComment['commentDate'] =	$commentDateArr[2].'.'
            .$commentDateArr[1].'.'
            .$commentDateArr[0].' '.$commentDateTimeArr[1] ;


        $oneComment['authorFio'] = $oneComment['last_name'];

        if ($oneComment['first_name']!='')
        {
            $oneComment['authorFio'] .= (' '.mb_substr($oneComment['first_name'],0,1,'utf-8').'.');
        }

        if ($oneComment['middle_name']!='')
        {
            $oneComment['authorFio'] .= (' '.mb_substr($oneComment['middle_name'],0,1,'utf-8'). '.');
        }

        // Проверим - может ли залогиненный пользователь редактировать данное сообщение
        $user = User::model()->find('id=:id', array(':id' => Yii::app()->user->id));
        // var_dump($user);
        // exit();
        if ($user['employee_id']==$oneComment['employer_id'])
        {
            $oneComment['canEditComment']=1;
        }
        else
        {
            $oneComment['canEditComment']=0;
        }

    }

    public static function getComments($medcard)
    {
        if($medcard == null) {
            return array();
        }

        // Выберем policy_id. По полю policy_id выберем комментарии из таблицы, которые относятся к данному полису
        $policyId = $medcard['policy_id'];
        return static::getCommentsByPoliceId($policyId);
    }

    public static function getTopComment($medcard)
    {
        if($medcard == null) {
            return array();
        }

        // Выберем policy_id. По полю policy_id выберем комментарии из таблицы, которые относятся к данному полису
        $policyId = $medcard['policy_id'];
        $result = static::getTopCommentsByPoliceId($policyId);
        if ($result )
        {
            static::treatComment($result);
        }
        return
            $result;
    }

    // ПОлучить все комментарии по id полиса
    public static function getTopCommentsByPoliceId($policeId)
    {
        try {
            $connection = Yii::app()->db;
            $comments = $connection->createCommand()
                ->select('co.*, d.first_name, d.middle_name,d.last_name')
                ->from('mis.comments_oms co')
                ->leftJoin('mis.doctors d', 'd.id = co.employer_id')
                ->where('co.id_oms = :oms ', array(':oms' => $policeId))
                ->order('co.create_date desc')
            ;
            return $comments->queryRow() ;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // ПОлучить все комментарии по id полиса
    public static function getCommentsByPoliceId($policeId)
    {
        try {
            $connection = Yii::app()->db;
            $comments = $connection->createCommand()
                ->select('co.*, d.first_name, d.middle_name,d.last_name')
                ->from('mis.comments_oms co')
                ->leftJoin('mis.doctors d', 'd.id = co.employer_id')
                ->where('co.id_oms = :oms_id ', array(':oms_id' => $policeId))
                ->order('co.create_date desc')
                ;

            return $comments->queryAll() ;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}