<?php
class InsurancesController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';
    public $defaultAction = 'view';

    public function actionView() {
        $formAddEdit = new FormInsuranceAdd();
        $this->render('view', array(
            'model' => $formAddEdit
        ));
    }


    public function actionDelete($id) {
        try {
            $insurance = Insurance::model()->findByPk($id);
            $insurance->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Страховая компания успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditModel($insurance, $model, $msg) {
        // Раскодируем id регионов
        $regionsIds = CJSON::decode($model->regionsHidden);
        $insurance->name = $model->name;
		$insurance->code = $model->code;
        if($insurance->save()) {

            // Записываем id регионов.
            // Сначала убиваем всё для данной страховой компании
            InsuranceRegion::model()->deleteAll('insurance_id=:ins_id', array(':ins_id' => $insurance->id));

            // Перебираем массив regionsIds
            if ($regionsIds!=null)
            {
                foreach($regionsIds as $oneRegionId)
                {
                    //Создаём новый об'ект insuranceRegion, наполняем его данными и сохраняем в базу

                    $newLinkObject = new InsuranceRegion();
                    $newLinkObject->insurance_id = $insurance->id;
                    $newLinkObject->region_id = $oneRegionId;
                    // А теперь надо сохранить регион
                    $newLinkObject->save();
                }
            }

            echo CJSON::encode(array('success' => true,
                    'text' =>  $msg
                )
            );
        }
    }

    public function actionEdit() {
        $model = new FormInsuranceAdd();
        if(isset($_POST['FormInsuranceAdd'])) {
            $model->attributes = $_POST['FormInsuranceAdd'];
            if($model->validate()) {
                $insurance = Insurance::model()->find('id=:id', array(':id' => $_POST['FormInsuranceAdd']['id']));
                $this->addEditModel($insurance, $model, 'Страховая компания успешно отредактирована.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionAdd() {
        $model = new FormInsuranceAdd();
        if(isset($_POST['FormInsuranceAdd'])) {
            $model->attributes = $_POST['FormInsuranceAdd'];
            if($model->validate()) {
                $insurance = new Insurance();
                $this->addEditModel($insurance, $model, 'Страховая компания успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionGetOne($id) {
        // Надо добавить чтение регионов компании
        $model = new Insurance();
        $insurance = $model->getOne($id);
        // По id ищем список регионов

        $regions = InsuranceRegion::findRegions($id);
        $insurance['regions'] = $regions;
        echo CJSON::encode(array('success' => true,
                'data' => $insurance )
        );
    }

    public function actionGet() {
        //var_dump($_GET);
        //exit();
        try {



            $rows = $_GET['rows'];
            $page = $_GET['page'];
            $sidx = $_GET['sidx'];
            $sord = $_GET['sord'];


            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $regionId = false;

            // Если флаг is_chooser - то снимаем ИД региона
            if (isset($_GET['is_chooser']))
            {
                if (isset($_GET['region_insurance']))
                {
                    $regionId = $_GET['region_insurance'];
                }
                else
                {
                    $regionId = -1;
                }
            }



            $model = new Insurance();
            $num = $model->getRows($filters,false,false,false,false,$regionId);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $insurances = $model->getRows($filters, $sidx, $sord, $start, $rows,$regionId);
            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $insurances,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

}
?>