<?php
class CladrController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';

    public function actionViewRegions() {
        $this->render('regionView', array(
            'model' => new FormCladrRegionAdd()
        ));
    }

    public function actionViewDistricts() {
        $this->render('districtView', array(
            'model' => new FormCladrDistrictAdd()
        ));
    }

    public function actionViewSettlements() {
        $this->render('settlementView', array(
            'model' => new FormCladrSettlementAdd()
        ));
    }

    public function actionViewStreets() {
        $this->render('streetView', array(
            'model' => new FormCladrStreetAdd()
        ));
    }


    public function actionDistrictGet() {
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

            $model = new CladrDistrict();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $districts = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $districts,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionRegionGet() {
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

            $model = new CladrRegion();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $regions = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $regions,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionSettlementGet() {
        ini_set('memory_limit', '1024M');
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

            $model = new CladrSettlement();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $settlements = $model->getRows($filters, $sidx, $sord, $start, $rows);
            foreach($settlements as &$settlement) {
                $district = CladrDistrict::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $settlement['code_district']));
                if($district != null) {
                    $settlement['district'] = $district->name;
                }
            }

            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $settlements,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionStreetGet() {
        ini_set('memory_limit', '1024M');
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

            $model = new CladrStreet();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $streets = $model->getRows($filters, $sidx, $sord, $start, $rows);
            foreach($streets as &$street) {
                $district = CladrDistrict::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $street['code_district']));
                if($district != null) {
                    $street['district'] = $district->name;
                }
                $settlement = CladrSettlement::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $street['code_settlement']));
                if($settlement != null) {
                    $street['settlement'] = $settlement->name;
                }
            }

            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $streets,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionDistrictGetOne($id) {
        $model = new CladrDistrict();
        $district = $model->getOne($id);
        echo CJSON::encode(array(
                'success' => true,
                'data' => $district
            )
        );
    }

    public function actionRegionGetOne($id) {
        $model = new CladrRegion();
        $region = $model->getOne($id);
        echo CJSON::encode(array(
                'success' => true,
                'data' => $region
            )
        );
    }

    public function actionSettlementGetOne($id) {
        $model = new CladrRegion();
        $settlement = $model->getOne($id);
        echo CJSON::encode(array(
                'success' => true,
                'data' => $settlement
            )
        );
    }

    public function actionSettlementEdit() {
        $model = new FormCladrSettlementAdd();
        if(isset($_POST['FormCladrSettlementAdd'])) {
            $model->attributes = $_POST['FormCladrSettlementAdd'];
            if($model->validate()) {
                $district = CladrSettlement::model()->findByPk($_POST['FormCladrDistrictAdd']['id']);
                $this->addEditSettlementModel($district, $model, 'Населённый пункт успешно отредактирован.');
            } else {
                echo CJSON::encode(array(
                    'success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionDistrictEdit() {
        $model = new FormCladrDistrictAdd();
        if(isset($_POST['FormCladrDistrictAdd'])) {
            $model->attributes = $_POST['FormCladrDistrictAdd'];
            if($model->validate()) {
                $district = CladrDistrict::model()->find('id=:id', $_POST['FormCladrDistrictAdd']['id']);
                $this->addEditDistrictModel($district, $model, 'Район успешно отредактирован.');
            } else {
                echo CJSON::encode(array(
                    'success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionRegionEdit() {
        $model = new FormCladrRegionAdd();
        if(isset($_POST['FormCladrRegionAdd'])) {
            $model->attributes = $_POST['FormCladrRegionAdd'];
            if($model->validate()) {
                $region = CladrRegion::model()->findByPk($_POST['FormCladrRegionAdd']['id']);
                $this->addEditRegionModel($region, $model, 'Регион успешно отредактирован.');
            } else {
                echo CJSON::encode(array(
                    'success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionSettlementAdd() {
        $model = new FormCladrSettlementAdd();
        if(isset($_POST['FormCladrSettlementAdd'])) {
            $model->attributes = $_POST['FormCladrSettlementAdd'];
            if($model->validate()) {
                $settlement = new CladrSettlement();
                $this->addEditDistrictModel($settlement, $model, 'Новый населённый пункт успешно добавлен.');
            } else {
                echo CJSON::encode(array(
                    'success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionDistrictAdd() {
        $model = new FormCladrDistrictAdd();
        if(isset($_POST['FormCladrDistrictAdd'])) {
            $model->attributes = $_POST['FormCladrDistrictAdd'];
            if($model->validate()) {
                $district = new CladrDistrict();
                $this->addEditDistrictModel($district, $model, 'Новый район успешно добавлен.');
            } else {
                echo CJSON::encode(array(
                    'success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionRegionAdd() {
        $model = new FormCladrRegionAdd();
        if(isset($_POST['FormCladrRegionAdd'])) {
            $model->attributes = $_POST['FormCladrRegionAdd'];
            if($model->validate()) {
                $region = new CladrRegion();
                $this->addEditRegionModel($region, $model, 'Новый регион успешно добавлен.');
            } else {
                echo CJSON::encode(array(
                    'success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionSettlementDelete($id) {
        try {
            $settlement = CladrSetllement::model()->findByPk($id);
            $settlement->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Населённый пункт успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array(
                'success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionDistrictDelete($id) {
        try {
            $district = CladrDistrict::model()->findByPk($id);
            $district->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Район успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionRegionDelete($id) {
        try {
            $region = CladrRegion::model()->findByPk($id);
            $region->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Регион успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    private function addEditSettlementModel($settlement, $model, $msg) {
        $settlement->code_region = $model->codeRegion;
        $settlement->code_district = $model->codeDistrict;
        $settlement->code_cladr = $model->codeCladr;
        $settlement->name = $model->name;

        if($settlement->save()) {
            echo CJSON::encode(array(
                'success' => true,
                'text' => $msg
            ));
        }
    }

    private function addEditDistrictModel($district, $model, $msg) {
        $district->code_region = $model->codeRegion;
        $district->code_cladr = $model->codeCladr;
        $district->name = $model->name;

        if($district->save()) {
            echo CJSON::encode(array(
                'success' => true,
                'text' => $msg
            ));
        }
    }

    private function addEditRegionModel($region, $model, $msg) {
        $region->code_cladr = $model->codeCladr;
        $region->name = $model->name;

        if($region->save()) {
            echo CJSON::encode(array(
                'success' => true,
                'text' => $msg
            ));
        }
    }

    /* Синхронизация стран */
    public function actionSyncLands() {

    }

    /* Синхронизация регионов */
    public function actionSyncRegions() {
        if(!isset($_GET['rowsPerQuery'], $_GET['totalMaked'], $_GET['totalRows'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Недостаточно информации о считывании данных!'
                    ))
            );
            exit();
        }

        $processed = 0;
        $numErrors = 0;
        $numAdded = 0;

        $log = array();

        $regions = TasuCladrRegion::model()->getRows(false, 'codekladr_63612', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);

        if($_GET['totalRows'] == null) {
            $allRows = TasuCladrRegion::model()->findAll();
            $totalRows = count($allRows);
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('cladrRegions');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'cladrRegions';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $totalRows = $_GET['totalRows'];
        }

        foreach($regions as $region) {
            $processed++;
            $issetCode = CladrRegion::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $region['codekladr_63612']));
            if($issetCode != null) {
                continue;
            }
            // Добавляем услугу, если её нет
            try {
                $newRegion = new CladrRegion();
                $newRegion->name = $region['socr_58657'].' '.$region['name_36125'];
                $newRegion->code_cladr = $region['codekladr_63612'];
                if(!$newRegion->save()) {
                    $log[] = 'Невозможно импортировать регион с кодом '.$region['codekladr_63612'];
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
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $processed).' регионов.',
                    'processed' => $processed,
                    'totalRows' => $totalRows,
                    'numErrors' => $numErrors,
                    'numAdded' => $numAdded
                ))
        );
    }

    /* Синхронизация районов */
    public function actionSyncDistricts() {
        if(!isset($_GET['rowsPerQuery'], $_GET['totalMaked'], $_GET['totalRows'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Недостаточно информации о считывании данных!'
                    ))
            );
            exit();
        }

        $processed = 0;
        $numErrors = 0;
        $numAdded = 0;

        $log = array();

        $districts = TasuCladrDistrict::model()->getRows(false, 'codekladr_06188', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);

        if($_GET['totalRows'] == null) {
            $allRows = TasuCladrDistrict::model()->findAll();
            $totalRows = count($allRows);
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('cladrDistricts');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'cladrDistricts';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $totalRows = $_GET['totalRows'];
        }

        foreach($districts as $district) {
            $processed++;
            $issetCode = CladrDistrict::model()->find('code_cladr = :code_cladr AND code_region = :code_region', array(':code_cladr' => $district['codekladr_06188'], ':code_region' => $district['coderegion_58415']));
            if($issetCode != null) {
                continue;
            }
            // Добавляем услугу, если её нет
            try {
                $newDistrict = new CladrDistrict();
                $newDistrict->name = $district['name_51305'];
                $newDistrict->code_cladr = $district['codekladr_06188'];
                $newDistrict->code_region = $district['coderegion_58415'];
                if(!$newDistrict->save()) {
                    $log[] = 'Невозможно импортировать район с кодом '.$district['codekladr_06188'];
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
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $processed).' районов.',
                    'processed' => $processed,
                    'totalRows' => $totalRows,
                    'numErrors' => $numErrors,
                    'numAdded' => $numAdded
                ))
        );
    }

    /* Синхронизация населённых пунктов */
    public function actionSyncSettlements() {
        ini_set('memory_limit', '1024M');
        if(!isset($_GET['rowsPerQuery'], $_GET['totalMaked'], $_GET['totalRows'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Недостаточно информации о считывании данных!'
                    ))
            );
            exit();
        }

        $processed = 0;
        $numErrors = 0;
        $numAdded = 0;

        $log = array();

        $settlements = TasuCladrSettlement::model()->getRows(false, 'codesettlement_56846', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);

        if($_GET['totalRows'] == null) {
            $allRows = TasuCladrSettlement::model()->findAll();
            $totalRows = count($allRows);
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('cladrSettlements');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'cladrSettlements';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $totalRows = $_GET['totalRows'];
        }

        foreach($settlements as $settlement) {
            $processed++;
            $issetCode = CladrSettlement::model()->find('code_cladr = :code_cladr
                                                        AND code_region = :code_region
                                                        AND code_district = :code_district',
                array(':code_cladr' => $settlement['codesettlement_56846'],
                      ':code_region' => $settlement['coderegion_15248'],
                      ':code_district' => $settlement['codedistrict_55105']
                )
            );
            if($issetCode != null) {
                continue;
            }
            // Добавляем услугу, если её нет
            try {
                $newSettlement = new CladrSettlement();
                $newSettlement->name = $settlement['socr_51233'].' '.$settlement['name_41914'];
                $newSettlement->code_cladr = $settlement['codesettlement_56846'];
                $newSettlement->code_region = $settlement['coderegion_15248'];
                $newSettlement->code_district = $settlement['codedistrict_55105'];
                if(!$newSettlement->save()) {
                    $log[] = 'Невозможно импортировать населённый пункт с кодом '.$settlement['codesettlement_56846'];
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
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $processed).' районов.',
                    'processed' => $processed,
                    'totalRows' => $totalRows,
                    'numErrors' => $numErrors,
                    'numAdded' => $numAdded
                ))
        );
    }

    /* Синхронизация улиц */
    public function actionSyncStreets() {
        ini_set('memory_limit', '1024M');
        if(!isset($_GET['rowsPerQuery'], $_GET['totalMaked'], $_GET['totalRows'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Недостаточно информации о считывании данных!'
                    ))
            );
            exit();
        }

        $processed = 0;
        $numErrors = 0;
        $numAdded = 0;

        $log = array();

        $streets = TasuCladrStreet::model()->getRows(false, 'codestreet_52685', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);

        if($_GET['totalRows'] == null) {
            $totalRows = TasuCladrStreet::model()->getNumRows();
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('cladrStreets');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'cladrStreets';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $totalRows = $_GET['totalRows'];
        }

        foreach($streets as $street) {
            $processed++;
            $issetCode = CladrStreet::model()->find('code_cladr = :code_cladr
                                                        AND code_region = :code_region
                                                        AND code_district = :code_district
                                                        AND code_settlement = :code_settlement',
                array(
                    ':code_cladr' => $street['codestreet_52685'],
                    ':code_region' => $street['coderegion_17901'],
                    ':code_district' => $street['codedistrict_58020'],
                    ':code_settlement' => $street['codesettlement_38285']
                )
            );
            if($issetCode != null) {
                continue;
            }
            // Добавляем улицу, если её нет
            try {
                $newStreet = new CladrStreet();
                $newStreet->name = $street['socr_31302'].' '.$street['name_53962'];
                $newStreet->code_cladr = $street['codestreet_52685'];
                $newStreet->code_region = $street['coderegion_17901'];
                $newStreet->code_district = $street['codedistrict_58020'];
                $newStreet->code_settlement = $street['codesettlement_38285'];
                if(!$newStreet->save()) {
                    $log[] = 'Невозможно импортировать улицу с кодом '.$street['codestreet_52685'];
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
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $processed).' улиц.',
                    'processed' => $processed,
                    'totalRows' => $totalRows,
                    'numErrors' => $numErrors,
                    'numAdded' => $numAdded
                ))
        );
    }
}