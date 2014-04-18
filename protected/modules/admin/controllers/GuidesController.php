<?php
class GuidesController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
        $this->render('guidesView', array(
            'model' => new FormGuideAdd()
        ));
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

            $model = new MedcardGuide();
            $num = $model->getRows($filters);

            $totalPages = ceil(count($num) / $rows);
            $start = $page * $rows - $rows;

            $guides = $model->getRows($filters, $sidx, $sord, $start, $rows);

            echo CJSON::encode(
                array('rows' => $guides,
                    'total' => $totalPages,
                    'records' => count($num))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionEdit() {
        $model = new FormGuideAdd();
        if(isset($_POST['FormGuideAdd'])) {
            $model->attributes = $_POST['FormGuideAdd'];
            if($model->validate()) {
                $guide = MedcardGuide::model()->find('id=:id', array(':id' => $_POST['FormGuideAdd']['id']));
                $this->addEditModel($guide, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }
    }

    public function actionAdd() {
        $model = new FormGuideAdd();
        if(isset($_POST['FormGuideAdd'])) {
            $model->attributes = $_POST['FormGuideAdd'];
            if($model->validate()) {
                $guide = new MedcardGuide();
                $this->addEditModel($guide, $model, 'Категория успешно добавлена.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                    'errors' => $model->errors));
            }
        }

    }

    private function addEditModel($guide, $model, $msg) {
        $guide->name = $model->name;
        if($guide->save()) {
            echo CJSON::encode(array('success' => true,
                'text' => $msg));
        }
    }

    public function actionDelete($id) {
        try {
            $guide = MedcardGuide::model()->findByPk($id);
            $guide->delete();
            echo CJSON::encode(array('success' => 'true',
                'text' => 'Категория успешно удалена.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionGetone($id) {
        $model = new MedcardGuide();
        $guide = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $guide)
        );
    }

    // Экшн просмотра всех справочников
    public function actionAllView() {
        $this->layout = 'application.modules.admin.views.layouts.medguides';
        $medguidesTabWidget = CWidget::createWidget('application.modules.admin.components.widgets.MedguidesTabMenu');
        $currentGuide = $medguidesTabWidget->getCurrentGuide($medguidesTabWidget->getGuidesList());
        $this->render('medguidesView', array(
            'model' => new FormValueAdd(),
            'currentGuideId' => $currentGuide
        ));
    }

    // Экшн просмотра значений определённого справочника
    public function actionGetValues($id = false) {
        try {
            // Если вынимаем у непонятно чего - давать поворот-отворот
            if($id == false) {
                echo CJSON::encode(array('success' => 'false',
                                         'error' => 'Без указания справочника.'));
                exit();
            }
            $rows = (isset($_GET['rows'])) ? $_GET['rows'] : false;
            $page = (isset($_GET['page'])) ? $_GET['page'] : false;
            $sidx = (isset($_GET['sidx'])) ? $_GET['sidx'] : 'value';
            $sord = (isset($_GET['sord'])) ? $_GET['sord'] : 'asc';

            // Фильтры поиска
            if(isset($_GET['filters']) && trim($_GET['filters']) != '') {
                $filters = CJSON::decode($_GET['filters']);
            } else {
                $filters = false;
            }

            $model = new MedcardGuideValue();
            $num = $model->getRows($filters, $id);

            if($rows != false && $page != false && $sidx != false && $sord != false) {
                $totalPages = ceil(count($num) / $rows);
                $start = $page * $rows - $rows;
            } else {
                $start = false;
                $totalPages = 1;
            }

            $values = $model->getRows($filters, $id, $sidx, $sord, $start, $rows);
            unset($values['-3']);

            echo CJSON::encode(
                array('rows' => $values,
                      'total' => $totalPages,
                      'records' => count($num),
                      'success' => true)
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }

    // Добавить значение в справочник
    public function actionAddInGuide($id = false) {
        // Если вынимаем у непонятно чего - давать поворот-отворот
        if($id == false) {
            if(isset($_POST['FormValueAdd']['controlId'])) {
                $control = MedcardElement::model()->findByPk($_POST['FormValueAdd']['controlId']);
                if($control != null) {
                    $id = $control->guide_id;
                }
            }
        }

        if($id == false) {
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'Без указания справочника.'));
            exit();
        }
        $model = new FormValueAdd();
        if(isset($_POST['FormValueAdd'])) {
            $model->attributes = $_POST['FormValueAdd'];
            if($model->validate()) {
                $guideValue = new MedcardGuideValue();
                $guideValue->guide_id = $id;
                $this->addEditValueModel($guideValue, $model, 'Значение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    // Редактировать значение в справочнике
    public function actionEditInGuide() {
        $model = new FormValueAdd();
        if(isset($_POST['FormValueAdd'])) {
            $model->attributes = $_POST['FormValueAdd'];
            if($model->validate()) {
                $guideValue = MedcardGuideValue::model()->find('id=:id', array(':id' => $_POST['FormValueAdd']['id']));
                $this->addEditValueModel($guideValue, $model, 'Значение успешно добавлено.');
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    private function addEditValueModel($guideValue, $model, $msg) {
        $guideValue->value = $model->value;
        if($guideValue->save()) {
            echo CJSON::encode(array('success' => 'true',
                                     'text' => $msg,
                                     'id' => $guideValue->id,
                                     'display' => $guideValue->value));
        }
    }

    public function actionGetoneValue($id) {
        $model = new MedcardGuideValue();
        $value = $model->getOne($id);
        echo CJSON::encode(array('success' => true,
                                 'data' => $value)
        );
    }

    public function actionDeleteInGuide($id) {
        try {
            $guideValue = MedcardGuideValue::model()->findByPk($id);
            $guideValue->delete();
            echo CJSON::encode(array('success' => 'true',
                                     'text' => 'Значение справочника успешно удалено.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => 'false',
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }
}

?>