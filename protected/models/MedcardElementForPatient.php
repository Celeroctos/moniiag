<?php
class MedcardElementForPatient extends MisActiveRecord {
    private $typesList = array( // Типы контролов
        'Текстовое поле',
        'Текстовая область',
        'Выпадающий список',
        'Выпадающий список с множественным выбором',
        'Редактируемая таблица',
        'Числовое поле',
        'Дата'
    );

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'mis.medcard_elements_patient';
    }

    public function getOne($medcardId, $fieldId) {
        try {
            $connection = Yii::app()->db;
            $element = $connection->createCommand()
                ->select('mcp.*')
                ->from('mis.medcard_elements_patient mcp')
                ->where('mcp.medcard_id = :medcard_id AND mcp.element_id = :element_id', array(':medcard_id' => $medcardId, ':element_id' => $fieldId))
                ->queryRow();

            return $element;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getTypesList() {
        return $this->typesList;
    }

    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        $connection = Yii::app()->db;
        $elements = $connection->createCommand()
            ->select('me.*, mg.name as guide, mc.name as categorie')
            ->from('mis.medcard_elements me')
            ->leftJoin('mis.medcard_guides mg', 'mg.id = me.guide_id')
            ->leftJoin('mis.medcard_categories mc', 'mc.id = me.categorie_id');

        if($filters !== false) {
            $this->getSearchConditions($elements, $filters, array(

            ), array(
                'me' => array('id', 'label'),
                'mg' => array('categorie'),
                'mc' => array('guide')
            ), array(
                'categorie' => 'name',
                'guide' => 'name'
            ));
        }

        if($sidx !== false && $sord !== false && $start !== false && $limit !== false) {
            $elements->order($sidx.' '.$sord);
            $elements->limit($limit, $start);
        }

        return $elements->queryAll();
    }

    public function getElementsByCategorie($id) {
        try {
            $connection = Yii::app()->db;
            $elements = $connection->createCommand()
                ->select('mc.*')
                ->from('mis.medcard_elements mc')
                ->where('mc.categorie_id = :categorie_id', array(':categorie_id' => $id))
                ->queryAll();

            return $elements;

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

	public static function getTemplateName($recordId, $medcardId)
	{
		
		try {
			$connection = Yii::app()->db;
			
			
			$values = $connection->createCommand()
				->selectDistinct('SUBSTR(CAST(mep.change_date AS text), 0, CHAR_LENGTH(CAST(mep.change_date AS text)) - 2) AS change_date, mep.record_id, mep.medcard_id, mep2.template_name')
				->from('mis.medcard_elements_patient mep')
				->join('mis.medcard_elements_patient as mep2', 'mep.categorie_id=mep2.real_categorie_id')
				->where('mep.medcard_id = :medcard_id AND mep.record_id=:ri', array(':medcard_id' => $medcardId, ':ri'=>$recordId));
		
			$result = $values->queryAll();

			if (count($result)==0)
			{
				return "";
			}
			else
			{
				return $result[0]['template_name'];
			}
			return 0;
			
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}


	public function getLatestStateOfGreeting($greetingId, $elementPaths) {
		try {
			//var_dump($elementPaths);
			//exit();
			// Если массив путей пуст - вернём пустой массив
			if (count($elementPaths)==0)
				return array();
			
			// Соберём строку из путей для условия WHERE IN
			$pathsToSelect = '';
			foreach ($elementPaths as $onePath)
			{
				if ($pathsToSelect!='')
				{
					$pathsToSelect .= ',';
				}
				$pathsToSelect .= ('\'' . $onePath. '\'');
			}

			$connection = Yii::app()->db;

            // Старый код. Скорее всего потом не нужен будет
			/*$elements = $connection->createCommand()
				->select('mep.*')
				->from('mis.medcard_elements_patient mep')
				->where('mep.path in ('. $pathsToSelect .')
                        AND mep.greeting_id = :greeting_id
						AND mep.history_id = 
							(SELECT MAX(mep2.history_id)
							FROM mis.medcard_elements_patient mep2
							WHERE mep.path=mep2.path
							AND mep2.greeting_id = mep.greeting_id)',
					array(':greeting_id' => $greetingId));
*/
            $elements = $connection->createCommand()
                ->select('mep.*')
                ->from('mis.medcard_elements_patient mep')
                ->where('mep.path in ('. $pathsToSelect .')
                        AND mep.greeting_id = :greeting_id',
                    array(':greeting_id' => $greetingId));
            $elements->order('element_id, history_id desc');



			$allElements =  $elements->queryAll();
            $currentElement = false;
            $result = array();

            // Если есть элементы - берём его id для дальнейшего сравнения
            if (count($allElements )>0)
            {
                $currentElement  = $allElements[0]['element_id'];
                array_push($result,$allElements[0]);
            }
            // Дальше проверяем в цикле - если id элемента поменялся
            //    о сравнению с предыдущими строками - то нужно запихать текущую строку в результат
            foreach ($allElements as $oneRecord)
            {
                if ($oneRecord['element_id']!=$currentElement  )
                {
                    $currentElement  = $oneRecord['element_id'];
                    array_push($result,$oneRecord);
                }
            }
			return $result;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}


    public function getMaxHistoryPointId($element, $medcardId, $greetingId) {
        try {
            $connection = Yii::app()->db;
            $elements = $connection->createCommand()
                ->select('MAX(mep.history_id) as history_id_max')
                ->from('mis.medcard_elements_patient mep')
                ->where('mep.medcard_id = :medcard_id
                        AND mep.greeting_id = :greeting_id
                        AND mep.path = :path', array(':path' => $element['path'],
                                                     ':medcard_id' => $medcardId,
                                                     ':greeting_id' => $greetingId));
            return $elements->queryRow();

        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

	
	public function getHistoryPointsByCardId($medcard) {
		try {


            return MedcardRecord::getHistoryMedcardByCardId($medcard);

		} catch(Exception $e) {
			var_dump($e);
			exit();
			echo $e->getMessage();
		}
	}

    public function getHistoryPoints($medcard) {
		return $this->getHistoryPointsByCardId($medcard['card_number']);
    }

	public function getValuesByDate($date, $medcardId, $historyId) {
		try {
			$connection = Yii::app()->db;		
						
			$values = $connection->createCommand()
				->select('mep.*, me.type')
				->from('mis.medcard_elements_patient mep')
				->leftJoin('mis.medcard_elements me', 'me.id = mep.element_id')
				->where('(mep.medcard_id = :medcard_id AND mep.record_id = :record_id AND mep.element_id>0 AND mep.is_record=1)', 
					array(':medcard_id' => $medcardId,
						':record_id' => $historyId)
					)
					->orWhere('(
							(mep.element_id=-1)
							
												AND (mep.record_id = (SELECT MAX(mep2.record_id)
                                              FROM mis.medcard_elements_patient mep2
                                              WHERE mep2.real_categorie_id = mep.real_categorie_id
                                                    AND mep2.medcard_id = :medcard_id))
						
							
				)', array(':medcard_id' => $medcardId));
			return $values->queryAll();

		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	// Метод возвращает максимальную record id в таблице записей медкарты для данной медкарты
	public static function getMaxRecordId($medcardId)
	{
		try {
			$connection = Yii::app()->db;
			$values = $connection->createCommand()
				->select('MAX(mep.record_id) AS max_val')
				->from('mis.medcard_elements_patient mep')
				->where('mep.medcard_id = :medcard_id', 
					array(':medcard_id' => $medcardId)
					);
			
			$result = $values->queryAll();
			
			if (count($result)==0)
			{
				return 0;
			}
			else
			{
				return $result[0]['max_val'];
			}
			return 0;
			
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

    // Найти все конечные состояния полей, изменённых во время приёма
	public function findAllPerGreeting($greetingId, $pathForFind = false, $operator = 'eq', $recommendationOnly=false) {
			try {





            /*$connection = Yii::app()->db;
            $values = $connection->createCommand()
				->select('mep.*')
                ->from('mis.medcard_elements_patient mep')
                ->where('mep.greeting_id = :greetingId', array(':greetingId' => $greetingId))
                ->andWhere('mep.history_id = (SELECT MAX(mep2.history_id)
                                              FROM mis.medcard_elements_patient mep2
                                              WHERE mep2.element_id = mep.element_id
                                                    AND mep2.greeting_id = :greetingId)', array(':greetingId' => $greetingId));
                if($pathForFind !== false) {
                    if($operator == 'eq') {
                        $values->andWhere('mep.categorie_id = (SELECT DISTINCT mep3.categorie_id
                                                           FROM mis.medcard_elements_patient mep3
                                                           WHERE
                                                                mep3.path = :path
                                                                AND mep3.greeting_id = :greetingId)', array(':greetingId' => $greetingId,
                                                                                                            ':path' => $pathForFind));
                    } elseif($operator == 'like') {
                        $values->andWhere(array('like', 'mep.path', array($pathForFind.'%')));
                    }
                }
				
				// Если печатаем только рекоммендации
				if ($recommendationOnly)
				{
				$values->andWhere('mep.template_page_id=1');
				}
				
                $values->group('mep.element_id,
                         mep.history_id,
                         mep.medcard_id,
                         mep.value,
                         mep.change_date,
                         mep.greeting_id,
                         mep.categorie_name,
                         mep.categorie_id,
                         mep.path
                         ');

            return $values->queryAll();
            */
                $eqPath = ($pathForFind !== false) && ($operator == 'eq');
                $likePath = ($pathForFind !== false) && ($operator == 'like');
                $connection = Yii::app()->db;
                $values = $connection->createCommand()
                    ->select('mep.*')
                    ->from('mis.medcard_elements_patient mep')
                    ->where('mep.greeting_id = :greetingId', array(':greetingId' => $greetingId))
                    ->order('element_id, history_id desc');
                $elements = $values->queryAll();
                $results = array();

                $currentElementNumber = false;
                $currentMaximum = false;
                if (count($elements )>0)
                {
                    $currentElementNumber = $elements[0]['element_id'];
                    $currentMaximum = $elements[0]['history_id'];
                }
                // Проверяем условие max history_id
                foreach ($elements as $oneElement)
                {
                    if ($currentElementNumber!=$oneElement['element_id'])
                    {
                        $currentElementNumber=$oneElement['element_id'];
                        $currentMaximum = $oneElement['history_id'];
                        array_push($results,$oneElement);
                    }
                    else
                    {
                        if ($oneElement['history_id']==$currentMaximum)
                        {
                            array_push($results,$oneElement);
                        }
                    }


                }

                // Проверяем likePath
                if ($likePath)
                {
                    $tempResult = array();
                    foreach ($results as $oneElement)
                    {
                        // ПРоверяем на то, что путь начинается на подстроку pathForFind
                        if (strpos($oneElement['path'],$pathForFind)===0)
                        {
                            array_push($tempResult,$oneElement);
                        }
                    }
                    $results = $tempResult;

                }

                // ПРоверяем eqPath
                if ($eqPath)
                {
                    $tempResult = array();
                    foreach ($results as $oneElement)
                    {
                        if ($oneElement['path']==$pathForFind)
                        {
                            array_push($tempResult,$oneElement);
                        }
                    }
                    $results = $tempResult;
                }
                return $results;
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>