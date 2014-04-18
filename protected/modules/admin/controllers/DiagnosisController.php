<?php
class DiagnosisController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    // Получить страницу с шаблоном "любимых" диагнозов
    public function actionAllView() {
        $this->render('index', array(

        ));
    }

    public function actionGetLikes($id) {
        $model = new LikeDiagnosis();
        $diagnosisRows = $model->getRows(false, $id); // Получить предпочтения по врачу
        echo CJSON::encode(array('success' => true,
                                 'data' => $diagnosisRows)
        );
    }

    public function actionGetLikesAndDistrib($id) {
        $model = new LikeDiagnosis();
        $diagnosisRows = $model->getRows(false, $id); // Получить предпочтения по врачу
        $modelDistrib = new DistribDiagnosis();
        $diagnosisDistribRows = $modelDistrib->getRows(false, $id); // Получить предпочтения по врачу
        echo CJSON::encode(array('success' => true,
                                 'data' => array(
                                     'likes' => $diagnosisRows,
                                    // 'distrib' => $diagnosisDistribRows
                                     'employees' => $this->getEmployeesPerSpec($id)
                                 )
                            )
        );
    }


    private function getEmployeesPerSpec($medworkerId) {
        $specEmployees = Employee::model()->getEmployeesPerSpec($medworkerId);
        return $specEmployees;
    }

    public function actionGetDistrib($employeeid) {
        $modelDistrib = new DistribDiagnosis();
        $diagnosisDistribRows = $modelDistrib->getRows(false, $employeeid); // Получить предпочтения по врачу
        echo CJSON::encode(array(
                'success' => true,
                'data' => $diagnosisDistribRows
            )
        );
    }


	public function actionGetClinical() {
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
		$modelClinical = new ClinicalDiagnosis();
		$num = $modelClinical->getRows($filters, false);

		$totalPages = ceil(count($num) / $rows);
		$start = $page * $rows - $rows;

		$diagnosisClinicalsRows = $modelClinical->getRows($filters, false, $sidx, $sord, $start, $rows);
		echo CJSON::encode(array(
			'success' => true,
			'total' => $totalPages,
			'records' => count($num),
			'rows' => $diagnosisClinicalsRows
			)
        );
	}

	public function actionDeleteClinical()
	{
		if (isset($_GET['id']))
		{
			$diag = ClinicalDiagnosis::model()->findByPk($_GET['id']);
			// Помечаем, что старый клинический диагноз удалён
			if ($diag!=NULL)
			{
				$diag->is_deleted = 1;
				$diag->save();
				// Сообщаем, что успешно удалили
				echo CJSON::encode(array('success' => true,
					'msg' => 'Диагноз успешно удалён'));
			}
		}
	}

    public function actionSetLikes() {
        if(!isset($_GET['medworker_id'], $_GET['diagnosis_ids'])) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array())
            );
            exit();
        }
        // В противном случае, устанавливаем все, которые могут быть установлены
        // Удаляем все, уже установленные
        LikeDiagnosis::model()->deleteAll('medworker_id = :medworker_id', array(':medworker_id' => $_GET['medworker_id']));
        $diagnosis = CJSON::decode($_GET['diagnosis_ids']);;
        foreach($diagnosis as $dia) {
            $like = new LikeDiagnosis();
            $like->medworker_id = $_GET['medworker_id'];
            $like->mkb10_id = $dia['id'];
            if(!$like->save()) {
                echo CJSON::encode(array('success' => false,
                                         'error' => 'Не могу сохранить любимый диагноз!')
                );
                exit();
            }
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => array())
        );
    }

    public function actionSetDistrib() {
        if(!isset($_GET['employee_id'], $_GET['diagnosis_ids'])) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array())
            );
            exit();
        }
        // В противном случае, устанавливаем все, которые могут быть установлены
        // Удаляем все, уже установленные
        DistribDiagnosis::model()->deleteAll('employee_id = :employee_id', array(':employee_id' => $_GET['employee_id']));
        $diagnosis = CJSON::decode($_GET['diagnosis_ids']);

        foreach($diagnosis as $dia) {
            $distrib = new DistribDiagnosis();
            $distrib->employee_id = $_GET['employee_id'];
            $distrib->mkb10_id = $dia;
            if(!$distrib->save()) {
                echo CJSON::encode(array('success' => false,
                                         'error' => 'Не могу сохранить диагноз!')
                );
                exit();
            }
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => array())
        );
    }

    public function actionGetone($id) {

    }

    public function actionDistribView() {
        $this->render('distrib', array(

        ));
    }

	public function actionClinicalView() {
		$this->render('clinical', array(
			'model' => new FormClinicalDiagnosisAdd()
        ));
	}

	public function actionAddClinic()
	{
		$model = new ClinicalDiagnosis();
		$model->description = $_POST['FormClinicalDiagnosisAdd']['description'];
		
		if ($model->save())
		{
			echo CJSON::encode(array('success' => true,
				'data' => array()));
		}
		else
		{
			echo CJSON::encode(array('success' => false,
				'error' => 'Очень извиняюсь, но не могу сохранить диагноз :(((')
					);
		}
	}

	public function actionEditClinic()
	{
		$model = new FormClinicalDiagnosisAdd();
		if(isset($_POST['FormClinicalDiagnosisAdd']))
		{
			$model->attributes = $_POST['FormClinicalDiagnosisAdd'];
			if($model->validate()) {
				$diag = ClinicalDiagnosis::model()->findByPk($_POST['FormClinicalDiagnosisAdd']['id']);
				// Помечаем, что старый клинический диагноз удалён
				if ($diag->description != $model->description)
				{
					$diag->is_deleted = 1;
					$diag->save();
				}
				else
				{
					// Названия диагнозов совпадают - выходим
					echo CJSON::encode(array('success' => true,
						'data' => array()));
					return;
				}
				// Создаём новый диагноз
				$newDiag = new ClinicalDiagnosis();
				$newDiag->description = $model->description;
				
				//$diag->is_deleted = $model->description;
				//$diag->description = $model->description;
				if($newDiag->save()) {
					echo CJSON::encode(array('success' => true,
						'data' => array()));
				}
				else
				{
					echo CJSON::encode(array('success' => false,
						'error' => 'Очень извиняюсь, но не могу сохранить диагноз :(((')
							);
				}
			} else {
				echo CJSON::encode(array('success' => 'false',
					'errors' => $model->errors));
			}
		}
	}

	public function actionGetOneClinical() {
		
		$model = new ClinicalDiagnosis();
		$id = $_GET['id'];
		//var_dump($id );
		//exit();
		//echo('<pre>');
		//var_dump($model);
		//exit();
		$value = $model->getOne($id );
		
		echo CJSON::encode(array('success' => true,
			'data' => $value)
				);
	}

    public function actionMkb10View() {
        $this->render('mkb10', array());
    }
}