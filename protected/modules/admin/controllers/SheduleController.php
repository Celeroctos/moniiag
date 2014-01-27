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
            $this->addEditModelShedule($model);
            echo CJSON::encode(array('success' => 'true',
                                     'msg' => 'Операция успешно проведена, расписание сохранено'));
        }
    }

    public function addEditModelShedule($model) {
        if(!$model->validate(array('dateBegin', 'dateEnd', 'doctorId'))) {
            echo CJSON::encode(array('success' => 'false',
                                     'errors' => $model->errors));
            exit();
        }
        $allClean = true; // Флаг, который говорит о том, новое ли совсем расписание или нет. Если хотя бы одно строка в расписании есть, этот флаг примет $i первой найденной непустой строки
        if($model->doctorId != null) {
            $dayModels = SheduleSetted::model()->findAll('employee_id = :employee_id AND date_id != NULL', array(':employee_id' => $model->doctorId));
            $num = count($dayModels);
            $days = array();
            for($i = 0; $i < 7; $i++) {
                // Ищем среди выбранных записей строку с таким днём в расписании
                for($j = 0; $j < $num; $j++) {
                    if($dayModels[$j]->weekday == $i) {
                        $days[$i] = $dayModels[$j];
                        $allClean = $j;
                        break;
                    }
                }
                if(empty($days[$i])) {
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
        if($allClean !== true && $days[$allClean]->date_id != null) {
            $sheduleSettedBeModel = SheduleSettedBe::model()->find('id = :id', array(':id' => $days[$allClean]->date_id));

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
            // Валидируем по отдельности. Если один из атрибутов валидный, а другой - нет, это ошибка. Если оба атрибуты времени пустые, то это значит, что время просто не задано на данный день, и он выходной. Если такой день уже есть, то его надо удалить из базы.
            if(!$model->validate(array('timeBegin'.$i, 'timeEnd'.$i, 'cabinet'.$i))) {
                if(($model->validate(array('timeBegin'.$i)) && !$model->validate(array('timeEnd'.$i))) ||
                   (!$model->validate(array('timeBegin'.$i)) && $model->validate(array('timeEnd'.$i)))) {
                    echo CJSON::encode(array('success' => 'false',
                                             'errors' => $model->errors));
                    exit();
                } else {
                    // Удаляем из базы
                    $m = SheduleSetted::model()->findAll('employee_id = :employee_id AND weekday = :weekday', array(
                        ':employee_id' => $model->doctorId,
                        ':weekday' => $i
                    ));
                    if(count($m) > 0) {
                        foreach($m as $element) {
                            $element->delete();
                        }
                    }
                    continue;
                }
            }

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
                        // т.е. это удаление строки
                        if(trim($model->id) != '' && trim($model->id) != null) {
                            $m =  SheduleSetted::model()->find('id = :id', array(':id' => $model->id));
                            if($m != null) {
                                $m->delete();
                            }
                        }
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
            if($sheduleSettedBeModel != null) {
                $resultArr['dateBegin'] = $sheduleSettedBeModel->date_begin;
                $resultArr['dateEnd'] = $sheduleSettedBeModel->date_end;
            } else {
                $resultArr['dateBegin'] = '';
                $resultArr['dateEnd'] = '';
            }
        }
        foreach($rows as $row) {
            $row->time_begin = substr($row->time_begin, 0, strrpos($row->time_begin, ':'));
            $row->time_end = substr($row->time_end, 0, strrpos($row->time_end, ':'));
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

    // Просмотр календаря выходных дней
    public function actionViewRest() {
        $restModel = new FormRestDaysEdit();
        $restDays = SheduleRest::model()->findAll();
        $restDaysResponse = array();
        if(!isset($_GET['date'])) {
            $dateBegin = date('Y-n-j');
        } else {
            $dateBegin = $_GET['date'];
        }
        foreach($restDays as $day) {
            $restDaysResponse[$day['day']] = array('selected' => 'selected');
        }
        $this->render('rest', array(
            'model' => $restModel,
            'selectedDaysJson' => CJSON::encode($restDaysResponse),
            'selectedDays' => $restDaysResponse,
            'restCalendars' => CJSON::encode($this->getRestDays($dateBegin)),
            'firstDay' => date('w', strtotime($dateBegin)),
            'year' => date('Y'),
            'restDays' => array('Понедельник',
                'Вторник',
                'Среда',
                'Четверг',
                'Пятница',
                'Суббота',
                'Воскресенье')
        ));
    }

    // Редактирование календаря выходных дней
    public function actionRestEdit() {
        $model = new FormRestDaysEdit();
        if(isset($_POST['FormRestDaysEdit'])) {
            $model->attributes = $_POST['FormRestDaysEdit'];
            SheduleRest::model()->deleteAll();
            foreach($model->restDays as $day) {
                $sheduleRest = new SheduleRest();
                $sheduleRest->day = $day;
                if(!$sheduleRest->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'msg' => 'Не могу сохранить выходной день!'));
                }
            }
            echo CJSON::encode(array('success' => true,
                                     'msg' => 'Выходные дни успешно сохранены.'));
        }
    }

    private function getRestDays($dateBegin) {
        $parts = explode('-', $dateBegin);
        $dateEnd = ($parts[0] + 1).'-'.$parts[1].'-'.$parts[2];
        $responseDb = SheduleRestDay::model()->findAll('t.date >= :dateBegin AND t.date < :dateEnd', array(':dateBegin' => $dateBegin, ':dateEnd' => $dateEnd));
        $response = array();
        // Делим всю выборку на 12 месяцев
        foreach($responseDb as $day) {
            $month = date('n', strtotime($day['date']));
            if(!isset($response[$month])) {
                $response[$month] = array();
            }
            // Теперь смотрим, какие даты подгоняются под этот месяц
            $response[$month][] = $day;
        }
        return $response;
    }

    public function actionSetHolidays() {
        if(!isset($_GET['dates'])) {
            echo CJSON::encode(array('success' => false,
                                     'msg' => 'Не могу сохранить выходной день!'));
            exit();
        }
        $dates = CJSON::decode($_GET['dates']);
        SheduleRestDay::model()->deleteAll();
        foreach($dates as $date) {
            $rest = new SheduleRestDay();
            $rest->date = $date;
            if(!$rest->save()) {
                echo CJSON::encode(array('success' => false,
                                         'msg' => 'Не могу сохранить день в расписании!'));
                exit();
            }
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => array()));
    }
}