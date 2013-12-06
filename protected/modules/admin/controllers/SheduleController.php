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
        // Создаём основную запись для расписания, если её нет
        // Если есть расписание, значит можно вынуть ключ
        if($days[0]->date_id != null) {
            $sheduleSettedBeModel = SheduleSettedBe::model()->find('id = :id', array(':id' => $days[0]->date_id));
        } else {
            $sheduleSettedBeModel = new SheduleSettedBe();
        }
        $sheduleSettedBeModel->date_begin = $model->dateBegin;
        $sheduleSettedBeModel->date_end = $model->dateEnd;
        if(!$sheduleSettedBeModel->save()) {
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
            $days[$i]->date_id = $sheduleSettedBeModel->id;
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
                    // Типа, "я пропускаю это или удаляю"
                    if(trim($model->day) == '' && trim($model->timeBegin) == '' && trim($model->timeEnd) == '') {
                        continue;
                    } else { // Хотя бы одно поле не удалено из строки - ошибка!
                        echo CJSON::encode(array('success' => 'false',
                                                 'errors' => $model->errors));
                        exit();
                    }
                } else {
                    $this->addEditModelSheduleExp($model);
                }
            }
        }
        echo CJSON::encode(array('success' => 'true',
                                 'msg' => 'Операция успешно проведена, расписание сохранено'));
    }

    // Получение раписания для конкретного врача
    public function actionGet($id) {
        $rows = SheduleSetted::model()->findAll('employee_id = :employee_id', array(':employee_id' => $id));
        $resultArr = array(
            'data' => array()
        );
        if(count($rows) > 0) {
            $sheduleSettedBeModel = SheduleSettedBe::model()->find('id = :id', array(':id' => $rows[0]->date_id));
            $resultArr['dateBegin'] = $sheduleSettedBeModel->date_begin;
            $resultArr['dateEnd'] = $sheduleSettedBeModel->date_end;
        }
        foreach($rows as $row) {
            $resultArr['data'][] = array(
                'timeBegin' => $row->time_begin,
                'timeEnd' => $row->time_end,
                'cabinetId' => $row->cabinet_id,
                'employeeId' => $row->employee_id,
                'weekday' => $row->weekday,
                'day' => $row->day,
                'type' => $row->type,
                'id' => $row->id
            );
        }
        echo CJSON::encode(array('success' => 'true',
                                 'data' => $resultArr));
    }
}