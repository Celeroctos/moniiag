<?php
class MedcardCategorie extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_categories';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $categorie = $connection->createCommand()
                ->select('mc.*')
                ->from('mis.medcard_categories mc')
                ->where('mc.id = :id', array(':id' => $id))
                ->queryRow();

            return $categorie;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
        return array();
    }

	public function getMatches($pattern) {
        try {
            $connection = Yii::app()->db;
            $categorie = $connection->createCommand()
                ->select('mc.*')
                ->from('mis.medcard_categories mc')
                ->where('mc.name LIKE \'%:pattern%\'', array(':pattern' => $pattern))
                ->queryRow();

            return $categorie;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
	}

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {

        $categories = Yii::app()->db->createCommand()
            ->select('mc.*, mc2.name as parent')
            ->from('mis.medcard_categories mc')
			->leftJoin('mis.medcard_categories mc2', 'mc.parent_id = mc2.id');

        if($filters !== false) {
            $this->getSearchConditions($categories, $filters, array(), array(
                'mc' => array('id', 'name'),
            ), array());
        }

        if($start !== false && $limit !== false) {
            $categories->limit($limit, $start);
        }

        if($sidx !== false && $sord !== false) {
            $categories->order($sidx.' '.$sord);
        }

        return $categories->queryAll();
    }

    public function getChildren($parentID) {
        try {
            return Yii::app()->db->createCommand()
                ->select("*")
                ->from("mis.medcard_categories c")
                ->where("c.parent_id = :parent_id", array(":parent_id" => $parentID))
				->order("position")
                ->queryAll();
        } catch (Exception $e) {
            print json_encode(array(
                "status" => false,
                "message" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine()
            )); die;
        }
        return null;
    }

    public function getElements($id) {
        try {
            return Yii::app()->db->createCommand()
                ->select("*")
                ->from("mis.medcard_elements c")
                ->where("c.categorie_id = :id", array(":id" => $id))
				->order("position")
                ->queryAll();
        } catch (Exception $e) {
            print json_encode(array(
                "status" => false,
                "message" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine()
            )); die;
        }
        return null;
    }
}