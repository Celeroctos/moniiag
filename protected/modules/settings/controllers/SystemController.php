<?php
class SystemController extends Controller {
    public $formModel;
    public function actionView() {
        $this->formModel = new FormSystemEdit();
        $this->fillSystemModel();
        $this->render('view', array(
            'model' => $this->formModel
        ));
    }

    public function actionSettingsEdit() {
        $this->formModel = new FormSystemEdit();
        if(isset($_POST['FormSystemEdit'])) {
            $this->formModel->attributes = $_POST['FormSystemEdit'];
            if($this->formModel->validate()) {
                foreach($this->formModel->attributes as $key => $settingForm) {
                    $setting = Setting::model()->find('module_id = -1 AND name = :name', array(':name' => $key));
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

    private function fillSystemModel() {
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
                    'data' => -1 // Модуль расписания
                )
            )
        );
        $settingModel = new Setting();
        $settings = $settingModel->getRows($filters);
        return $settings;
    }
}
?>