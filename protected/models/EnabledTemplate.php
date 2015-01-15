<?php
class EnabledTemplate extends MisActiveRecord  {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medpersonal_templates';
    }


    public function getAll() {
        try {

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

    }
  
    public function getByMedpersonalType($id) {
        try {
            $connection = Yii::app()->db;
            $checked = $connection->createCommand()
                ->select('mt.*')
                ->from('mis.medpersonal_templates mt')
                ->where('mt.id_medpersonal = :id', array(':id' => $id));
            return $checked->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }  
	
	public function getByTemplateId($id) {
        try {
            $connection = Yii::app()->db;
            $checked = $connection->createCommand()
                ->select('mt.*, mw.*')
                ->from('mis.medpersonal_templates mt')
				->join(Medworker::model()->tableName().' mw', 'mt.id_medpersonal = mw.id')
                ->where('mt.id_template = :id', array(':id' => $id));
            return $checked->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }  
  
        // Удаление связи шаблоны->должность
    public function deleteByMedpersonal($id) {
        try {
            $connection = Yii::app()->db;
            $checked = $connection->createCommand()
            ->delete('mis.medpersonal_templates', 'id_medpersonal =:id_medpersonal', array(':id_medpersonal' => $id));
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
  
    public function getOne($id) {
        try {


        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
    

}

?>