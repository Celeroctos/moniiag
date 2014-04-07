<?php
class ModulesController extends Controller {
    public $layout = 'index';
    public $formModel = null;

    public function actionInfoView() {
        $this->render('indexView', array(
        ));
    }

    // Вьюха для редактирования настроек раписания
    public function actionSheduleSettings() {
        $this->formModel = new FormSheduleSettings();
        $this->fillSheduleModel();
        $this->render('shedule', array(
            'model' => $this->formModel,
            'shiftModel' => new FormShiftAdd()
        ));
    }

    public function actionSheduleSettingsEdit() {
        $this->formModel = new FormSheduleSettings();
        if(isset($_POST['FormSheduleSettings'])) {
            $this->formModel->attributes = $_POST['FormSheduleSettings'];
            if($this->formModel->validate()) {
                foreach($this->formModel->attributes as $key => $settingForm) {
                    $setting = Setting::model()->find('module_id = 1 AND name = :name', array(':name' => $key));
                    if($setting != null) {
                        $setting->value = $settingForm;
                        if(!$setting->save()) {
                            echo CJSON::encode(array('success' => 'false',
                                                     'errors' => $setting->errors));
                            exit();
                        }
                    }
                }
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Настройки успешно изменены.'));
                exit();
            }
        }

        echo CJSON::encode(array('success' => 'false',
                                 'errors' => $this->formModel->errors));
    }

    private function fillSheduleModel() {
        $settings = $this->getSettings();
        foreach($settings as $setting) {
            if($setting['name'] != null) {
                $this->formModel->{$setting['name']} = $setting['value'];
            } else {
                $this->formModel->{$setting['name']} = '';
            }
        }
    }

    private function getSettings() {
        $filters = array(
            'groupOp' => 'AND',
            'rules' => array(
                array(
                    'field' => 'module_id',
                    'op' => 'eq',
                    'data' => 1 // Модуль расписания
                )
            )
        );
        $settingModel = new Setting();
        $settings = $settingModel->getRows($filters);
        return $settings;
    }

    // Получение смен врачей
    public function actionGetShifts() {
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

            $model = new Shift();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $shifts = $model->getRows($filters, $sidx, $sord, $start, $rows);
            // Теперь получим настройку модуля для организации смен
            $shiftType = Setting::model()->find('module_id = 1 AND name = \'shiftType\'');
            foreach($shifts as &$shift) {
                $shift['shiftType'] = $shiftType->value;
                $parts = explode(':', $shift['time_begin']);
                if(count($parts) == 3) { // Это значит, что есть часы, минуты и секунды
                    $shift['time_begin'] = $parts[0].':'.$parts[1];
                }
                $parts = explode(':', $shift['time_end']);
                if(count($parts) == 3) { // Это значит, что есть часы, минуты и секунды
                    $shift['time_end'] = $parts[0].':'.$parts[1];
                }
            }

            echo CJSON::encode(
                array('rows' => $shifts,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

	

    // Добавление смены
    public function actionAddShift() {
        $model = new FormShiftAdd();
        if(isset($_POST['FormShiftAdd'])) {
            $model->attributes = $_POST['FormShiftAdd'];
            if($model->validate()) {
                $shift = new Shift();
                $this->addEditModelShift($shift, $model);

                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Новая запись успешно добавлена!'));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Редактирование смены
    public function actionEditShift() {
        $model = new FormShiftAdd();
        if(isset($_POST['FormShiftAdd'])) {
            $model->attributes = $_POST['FormShiftAdd'];
            if($model->validate()) {
                $shift = Shift::model()->findByPk($model->id);
                $this->addEditModelShift($shift, $model);
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Запись успешно отредактирована.'));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Удаление смены
    public function actionDeleteShift($id) {
        try {
            $shift = Shift::model()->findByPk($id);
            $shift->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Смена успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionGetoneShift($id) {
        $model = new Shift();
        $shift = $model->getOne($id);
        $parts = explode(':', $shift['time_begin']);
        if(count($parts) == 3) { // Это значит, что есть часы, минуты и секунды
            $shift['time_begin'] = $parts[0].':'.$parts[1];
        }
        $parts = explode(':', $shift['time_end']);
        if(count($parts) == 3) { // Это значит, что есть часы, минуты и секунды
            $shift['time_end'] = $parts[0].':'.$parts[1];
        }
        echo CJSON::encode(array('success' => 'true',
                                 'data' => $shift)
        );
    }

    public function addEditModelShift($shift, $model) {
        $shift->time_begin = $model->timeBegin;
        $shift->time_end = $model->timeEnd;
        
        if(!$shift->save()) {
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'Произошла ошибка записи новой смены.'));
            exit();
        }

        return true;
    }

}

?>