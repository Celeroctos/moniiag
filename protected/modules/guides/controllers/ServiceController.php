<?php
class ServiceController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';

    public function actionView() {
        $this->render('serviceView', array(
            'model' => new FormServiceAdd()
        ));
    }

    public function actionEdit() {
        $model = new FormServiceAdd();
        if(isset($_POST['FormServiceAdd'])) {
            $model->attributes = $_POST['FormServiceAdd'];
            if($model->validate()) {
                $service = MedService::model()->find('id=:id', array(':id' => $_POST['FormServiceAdd']['id']));
                $this->addEditModel($service, $model, 'Услуга успешно отредактирована.');
            } else {
                echo CJSON::encode(array(
                    'success' => 'false',
                    'errors' => $model->errors
                ));
            }
        }
    }

    public function actionDelete($id) {
        try {
            $service = MedService::model()->findByPk($id);
            $service->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Льгота успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array(
                'success' => 'false',
                'error' => 'На данную запись есть ссылки!'
            ));
        }
    }

    private function addEditModel($service, $model, $msg) {
        MedService::model()->updateAll(array(
			'is_default' => 0
			), 
			'is_default = 1', 
			array()
		);
		$service->tasu_code = $model->code;
        $service->name = $model->name;
		$service->is_default = $model->isDefault;

        if($service->save()) {
            echo CJSON::encode(array(
                'success' => true,
                'text' => $msg));
        }
    }

    public function actionAdd() {
        $model = new FormServiceAdd();
        if(isset($_POST['FormServiceAdd'])) {
            $model->attributes = $_POST['FormServiceAdd'];
            if($model->validate()) {
                $service = new MedService();
                $this->addEditModel($service, $model, 'Льготы успешно добавлена.');
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

            $model = new MedService();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $services = $model->getRows($filters, $sidx, $sord, $start, $rows);
			foreach($services as &$service) {
				if($service['is_default'] == null) {
					$service['is_default_desc'] = 'Нет';
					$service['is_default'] = 0;
				} else {
					$service['is_default_desc'] = 'Да';
				}
				$service['is_default'] = '';
			}

            echo CJSON::encode(
                array('rows' => $services,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetone($id) {
        $model = new MedService();
        $service = $model->getOne($id);
		if($service['is_default'] == null) { // На всякий случай, для клиентского интерфейса
			$service['is_default'] = 0;
		}
        echo CJSON::encode(array('success' => true,
                                 'data' => $service)
        );
    }

    public function actionSyncWithTasu() {
        if(!isset($_GET['rowsPerQuery'], $_GET['totalMaked'], $_GET['totalRows'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Недостаточно информации о считывании данных!'
                    ))
            );
            exit();
        }

        $versionEnd = '9223372036854775807';
        $services = TasuService::model()->getRows(false, $versionEnd, 'legacycode_35394', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);

        $processed = 0;
        $numErrors = 0;
        $numAdded = 0;

        $log = array();

        if($_GET['totalRows'] == null) {
            $allRows = TasuService::model()->findAll("[t].version_end = :version", array(':version' => $versionEnd));
            $totalRows = count($allRows);
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('medservices');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'medservices';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $totalRows = $_GET['totalRows'];
        }

        foreach($services as $service) {
            $processed++;
            $issetService = MedService::model()->find('tasu_code = :tasu_code', array(':tasu_code' => $service['legacycode_35394']));
            if($issetService != null) {
                continue;
            }
            // Добавляем услугу, если её нет
            try {
                $newService = new MedService();
                $newService->name = $service['name_42649'];
                $newService->tasu_code = $service['legacycode_35394'];
                if(!$newService->save()) {
                    $log[] = 'Невозможно импортировать услугу с кодом '.$service['legacycode_35394'];
                    $numErrors++;
                } else {
                    $numAdded++;
                }
            } catch(Exception $e) {
                $numErrors++;
            }
        }

        echo CJSON::encode(array(
                'success' => true,
                'data' => array(
                    'log' => $log,
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $processed).' услуг.',
                    'processed' => $processed,
                    'totalRows' => $totalRows,
                    'numErrors' => $numErrors,
                    'numAdded' => $numAdded
                ))
        );
    }
}

?>