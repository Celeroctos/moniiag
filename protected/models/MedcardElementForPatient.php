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

            /*
            $elements = $connection->createCommand()
                ->select('mep.*')
                ->from('mis.medcard_elements_patient mep')
                ->where('mep.path in ('. $pathsToSelect .')
                        AND mep.greeting_id = :greeting_id',
                    array(':greeting_id' => $greetingId));
            $elements->order('element_id, path, history_id desc');
			$allElements =  $elements->queryAll();
            */
            $allElements = MedcardElementForPatient::model()->findAllBySql(
                'SELECT mep.* FROM mis.medcard_elements_patient mep WHERE mep.path in ('
                . $pathsToSelect .
                ') AND mep.greeting_id = '.$greetingId.' ORDER BY mep.element_id, mep.path,mep.history_id desc'
            );


           // var_dump($allElements);
           // exit();
            $currentElement = false;
            $currentPath = false;
            $result = array();

            // Если есть элементы - берём его id для дальнейшего сравнения
            if (count($allElements )>0)
            {
                $currentElement  = $allElements[0]['element_id'];
                $currentPath = $allElements[0]['path'];
                array_push($result,$allElements[0]);
            }
            // Дальше проверяем в цикле - если id элемента поменялся
            //    о сравнению с предыдущими строками - то нужно запихать текущую строку в результат
            //var_dump();
            //exit();
            foreach ($allElements as $oneRecord)
            {
                $needAddToResult = false;

                if ( ($oneRecord['element_id']!=$currentElement) || ($oneRecord['element_id']!=$currentElement)  )
                {
                    //$currentElement  = $oneRecord['element_id'];
                    //array_push($result,$oneRecord);
                    $needAddToResult = true;
                }
                else
                {
                    //  ИД-шники равны, НО
                    // Надо проверить - не поменялся ли путь. Если поменялся - надо добавлять элемент
                    if ($oneRecord['path']!=$currentPath)
                    {
                        $needAddToResult = true;
                    }
                }

                // ПРоверяем - надо ли добавлять элемент и если надо - добавляем
                if ($needAddToResult )
                {
                    $currentElement  = $oneRecord['element_id'];
                    $currentPath  = $oneRecord['path'];
                    array_push($result,$oneRecord);
                }
            }

            //var_dump($result);
            //exit();

			return $result;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

    // Выдаёт список шаблонов-рекоммендаций, которые поменяли за приём по номеру приёма
    public static function getRecommendationTemplatesInGreeting($greetingId)
    {
        try {
            $connection = Yii::app()->db;
            $templates = $connection->createCommand()
                ->selectDistinct('template_id, template_name')
                ->from('mis.medcard_elements_patient mep')
                ->where('mep.greeting_id = :greetingId
                        AND (NOT(template_id is NULL))
                        AND template_page_id = 1',
                        array(
                            ':greetingId' => $greetingId
                        )
                );
            return $templates->queryAll();

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
			//var_dump($e);
			echo $e->getMessage();
            exit();
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
                                              WHERE
                                                    mep2.greeting_id = mep.greeting_id
                                                    AND mep2.path = mep.path
                                                    AND mep2.medcard_id = :medcard_id))
						
							
				)', array(':medcard_id' => $medcardId));
            $result = $values->queryAll();
			//var_dump($result);
            //exit();
            return $result;

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

    public function findGreetingTemplate($greetingId, $templateId)
    {
        try{
            // Выберем из таблицы medcard_records записи по приёму и номеру шаблона, отсортируем по record_id, выберем самую младшую
            //    и по record_id этой записи выберем из таблицы элементов медкарты элементы медкарты, которые и вернём в конце функции
            $connection = Yii::app()->db;
            $records = $connection->createCommand()
                ->select('mr.*')
                ->from('mis.medcard_records mr')
                //    ->leftJoin('mis.medcard_templates mt', 'mep.template_id = mt.id')
                ->where('mr.greeting_id = :greetingId AND template_id = :templateId',
                    array(
                        ':greetingId' => $greetingId,
                        ':templateId' => $templateId
                    )
                )
                ->order('record_id desc');
            $medcardRecords = $records->queryAll();


            // Берём нулевую строку (первую)
            //  Берём у неё record_id и по ней и приёму выбираем элементы медкарты
            $values = $connection->createCommand()
                ->select('mep.*')
                ->from('mis.medcard_elements_patient mep')
                //    ->leftJoin('mis.medcard_templates mt', 'mep.template_id = mt.id')
                //->where('mep.greeting_id = :greetingId AND ( ((mep.record_id = :recordId) AND NOT (mep.element_id=-1) ) OR  ((mep.record_id = 1) AND (mep.element_id=-1) )  )',
                                ->where('mep.greeting_id = :greetingId AND ( ((mep.record_id = :recordId) AND NOT (mep.element_id=-1) ) OR  (mep.element_id=-1) )',
                    array(
                        ':greetingId' => $greetingId,
                        ':recordId' => $medcardRecords[0]['record_id']
                    ))
                ->order('element_id, history_id desc');
            $elements = $values->queryAll();
            //var_dump($elements );
           // exit();
            return $elements;
        }
        catch(Exception $e)
        {
            var_dump($e);
            exit();
        }
    }

    // Найти все конечные состояния полей, изменённых во время приёма
    public function findAllPerGreeting($greetingId, $pathForFind = false, $operator = 'eq', $recommendationOnly=false) {
        try {
            $eqPath = ($pathForFind !== false) && ($operator == 'eq');
            $likePath = ($pathForFind !== false) && ($operator == 'like');
            $connection = Yii::app()->db;
            $values = $connection->createCommand()
                ->select('mep.*')
                ->from('mis.medcard_elements_patient mep')
                //    ->leftJoin('mis.medcard_templates mt', 'mep.template_id = mt.id')
                ->where('mep.greeting_id = :greetingId', array(':greetingId' => $greetingId))
                //->order('element_id, history_id desc');
                ->order('path, history_id desc');
            $elements = $values->queryAll();
            //var_dump($elements );
            //exit();
            //===========>
            $results = array();

            $currentElementNumber = false;
            $currentMaximum = false;
            if (count($elements )>0)
            {
                $currentElementPath = $elements[0]['path'];
                $currentMaximum = $elements[0]['history_id'];
            }

            //var_dump($elements );
            //exit();

            // Проверяем условие max history_id
            foreach ($elements as $oneElement)
            {
                if ($currentElementPath!=$oneElement['path'])
                {
                    $currentElementPath=$oneElement['path'];
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

            //var_dump($results);
            //exit();

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

            // ПРоверка печати рекомендаций
            if ($recommendationOnly)
            {
                $tempResult = array();
                foreach ($results as $oneElement)
                {
                    if ($oneElement['template_page_id']=='1')
                    {
                        array_push($tempResult,$oneElement);
                    }
                }
                $results = $tempResult;
            }

            //======>
            //var_dump($results);
            //exit();
            return $results;
        } catch(Exception $e) {
            echo $e->getMessage();
            exit();
        }
    }
}

?>