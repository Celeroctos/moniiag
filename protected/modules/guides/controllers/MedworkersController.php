<?php
class MedworkersController extends Controller
{
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';
	
	public function actionList()
	{
		$model=new Medpersonal;
		$this->render('list', [
			'model'=>$model,
		]);
	}
	
	public function actionCreate()
	{
		$modelMedpersonal=new Medpersonal('medworkers.create');
		
		if(isset($_POST['Medpersonal']))
		{
			$modelMedpersonal->attributes=Yii::app()->request->getPost('Medpersonal');
			$transaction=Yii::app()->db->beginTransaction();
			try
			{
				if($modelMedpersonal->save())
				{
					is_array($modelMedpersonal->medcard_templates) ? : $modelMedpersonal->medcard_templates=array();
					foreach($modelMedpersonal->medcard_templates as $key=>$value)
					{ //Вбиваем шаблоны у данного медперсонала
						$modelMedpersonal_templates=new Medpersonal_templates;
						$modelMedpersonal_templates->id_medpersonal=$modelMedpersonal->id;
						$modelMedpersonal_templates->id_template=$value;
						$modelMedpersonal_templates->save(); //валидация уникальности
					}
					$transaction->commit();
					Yii::app()->user->addFlashMessage(WebUser::MSG_SUCCESS, 'Вы успешно добавили должность!');
					$this->redirect(['medworkers/view']);
				}
			} 
			catch (Exception $e) 
			{
				$transaction->rollback(); //откат транзакции.
				Yii::app()->user->addFlashMessage(WebUser::MSG_SUCCESS, 'Ошибка в запросе к БД');
			}
		}
		
		$this->render('create', [
			'model'=>$modelMedpersonal,
			'payment_typeList'=>Medpersonal::getPayment_typeList(),
			'is_medworkerList'=>Medpersonal::getIs_medworkerList(),
			'is_for_pregnantsList'=>Medpersonal::getIs_for_pregnantsList(),
		]);
	}
	
	public function actionUpdate()
	{
		
	}
	
    public function actionView() 
	{
		return $this->actionList();
//        try {
//            // Модель формы для добавления и редактирования записи
//            $formAddEdit = new FormMedworkerAdd;
//
//            // Список вариантов для типов медработников
//            $connection = Yii::app()->db;
//            $typesListDb = $connection->createCommand()
//                ->select('mt.*')
//                ->from('mis.medpersonal_types mt')
//                ->queryAll();
//
//            $typesList = array();
//            foreach($typesListDb as $value) {
//                $typesList[(string)$value['id']] = $value['name'];
//            }
//
//            // Выберем все шаблоны приёмов, чтобы из вывести в интерфейс
//            $allTemplates = MedcardTemplate::model()->findAll();
//            
//            $this->render('view', array(
//                'model' => $formAddEdit,
//                'typesList' => $typesList,
//                'allTemplates' => $allTemplates
//            ));
//        } catch(Exception $e) {
//            echo $e->getMessage();
//        }
    }

    public function actionEdit() {
        $model = new FormMedworkerAdd();
        if(isset($_POST['FormMedworkerAdd'])) {
            $model->attributes = $_POST['FormMedworkerAdd'];
            if($model->validate()) {
                $medworker = Medworker::model()->findByPk($_POST['FormMedworkerAdd']['id']);
                $this->addEditModel($medworker, $model, 'Тип работника успешно отредактирован.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionDelete($id) {
        try {
            $medworker = Medworker::model()->findByPk($id);
            $medworker->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Медицинский работник успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function addEditModel($medworker, $model, $msg) {
        $medworker->name = $model->name;
        $medworker->type = $model->type;
        $medworker->payment_type = $model->paymentType;
        $medworker->is_for_pregnants = $model->isForPregnants;
        $medworker->is_medworker = $model->isMedworker;
        
        $success = true;
        if(!$medworker->save()) {
            $success = false;
        }
        
        // Берём все шаблоны
        $templatesList = MedcardTemplate::model()->findAll();
        // Берём разрешённые шаблоны и убиваем их для данной должности
        $checkedModel = new EnabledTemplate();
        // Удаляем шаблоны
        $checkedModel->deleteByMedpersonal($medworker->id);

        foreach($templatesList as $key => $template) {
            // Если шаблон перечислен - проставляем
            if(isset($_POST['template'.$template['id']])) {
                $checked = new EnabledTemplate();
                $checked->id_template = $template['id'];
                $checked->id_medpersonal = $medworker->id;
                if(!$checked->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'text' => 'Невозможно сохранить шаблон.'));
                }
            }
        }
        
        if($success) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
        }
    }
	
	public function actionIssetDoctorPerMedworker($id) {
		$issetDoctors = Doctor::model()->findAll('post_id = :post_id', array(':post_id' => $id));
		echo CJSON::encode(array(
			'success' => true,
			'doctors' => $issetDoctors
		));
	}

    public function actionAdd() {
        $model = new FormMedworkerAdd();
        if(isset($_POST['FormMedworkerAdd'])) {
            $model->attributes = $_POST['FormMedworkerAdd'];
            if($model->validate()) {
                $medworker = new Medworker();

                $this->addEditModel($medworker, $model, 'Новый тип работника успешно добавлен.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionGet() {
        try {
            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];

            // Фильтры поиска
            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new Medworker();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $order = array(
                'is_medworker_desc' => 'is_medworker',
                'payment_type_desc' => 'payment_type',
                'pregnants' =>  'is_for_pregnants'
            );
            if(isset($order[$sidx])) {
                $sidx = $order[$sidx];
            }

            $medworkers = $model->getRows($filters, $sidx, $sord, $start, $rows);
            foreach($medworkers as &$medworker) {
                if($medworker['is_medworker'] == 1) {
                    $medworker['is_medworker_desc'] = 'Да';
                } else {
                    $medworker['is_medworker_desc'] = 'Нет';
                }

                if($medworker['is_for_pregnants'] == null) {
                    $medworker['is_for_pregnants'] = 0;
                }
                // Тип оплаты
                if($medworker['payment_type'] == 1) {
                    $medworker['payment_type_desc'] = 'Бюджет';
                } elseif($medworker['payment_type'] == 0) {
                    $medworker['payment_type_desc'] = 'ОМС';
                } else {
                    $medworker['payment_type_desc'] = '';
                }
                $medworker['pregnants'] = $medworker['is_for_pregnants'] ? 'Да' : 'Нет';
            }

            echo CJSON::encode(
                array('rows' => $medworkers,
                      'total' => $totalPages,
                      'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }


    public function actionGetone($id) {
        $model = new Medworker();
        $medworker = $model->getOne($id);
        if($medworker['is_for_pregnants'] == null) {
            $medworker['is_for_pregnants'] = 0;
        }
        if($medworker['payment_type'] == 1) {
            $medworker['payment_type_desc'] = 'Бюджет';
        } elseif($medworker['payment_type'] == 0) {
            $medworker['payment_type_desc'] = 'ОМС';
        } else {
            $medworker['payment_type_desc'] = '';
        }
        
        // Берём прочитываем разрешённые для должности шаблоны приёма и записываем в поле "templates"
        $templatesModel = new EnabledTemplate();
        $templates = $templatesModel->getByMedpersonalType($id);
        $medworker['templates'] = array();
        foreach($templates as $key => $template) {
            $medworker['templates'][] = $template['id_template'];

        }
        
        echo CJSON::encode(array('success' => true,
                                 'data' => $medworker)
        );
    }
}

?>