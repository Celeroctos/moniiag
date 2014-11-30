<?php
class MedcardTemplate extends MisActiveRecord {
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_templates';
    }

    public function getOne($id) {
        try {
            $connection = Yii::app()->db;
            $template = $connection->createCommand()
                ->select('mt.*')
                ->from('mis.medcard_templates mt')
                ->where('mt.id = :id', array(':id' => $id))
                ->queryRow();
            return $template;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
        return array();
    }

    public function getTemplatesByEmployee($id, $pageId = 0)
    {
         try {
            $connection = Yii::app()->db;
            $templates = $connection->createCommand()
            ->select('mt.*')
            ->from('mis.medcard_templates mt')
            ->join('mis.medpersonal_templates mpt', 'mpt.id_template = mt.id')
            ->where('mpt.id_medpersonal = :medpersonal_id AND mt.page_id = :page_id', array(':medpersonal_id' => $id, ':page_id' => $pageId));
            return $templates->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

	public function getTemplateCategories($id) {
		try {
			$row = Yii::app()->db->createCommand()
				->select('json_array_elements(cast(categorie_ids AS json))')
				->from('mis.medcard_templates')
				->where('id = :id', array(':id' => $id))
				->queryRow();
			return $row["json_array_elements"];
		} catch(Exception $e) {
			echo json_encode(array(
				"status" => false,
				"message" => $e->getMessage()
			)); die;
		}
		return null;
	}

	public function addCategoryToTemplate($id, $categoryID) {
		try {
			$connection = Yii::app()->db;
			$template = $connection->createCommand()
				->select('categorie_ids')
				->from('mis.medcard_templates')
				->where('id = :id', array(':id' => $id))
				->queryRow();
            $categories = json_decode($template["categorie_ids"]);
            array_push($categories, $categoryID);
            $this->updateByPk($id, array(
                'categorie_ids' => json_encode($categories)
            ));
		} catch(Exception $e) {
			echo json_encode(array(
				"status" => false,
				"message" => $e->getMessage()
			)); die;
		}
		return array();
	}

	public function setTemplateCategories($id, $cids) {
		try {
			$this->updateByPk($id, array(
				'categorie_ids' => $cids
			));
		} catch(Exception $e) {
			echo json_encode(array(
				"status" => false,
				"message" => $e->getMessage()
			)); die;
		}
		return array();
	}

    public function removeCategoryFromTemplate($id, $categoryID) {
        try {
            $connection = Yii::app()->db;
            $template = $connection->createCommand()
                ->select('categorie_ids')
                ->from('mis.medcard_templates')
                ->where('id = :id', array(':id' => $id))
                ->queryRow();
            $categories = json_decode($template["categorie_ids"]);
            foreach ($categories as $i => &$cid) {
                if (intval($categoryID) === intval($cid)) {
                    array_splice($categories, $i, 1);
                    break;
                }
            }
            $this->updateByPk($id, array(
                'categorie_ids' => json_encode($categories)
            ));
        } catch(Exception $e) {
            echo json_encode(array(
                "status" => false,
                "message" => $e->getMessage()
            )); die;
        }
        return array(
            'id' => $id,
            'categories' => $categories
        );
    }
    
    public function getTemplatesByPageId($id) {
        try {
            $connection = Yii::app()->db;
            $templates = $connection->createCommand()
                ->select('mt.*')
                ->from('mis.medcard_templates mt')
                ->where('mt.page_id = :page_id', array(':page_id' => $id));

            return $templates->queryAll();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $templates = $connection->createCommand()
            ->select('me.*')
            ->from('mis.medcard_templates me');

        if($filters !== false) {
            $this->getSearchConditions($templates, $filters, array(

            ), array(
                'me' => array('id', 'name', 'page', 'categorie'),
            ), array(

            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $templates->order($sidx.' '.$sord);
            $templates->limit($limit, $start);
        }

        return $templates->queryAll();
    }

    public function getTemplateIndexes() {

        $connection = Yii::app()->db;

        $templates = $connection->createCommand()
            ->selectDistinct('me.index')
            ->from(MedcardTemplate::tableName().' me')
            ->order('me.index', 'asc');

        return $templates->queryAll();
    }
}