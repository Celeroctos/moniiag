<?php
class WardsController extends Controller 
{
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';
	
	public function actionList()
	{
		$model=new Ward;
		$this->render('list', ['model'=>$model]);
	}
	
	public function actionCreate()
	{
		$model=new Ward('wards.create'); //Сценарий [controller].[action]
		
		if(isset($_POST['Ward']))
		{
			$model->attributes=Yii::app()->request->getPost('Ward');
			
			if($model->save())
			{
				Yii::app()->user->setFlash('success', 'Успешное добавление!');
				$this->redirect(['wards/view']);
			}
		}
		
		$this->render('create', [
			'model'=>$model,
			'enterpriseList'=>Enterprise::getEnterpriseListData('insert'),
			'medcardruleList'=>MedcardRule::getMedcardruleListData('insert'),
		]);
	}
	
	public function actionUpdate($id)
	{
		$record=Ward::model()->findByPk($id); // Сценарий: [controller].[action]
		$enterpriseList=Enterprise::getEnterpriseListData('insert');
		$medcardruleList=MedcardRule::getMedcardruleListData('insert');
		
		if($record===null)
		{
			throw new CHttpException(404, 'Обновляемый объект не найден!');
		}
		elseif(isset($_POST['Ward']))
		{
			$record->scenario='wards.update';
			$record->attributes=Yii::app()->request->getPost('Ward');
		
			if($record->save())
			{
				Yii::app()->user->setFlash('success', 'Успешное редактирование!');
				$this->refresh();
			}
		}

		$this->render('update', [
			'record'=>$record,
			'enterpriseList'=>$enterpriseList,
			'medcardruleList'=>$medcardruleList,
		]);
	}
	
    public function actionView() 
	{
		return $this->actionList();
//        try {
//            // Модель формы для добавления и редактирования записи
//            $formAddEdit = new FormWardAdd;
//
//            // Список учреждений
//            $connection = Yii::app()->db;
//            $enterprisesListDb = $connection->createCommand()
//                ->select('ep.*')
//                ->from('mis.enterprise_params ep')
//                ->order('ep.fullname asc')
//                ->queryAll();
//
//            $enterprisesList = array();
//            foreach($enterprisesListDb as $value) {
//                $enterprisesList[(string)$value['id']] = $value['fullname'];
//            }
//			
//			// Список правил
//			$rulesList = array();
//            foreach(MedcardRule::model()->findAll() as $value) {
//                $rulesList[(string)$value['id']] = $value['name'];
//            }
//
//            $this->render('view', array(
//                'model' => $formAddEdit,
//                'typesList' => $enterprisesList,
//				'rulesList' => $rulesList
//            ));
//        } catch(Exception $e) {
//            echo $e->getMessage();
//        }
    }

    public function actionEdit() {
        $model = new FormWardAdd();
        if(isset($_POST['FormWardAdd'])) {
            $model->attributes = $_POST['FormWardAdd'];
            if($model->validate()) {
                $ward = Ward::model()->findByPk($model->id);

                $this->addEditModel($ward, $model, 'Новое отделение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }
	
	public function actionIssetDoctorPerWard($id) {
		$issetDoctors = Doctor::model()->findAll('ward_code = :ward_id', array(':ward_id' => $id));
		echo CJSON::encode(array(
			'success' => true,
			'doctors' => $issetDoctors
		));
	}

    public function actionDelete($id) 
	{
		$record=Ward::model()->findByPk($id);
		
		$criteria=new CDbCriteria;
		$criteria->condition='ward_code=:ward_code';
		$criteria->params=[':ward_code'=>$id];
		$recordDoctor=Doctor::model()->find($criteria);
		
		if($record===null)
		{
			throw new CHttpException(404, 'Удаляемый объект не найден');
		}
		elseif($recordDoctor!=null)
		{
			Yii::app()->user->setFlash('error', 'У данного отделения присутствуют врачи!');
			$this->redirect(['wards/view']);
		}
		elseif(Ward::model()->deleteByPk($id))
		{
			Yii::app()->user->setFlash('success', 'Успешное удаление!');
			$this->redirect(['wards/view']);
		}
//        try {
//            $ward = Ward::model()->findByPk($id);
//            $ward->delete();
//            echo CJSON::encode(array('success' => 'true',
//                                     'text' => 'Отделение успешно удалено.'));
//        } catch(Exception $e) {
//            // Это нарушение целостности FK
//            echo CJSON::encode(array('success' => 'false',
//                'error' => 'На данную запись есть ссылки!'));
//        }
    }

    public function actionAdd() {
        $model = new FormWardAdd();
        if(isset($_POST['FormWardAdd'])) {
            $model->attributes = $_POST['FormWardAdd'];
            if($model->validate()) {
                $ward = new Ward();
                $this->addEditModel($ward, $model, 'Новое отделение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    private function addEditModel($ward, $model, $msg) {
        $ward->enterprise_id = $model->enterprise;
        $ward->name = $model->name;
		$ward->rule_id = $model->ruleId;


        if($ward->save()) {
            echo CJSON::encode(array('success' => true,
                                     'text' => $msg));
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

            $model = new Ward();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $wards = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $wards,
                      'total' => $totalPages,
                      'records' => count($num),
                      'success' => true
                )
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new Ward();
        $ward = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $ward)
        );
    }

    public function actionGetByEnterprise($id) {
        $model = new Ward();
        $wards = $model->getByEnterprise($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $wards)
        );
    }
}

?>