<?php
class CladrController extends Controller {
    public $layout = 'application.modules.guides.views.layouts.index';

    public function actionGetCladrData($data = false) {
        if(!isset($_GET['data']) && !$data) {
            echo CJSON::encode(
                array(
                    'success' => false,
                    'error' => 'Не хватает данных для запроса!'
                )
            );
        }

        $data = isset($_GET['data']) ? CJSON::decode($_GET['data']) : $data;
        $answer = array();

        if(isset($data['regionId'])) {
            $answer['region'] = CladrRegion::model()->findAll('id = :id', array(':id' => $data['regionId']));
        } else {
            $answer['region'] = null;
        }
        if(isset($data['districtId'])) {
            $answer['district'] = CladrDistrict::model()->findAll('id = :id', array(':id' => $data['districtId']));
        } else {
            $answer['district'] = null;
        }
        if(isset($data['settlementId'])) {
            $answer['settlement'] = CladrSettlement::model()->findAll('id = :id', array(':id' => $data['settlementId']));
        } else {
            $answer['settlement'] = null;
        }
        if(isset($data['streetId'])) {
            $answer['street'] = CladrStreet::model()->findAll('id = :id', array(':id' => $data['streetId']));
        } else {
            $answer['street'] = null;
        }
        if(!isset($data['house'])) {
            $answer['house'] = '';
        } else {
            $answer['house'] = $data['house'];
        }
        if(!isset($data['building'])) {
            $answer['building'] = '';
        } else {
            $answer['building'] = $data['building'];
        }
        if(!isset($data['flat'])) {
            $answer['flat'] = '';
        } else {
            $answer['flat'] = $data['flat'];
        }
        if(!isset($data['postindex'])) {
            $answer['postindex'] = '';
        } else {
            $answer['postindex'] = $data['postindex'];
        }

        if(Yii::app()->request->isAjaxRequest && (!isset($data['returnData']) || $data['returnData'] == 0)) {
            echo CJSON::encode(
                array(
                    'success' => true,
                    'data' => $answer
                )
            );
        } else {
            return $answer;
        }
    }

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
                if(isset($_GET['region'])) {
                    $filters['rules'][] = array(
                        'field' => 'code_region',
                        'op' => 'eq',
                        'data' => $_GET['region']
                    );
                }
            } else {
                $filters = false;
            }

            $model = new CladrDistrict();
            $num = $model->getRows($filters,false,false,false,false,false);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $districts = $model->getRows($filters, $sidx, $sord, $start, $rows,false);

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
                if(isset($_GET['region'])) {
                    $filters['rules'][] = array(
                        'field' => 'code_region',
                        'op' => 'eq',
                        'data' => $_GET['region']
                    );
                }
                if(isset($_GET['district'])) {
                    $filters['rules'][] = array(
                        'field' => 'code_district',
                        'op' => 'eq',
                        'data' => $_GET['district']
                    );
                }
                // А если нет - то этот элемент надо создать
                else
                {
                    $filters['rules'][] = array(
                        'field' => 'code_district',
                        'op' => 'eq',
                        'data' => '000'
                    );
                }
            } else {
                $filters = false;
            }

            $model = new CladrSettlement();
            $num = $model->getNumRows($filters,false,false,false,false,false);

            $totalPages = ceil($num / $rows);
            $start = $page * $rows - $rows;

            $settlements = $model->getRows($filters, $sidx, $sord, $start, $rows,false);
            foreach($settlements as &$settlement) {
                $district = CladrDistrict::model()->find('code_cladr = :code_cladr  AND code_region = :code_region_settlement',
                    array(
                        ':code_cladr' => $settlement['code_district'],
                        ':code_region_settlement' => $settlement['code_region']
                    ));
                if($district != null) {
                    $settlement['district'] = $district->name;
                }
            }

            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $settlements,
                    'total' => $totalPages,
                    'records' => $num)
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
                if(isset($_GET['region'])) {
                    $filters['rules'][] = array(
                        'field' => 'code_region',
                        'op' => 'eq',
                        'data' => $_GET['region']
                    );
                }
                if(isset($_GET['district'])) {
                    $filters['rules'][] = array(
                        'field' => 'code_district',
                        'op' => 'eq',
                        'data' => $_GET['district']
                    );
                }
                // А если нет - то этот элемент надо создать
                else
                {
                    $filters['rules'][] = array(
                        'field' => 'code_district',
                        'op' => 'eq',
                        'data' => '000'
                    );
                }
                if(isset($_GET['settlement'])) {
                    $filters['rules'][] = array(
                        'field' => 'code_settlement',
                        'op' => 'eq',
                        'data' => $_GET['settlement']
                    );
                }
                else
                {
                    $filters['rules'][] = array(
                        'field' => 'code_settlement',
                        'op' => 'eq',
                        'data' => '000000'
                    );
                }
            } else {
                $filters = false;
            }

            $model = new CladrStreet();
            $num = $model->getNumRows($filters);

            $totalPages = ceil($num / $rows);
            $start = $page * $rows - $rows;

            $streets = $model->getRows($filters, $sidx, $sord, $start, $rows);
            foreach($streets as &$street) {
                $district = CladrDistrict::model()->find('code_cladr = :code_cladr AND code_region = :code_region_street',
                    array(
                        ':code_cladr' => $street['code_district'],
                        ':code_region_street' => $street['code_region']
                    ));
                if($district != null) {
                    $street['district'] = $district->name;
                }
                $settlement = CladrSettlement::model()->find('code_cladr = :code_cladr AND code_region = :code_region_street AND code_district= :code_district_street',
                    array(
                        ':code_cladr' => $street['code_settlement'],
                        ':code_region_street' => $street['code_region'],
                        ':code_district_street' => $street['code_district']
                    ));
                if($settlement != null) {
                    $street['settlement'] = $settlement->name;
                }
            }

            echo CJSON::encode(
                array(
                    'success' => true,
                    'rows' => $streets,
                    'total' => $totalPages,
                    'records' => $num)
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
        $model = new CladrSettlement();
        $settlement = $model->getOne($id);
        echo CJSON::encode(array(
                'success' => true,
                'data' => $settlement
            )
        );
    }

    public function actionStreetGetOne($id) {
        $model = new CladrStreet();
        $street = $model->getOne($id);
        echo CJSON::encode(array(
                'success' => true,
                'data' => $street
            )
        );
    }

    public function actionStreetEdit() {
        $model = new FormCladrStreetAdd();
        if(isset($_POST['FormCladrStreetAdd'])) {
            $model->attributes = $_POST['FormCladrStreetAdd'];
            if($model->validate()) {
                $street = CladrStreet::model()->findByPk($_POST['FormCladrStreetAdd']['id']);
                $this->addEditStreetModel($street, $model, 'Улица успешно отредактирована.');
            } else {
                echo CJSON::encode(array(
                    'success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    /**
     * Generates a Universally Unique IDentifier, version 4.
     *
     * RFC 4122 (http://www.ietf.org/rfc/rfc4122.txt) defines a special type of Globally
     * Unique IDentifiers (GUID), as well as several methods for producing them. One
     * such method, described in section 4.4, is based on truly random or pseudo-random
     * number generators, and is therefore implementable in a language like PHP.
     *
     * We choose to produce pseudo-random numbers with the Mersenne Twister, and to always
     * limit single generated numbers to 16 bits (ie. the decimal value 65535). That is
     * because, even on 32-bit systems, PHP's RAND_MAX will often be the maximum *signed*
     * value, with only the equivalent of 31 significant bits. Producing two 16-bit random
     * numbers to make up a 32-bit one is less efficient, but guarantees that all 32 bits
     * are random.
     *
     * The algorithm for version 4 UUIDs (ie. those based on random number generators)
     * states that all 128 bits separated into the various fields (32 bits, 16 bits, 16 bits,
     * 8 bits and 8 bits, 48 bits) should be random, except : (a) the version number should
     * be the last 4 bits in the 3rd field, and (b) bits 6 and 7 of the 4th field should
     * be 01. We try to conform to that definition as efficiently as possible, generating
     * smaller values where possible, and minimizing the number of base conversions.
     *
     * @copyright  Copyright (c) CFD Labs, 2006. This function may be used freely for
     *              any purpose ; it is distributed without any form of warranty whatsoever.
     * @author      David Holmes <dholmes@cfdsoftware.net>
     *
     * @return  string  A UUID, made up of 32 hex digits and 4 hyphens.
     */

    private function uuid() {

        // The field names refer to RFC 4122 section 4.1.2

        return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
            mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
            mt_rand(0, 65535), // 16 bits for "time_mid"
            mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
            bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
            // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
            // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
            // 8 bits for "clk_seq_low"
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
        );
    }

    private function create_guid()   //Генераци GUID
    {
		return mb_convert_case($this->uuid(), MB_CASE_UPPER, "UTF-8");
    }

    public function actionSettlementEdit() {
        $model = new FormCladrSettlementAdd();
        if(isset($_POST['FormCladrSettlementAdd'])) {
            $model->attributes = $_POST['FormCladrSettlementAdd'];
            if($model->validate()) {
                $settlement = CladrSettlement::model()->findByPk($_POST['FormCladrSettlementAdd']['id']);
                $this->addEditSettlementModel($settlement, $model, 'Населённый пункт успешно отредактирован.');
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
                $district = CladrDistrict::model()->findByPk($_POST['FormCladrDistrictAdd']['id']);
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

    // Выполняет действия с моделью строки из кладдр перед её добавлением в базу
    private function onBeforeAddCladdr(&$claddrEntry)
    {
        // За такое надо расстреливать без суда и следствия, но как по-другому сделать я не знаю :(
        if ($claddrEntry->code_cladr=='undefined') {$claddrEntry->code_cladr = '';}

        if ($claddrEntry->code_cladr == '' || $claddrEntry->code_cladr == null)
        {
            // Генерируем гуид
            $guid = $this->create_guid();
            // Убираем из строки фигурный скобки и прочую гадость (дефисы)
            $guid = str_replace('{','',$guid);
            $guid = str_replace('}','',$guid);
            $guid = str_replace('-','',$guid);

            $claddrEntry->code_cladr = $guid;

            // Пишем fake_cladr ля модели
            $claddrEntry->fake_cladr = 1;
        }
    }

    public function actionStreetAdd() {
        $model = new FormCladrStreetAdd();
        if(isset($_POST['FormCladrStreetAdd'])) {
            $model->attributes = $_POST['FormCladrStreetAdd'];
            if($model->validate()) {
                $street = new CladrStreet();
                $this->addEditStreetModel($street, $model, 'Новая улица успешно добавлена.');
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
                $this->addEditSettlementModel($settlement, $model, 'Новый населённый пункт успешно добавлен.');
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
		//var_dump(Yii::app());
		//exit();
        $model = new FormCladrRegionAdd();

        if(isset($_POST['FormCladrRegionAdd'])) {
            $model->attributes = $_POST['FormCladrRegionAdd'];
			//					var_dump($model);
			//	exit();		
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

    public function actionStreetDelete($id) {
        try {
            $street = CladrStreet::model()->findByPk($id);
            $street->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Улица успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array(
                'success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionSettlementDelete($id) {
        try {
            $settlement = CladrSettlement::model()->findByPk($id);
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

    private function addEditStreetModel($street, $model, $msg) {
        $street->code_region = $model->codeRegion;
        $street->code_district = $model->codeDistrict;
        $street->code_cladr = $model->codeCladr;
        $street->code_settlement = $model->codeSettlement;
        $street->name = $model->name;

        // Если нет кода района или кода населённого пункта - вставляем нуль
        if ($street->code_district == ''
            ||$street->code_district == null
            ||$street->code_district == "undefined")
            {
                $street->code_district = '000';
            }

        if ($street->code_settlement  == ''
            ||$street->code_settlement == null
            ||$street->code_settlement == "undefined")
        {
            $street->code_settlement = '000000';
        }


        $this->onBeforeAddCladdr($street);
        if($street->save()) {
            echo CJSON::encode(array(
                'success' => true,
                'text' => $msg
            ));
        }
    }

    private function addEditSettlementModel($settlement, $model, $msg) {
        $settlement->code_region = $model->codeRegion;
        $settlement->code_district = $model->codeDistrict;
        $settlement->code_cladr = $model->codeCladr;
        $settlement->name = $model->name;
        // Если нету кода района - дописываем нули
        if ($settlement->code_district == ''
            ||$settlement->code_district == null
            ||$settlement->code_district == "undefined")
            {
                $settlement->code_district = '000';
            }
        $this->onBeforeAddCladdr($settlement);
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
        $this->onBeforeAddCladdr($district);
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
        $this->onBeforeAddCladdr($region);
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