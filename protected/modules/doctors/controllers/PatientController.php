<?php
class PatientController extends Controller {
    public function actionGetHistoryMedcard() {
        if(!Yii::app()->request->isAjaxRequest) {
            exit('Error!');
        }
        //echo CJSON::encode(array('success' => 'true','data' => '')); exit();
        if(!isset($_GET['date'], $_GET['medcardid'])) {
            echo CJSON::encode(array('success' => true,
                                     'data' => 'Не хватает данных для запроса!'));
        }
        //echo CJSON::encode(array('success' => 'true','data' => '')); exit();
        $categorieWidget = $this->createWidget('application.modules.doctors.components.widgets.CategorieViewWidget');
        //ob_end_clean();
        //echo CJSON::encode(array('success' => 'true','data' => '')); exit();
        //exit();
        //var_dump($categorieWidget );
        //exit();
        
        $categorieWidget->createFormModel();
		$historyArr = $categorieWidget->getFieldsHistoryByDate($_GET['date'], $_GET['medcardid'],$_GET['historyPointId']); // Получаем поля для всех полей относительно хистори

		ob_end_clean();
        echo CJSON::encode(array('success' => 'true',
                                 'data' => $historyArr));
        exit();
    }


    public function actionSaveDiagnosis() {
        if(!isset($_GET['greeting_id'])) {
            exit('Не выбран приём!');
        }
        // Удалить предыдущие поставленные диагнозы
		//   по МКБ10
        PatientDiagnosis::model()->deleteAll('greeting_id = :greeting_id', array(':greeting_id' => $_GET['greeting_id']));
		// Клинические
		ClinicalPatientDiagnosis::model()->deleteAll('greeting_id = :greeting_id', array(':greeting_id' => $_GET['greeting_id']));
		
		// Сохраним первичные по МКБ
        if(isset($_GET['primary'])) {
            $primary = CJSON::decode($_GET['primary']);
            foreach($primary as $id) {
                $row = new PatientDiagnosis();
                $row->mkb10_id = $id;
                $row->greeting_id = $_GET['greeting_id'];
                $row->type = 0; // Первичный диагноз
                if(!$row->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'error' => 'Не могу сохранить первичный диагноз!'));
                    exit();
                }
            }
        }
		// Сохраним сопуствующие по МКБ
        if(isset($_GET['secondary'])) {
            $secondary = CJSON::decode($_GET['secondary']);
            foreach($secondary as $id) {
                $row = new PatientDiagnosis();
                $row->mkb10_id = $id;
                $row->greeting_id = $_GET['greeting_id'];
                $row->type = 1; // Сотпутствующий диагноз
                if(!$row->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'error' => 'Не могу сохранить сопутствующий диагноз!'));
                    exit();
                }
            }
        }
		
		// Сохраним первичные клинические
		if(isset($_GET['clinPrimary'])) {
			$clinPrimary = CJSON::decode($_GET['clinPrimary']);
			foreach($clinPrimary as $id) {
				$row = new ClinicalPatientDiagnosis();
				$row->diagnosis_id = $id;
				$row->greeting_id = $_GET['greeting_id'];
				$row->type = 0; // Первичный диагноз
				if(!$row->save()) {
					echo CJSON::encode(array('success' => false,
						'error' => 'Не могу сохранить первичный диагноз!'));
					exit();
				}
			}
		}
		// Сохраним сопутсвующие клинические
		if(isset($_GET['clinSecondary'])) {
			$clinSecondary = CJSON::decode($_GET['clinSecondary']);
			foreach($clinSecondary as $id) {
				$row = new ClinicalPatientDiagnosis();
				$row->diagnosis_id = $id;
				$row->greeting_id = $_GET['greeting_id'];
				$row->type = 1; // Сотпутствующий диагноз
				if(!$row->save()) {
					echo CJSON::encode(array('success' => false,
						'error' => 'Не могу сохранить сопутствующий диагноз!'));
					exit();
				}
			}
		}
		
        if(isset($_GET['note']) && trim($_GET['note']) != '') {
            $greeting = SheduleByDay::model()->findByPk($_GET['greeting_id']);
            if($greeting != null) {
                $greeting->note = $_GET['note'];
                $greeting->save();
            }
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => array()));
    }

    // Клонирование элемента (категории)
	public function actionCloneElement($pr_key, $recordId, $level = 0, $levelParts = array()) {
        $keyParts = explode('|', $pr_key);
        /* Порядок полей в ключе:
         * - Номер карты
         * - ID приёма
         * - Путь иерархии
         * - ID категории-родителя
         * - ID категории
         */
        $historyCategorie = MedcardElementForPatient::model()->find('categorie_id = :categorie_id AND greeting_id = :greeting_id AND path = :path AND medcard_id = :medcard_id', array(
            ':categorie_id' => $keyParts[3],
            ':greeting_id' => $keyParts[1],
            ':path' => $keyParts[2],
            ':medcard_id' => $keyParts[0])
        );

        if($historyCategorie == null) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array()));
            exit();
        }

		$recordId = MedcardElementForPatient::getMaxRecordId($keyParts[0]);

        $currentDate = date('Y-m-d h:i');

        // Создаём новую категорию, путь делаем + 1 у конечного элемента
        $medcardCategorieClone = new MedcardElementForPatient();
        $medcardCategorieClone->medcard_id = $keyParts[0];
        $medcardCategorieClone->history_id = 1;
		$medcardCategorieClone->record_id = $recordId;
        $medcardCategorieClone->greeting_id = $keyParts[1];
        $medcardCategorieClone->categorie_name = $historyCategorie->categorie_name;
        $medcardCategorieClone->is_wrapped = 0;
        $medcardCategorieClone->categorie_id = $historyCategorie->categorie_id;
        $medcardCategorieClone->element_id = -1;
        //$medcardCategorieClone->change_date = $currentDate;
		$medcardCategorieClone->change_date = date('Y-m-d H:i');
		$medcardCategorieClone->type = -1;
        $medcardCategorieClone->template_id = $historyCategorie->template_id; // TODO : вынуть идентификаторы шаблона
        $medcardCategorieClone->template_name = $historyCategorie->template_name; // TODO : вынуть имя шаблона
        $medcardCategorieClone->is_dynamic = 0; // Клонированные категории не должны быть динамичными
        $medcardCategorieClone->real_categorie_id = $historyCategorie->real_categorie_id;
        // Путь: бьём на составляющие и прибавляем к последнему элементу в позиции + 1 к максимальному номеру в иерархии в данной категории
        $pathParts = explode('.', $historyCategorie->path);
        // Ищем все элементы в иерархии, которые по позиции больше, чем текущий. Составим путь категории-родителя
        $elementsInCategorie = MedcardElementForPatient::model()->findAllPerGreeting($keyParts[1], $historyCategorie->path);
        $maxPosition = $pathParts[count($pathParts) - 1];
        foreach($elementsInCategorie as $categorie) {
            $pathParts2 = explode('.', $categorie['path']);
            // Сравниваются пути с одинаковым количество элементов
            if($maxPosition < $pathParts2[count($pathParts2) - 1]) {
                $maxPosition = $pathParts2[count($pathParts2) - 1];
            }
        }

        array_pop($pathParts);
        $pathParts[] = $maxPosition + 1;
        $savedCategoriePosition = $maxPosition + 1; // Сохраняем позицию для изменения элементов пути
        $medcardCategorieClone->path = implode('.', $pathParts);
        if(!$medcardCategorieClone->save()) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array()));
            exit();
        }

        // Теперь смотрим все то, что находится в категории. И тоже клонируем, причём рекурсивно: там могут быть вложенные категории
        $elementsInCategorie = MedcardElementForPatient::model()->findAllPerGreeting($keyParts[1], $keyParts[2], 'like');
        $historyTransform = array();
        $historyTransformToId = array();
        $dependencesAnswer = array();

        foreach($elementsInCategorie as $element) {
            // Если путь полностью совпадает, это категория, которая уже добавлена, её надо пропустить
            if($keyParts[2] == $element['path']) {
                continue;
            }
            $pathParts2 = explode('.', $element['path']);
            $pathParts2[count($pathParts) - 1] =  $savedCategoriePosition;

            $historyCategorieElementNext = new MedcardElementForPatient();
            $historyCategorieElementNext->history_id = 1;
			$historyCategorieElementNext->record_id = $recordId;
            $historyCategorieElementNext->medcard_id = $element['medcard_id'];
            $historyCategorieElementNext->greeting_id = $element['greeting_id'];
            $historyCategorieElementNext->path = implode('.', $pathParts2);
            $historyCategorieElementNext->is_wrapped = $element['is_wrapped'];
            $historyCategorieElementNext->categorie_id = $element['categorie_id'];
            $historyCategorieElementNext->categorie_name = $element['categorie_name'];
            $historyCategorieElementNext->element_id = $element['element_id'];
            $historyCategorieElementNext->label_before = $element['label_before'];
            $historyCategorieElementNext->label_after = $element['label_after'];
            $historyCategorieElementNext->size = $element['size'];
            //$historyCategorieElementNext->change_date = $currentDate;
			$historyCategorieElementNext->change_date = date('Y-m-d H:i');
            $historyCategorieElementNext->type = $element['type'];
            $historyCategorieElementNext->guide_id = $element['guide_id'];
			$historyCategorieElementNext->allow_add = $element['allow_add'];
            $historyCategorieElementNext->real_categorie_id = $element['real_categorie_id'];
            $historyCategorieElementNext->config = $element['config'];

            if(!$historyCategorieElementNext->save()) {
                exit('Не могу отклонировать элемент '.$element['path']);
            }

            // Теперь смотрим на зависимости клонированного элемента
            // Зависимости могут быть инициатор + зависимый, просто зависимый от стороннего
            $dependences = MedcardElementPatientDependence::model()->findAll('
                medcard_id = :medcard_id
                AND greeting_id = :greeting_id
                AND element_path = :element_path
            ', array(
               ':medcard_id' => $keyParts[0],
               ':greeting_id' => $keyParts[1],
               ':element_path' => $element['path']
            ));

            $historyTransform[$element['path']] =  $historyCategorieElementNext->path; // Сохраняем для апдейта зависимостей контрола
            $historyTransformToId[$historyCategorieElementNext->path] = $element['element_id']; // Для выдачи ответа айдишника инициатора зависимости
            foreach($dependences as $dependence) {
                $newDep = new MedcardElementPatientDependence();
                $newDep->greeting_id = $dependence['greeting_id'];
                $newDep->medcard_id = $dependence['medcard_id'];
                $newDep->element_path = $historyCategorieElementNext->path;
                $newDep->action = $dependence['action'];
                $newDep->value = $dependence['value'];
                $newDep->element_id = $dependence['element_id'];
                // А вот эти поля ставим пока в прежнем состоянии. Как только будем выяснять зависимые элементы (см. ниже), нужно будет прогнать процедуру апдейта
                $newDep->dep_element_id = $dependence['dep_element_id'];
                $newDep->dep_element_path = $dependence['dep_element_path'];
                if(!$newDep->save()) {
                    exit('Не могу сохранить клонированную зависимость (элемент-инициатор)!');
                }
            }
        }


        foreach($historyTransform as $key => $transform) {
            $elements = MedcardElementPatientDependence::model()->findAll(
                'medcard_id = :medcard_id
                AND greeting_id = :greeting_id
                AND element_path = :element_path',
                array(
                    ':medcard_id' => $keyParts[0],
                    ':greeting_id' => $keyParts[1],
                    ':element_path' => $transform
                )
            );

            if(count($elements) > 0) {
                $dependencesAnswer[] = array(
                    'path' => $transform,
                    'elementId' => $historyTransformToId[$transform],
                    'dependences' => array(
                        'list' => array()
                    )
                );
            }

            $list = array();
            foreach($elements as $element) {
                MedcardElementPatientDependence::model()->updateAll(array(
                   'dep_element_path' => $historyTransform[$element['dep_element_path']]
                ), 'medcard_id = :medcard_id
                    AND greeting_id = :greeting_id
                    AND element_path = :element_path
                    AND dep_element_path = :dep_element_path',
                    array(
                        ':medcard_id' => $keyParts[0],
                        ':greeting_id' => $keyParts[1],
                        ':element_path' => $element['element_path'],
                        ':dep_element_path' => $element['dep_element_path']
                    )
                );
                $list[] = array(
                  'elementId' => $element['dep_element_id'],
                  'action' => $element['action'],
                  'value' => $element['value']
                );
            }

            if(count($elements) > 0) {
                $dependencesAnswer[count($dependencesAnswer) - 1]['dependences']['list'] = $list;
            }
        }

        echo CJSON::encode(array(
                 'success' => true,
                 'data' => array(
                    'pk_key' =>  $keyParts[0].'|'.$keyParts[1].'|'.$medcardCategorieClone->path.'|'.$keyParts[3].'|'.$keyParts[4],
                    'dependences' => $dependencesAnswer, // Массив выстроенных зависимостей (для того, чтобы можно было отобразить зависимости "на лету" без перезагрузки страницы
                    'repath' =>  $historyTransform // Массив для реплейса путей
                 )
              )
          );
    }

    // UnКлонирование элемента (категории)
	public function actionUnCloneElement($pr_key) {
        $keyParts = explode('|', $pr_key);
        /* Порядок полей в ключе:
         * - Номер карты
         * - ID приёма
         * - Путь иерархии
         * - ID категории-родителя
         * - ID категории реальный
         */
        $elements = MedcardElementForPatient::model()->findAllPerGreeting($keyParts[1], $keyParts[2], 'like');
        foreach($elements as $element) {
            MedcardElementForPatient::model()->findByPk(
                array(
                    'path' => $element['path'],
                    'greeting_id' => $element['greeting_id'],
                    'medcard_id' => $element['medcard_id'],
                    'categorie_id' => $element['categorie_id'],
                    'history_id' => $element['history_id']
                )
            )->delete();

            MedcardElementPatientDependence::model()->deleteAll(
                'medcard_id = :medcard_id
                 AND greeting_id = :greeting_id
                 AND (element_path = :element_path
                      OR dep_element_path = :element_path)',
                array(
                    ':element_path' => $element['path'],
                    ':greeting_id' => $element['greeting_id'],
                    ':medcard_id' => $element['medcard_id'],
                )
            );
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => array()));
    }

    public function actionViewSearch() {
        $this->render('searchPatient', array());
    }
}
?>