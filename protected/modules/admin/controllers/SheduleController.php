<?php
class SheduleController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';

    public function actionView() {
        $ward = new Ward();
        $wardsResult = $ward->getRows(false);
        $wardsList = array('-1' => 'Нет');
        foreach($wardsResult as $key => $value) {
            $wardsList[$value['id']] = $value['name'];
        }

        // Список должностей
        $post = new Post();
        $postsResult = $post->getRows(false);
        $postsList = array('-1' => 'Нет');
        foreach($postsResult as $key => $value) {
            $postsList[$value['id']] = $value['name'];
        }

        // Модель формы для модицикации расписания
        $formModel = new FormSheduleAdd();

        // Список кабинетов
        $cabinet = new Cabinet();
        $cabinetResult = $cabinet->getRows(false);
        $cabinetList = array();
        foreach($cabinetResult as $key => $value) {
            $cabinetList[$value['id']] = $value['cab_number'].' - '.$value['description'].', '.$value['ward'].' отделение, '.$value['enterprise'];
        }

        $daysExp = $this->getExpDays(true);

        $this->render('index', array(
            'wardsList' => $wardsList,
            'postsList' => $postsList,
            'model' => $formModel,
            'daysExp' => $daysExp,
            'cabinetList' => $cabinetList
        ));
    }

    // Дни-исключения
    public function getExpDays($createFirst = false) {
        $result = array();
        if($createFirst) {
            $result[] = new FormSheduleExpAdd();
        } else {
            // Здесь нужно сделать выборку тех дней-исключений, которые уже есть в базе..
        }
        return $result;
    }

    public function actionAddEdit() {
        $model = new FormSheduleAdd();
        if(isset($_POST['FormSheduleAdd'])) {
            $model->attributes = $_POST['FormSheduleAdd'];
            if($model->validate()) {
                $this->addEditModelShedule($model);
                echo CJSON::encode(array('success' => 'true',
                                         'msg' => 'Операция успешно проведена, расписание сохранено'));
            } else {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => $model->errors));
            }
        }
    }

    public function addEditModelShedule($model) {
        if($model->doctorId != null) {
            $days = SheduleSetted::model()->findAll('employee_id = :employee_id', array(':employee_id' => $model->doctorId));
            if(count($days) != 7) {
                $days = array();
                for($i = 0; $i < 7; $i++) {
                    $days[$i] = new SheduleSetted();
                }
            }
        } else {
            echo CJSON::encode(array('success' => 'false',
                                     'errors' => 'Не cмогу добавить элемент расписания в базу!'));
            exit();
        }
        for($i = 0; $i < 7; $i++) {
            $cabinet = 'cabinet'.$i;
            $days[$i]->cabinet_id = $model->$cabinet;
            if($days[$i]->employee_id == null) {
                $days[$i]->employee_id = $model->doctorId;
            }
            $days[$i]->weekday = $i;
            $timeBegin = 'timeBegin'.$i;
            $days[$i]->time_begin = $model->$timeBegin;
            $timeEnd = 'timeEnd'.$i;
            $days[$i]->time_end = $model->$timeEnd;
            $days[$i]->type = 0; // Обычное расписание
            if(!$days[$i]->save()) {
                echo CJSON::encode(array('success' => 'false',
                                         'errors' => 'Не могу добавить элемент расписания в базу!'));
                exit();
            }
        }
    }


    public function addEditModelSheduleExp($model) {
        if($model->id != null) {
            $day = SheduleSetted::model()->find('id = :id', array(':id' => $model->id));
            if($day == null) {
                $day = new SheduleSetted();
            }
        } else {
            $day = new SheduleSetted();
        }

        $day->cabinet_id = $model->cabinet;
        $day->employee_id = $model->doctorId;
        $day->time_begin = $model->timeBegin;
        $day->time_end = $model->timeEnd;
        $day->day = $model->day;
        $day->type = 1; // День-исключение

        if(!$day->save()) {
            echo CJSON::encode(array('success' => 'false',
                                     'errors' => 'Не могу добавить элемент расписания в базу!'));
            exit();
        }
    }

    public function actionAddEditExps() {
        if(isset($_POST['FormSheduleExpAdd'])) {
            foreach($_POST['FormSheduleExpAdd'] as $key => $item) {
                $model = new FormSheduleExpAdd();
                $model->attributes = $item;
                if(!$model->validate()) {
                    echo CJSON::encode(array('success' => 'false',
                                             'errors' => $model->errors));
                    exit();
                } else {
                    $this->addEditModelSheduleExp($model);
                }
            }
        }
        echo CJSON::encode(array('success' => 'true',
                                 'msg' => 'Операция успешно проведена, расписание сохранено'));
    }
}