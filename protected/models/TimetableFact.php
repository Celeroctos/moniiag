<?php
class TimetableFact extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.timetable_facts';
    }

    public function getForSelect()
    {
        try
        {
            $connection = Yii::app()->db;
            $facts = $connection->createCommand()
                ->select('*')
                ->from(TimetableFact::tableName().' tf');
            $result = $facts ->queryAll();
            return $result;
        }
        catch (Exception $e)
        {
            var_dump($e);
            exit();
        }
    }
}

?>