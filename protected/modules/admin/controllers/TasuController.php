<?php
class TasuController extends Controller {
    public $layout = 'application.modules.admin.views.layouts.index';
    public $answer = array();
    public $tableSchema = 'mis';
	public $version_end = '9223372036854775807';
	public $processed = 0;
	public $totalRows = 0;
	public $lastId = 1;
	public $numErrors = 0;
	public $numAddedPatients = 0;
	public $numAddedDoctors = 0;
	public $numAdded = 0;
	public $log = array();
    // Просмотр страницы интеграции с ТАСУ
    public function actionView() {
        if(isset($_GET['iframe'])) {
            $this->layout = 'application.modules.admin.views.layouts.empty';
            $this->render('empty', array());
        } else {
            // Смотрим, какие таблицы есть в базе
            $sql = "SELECT t.schemaname, c.relname, d.description
                    FROM pg_class c
                    join pg_description d on d.objoid = c.oid
                    join pg_attribute a on a.attrelid = c.oid
                    join pg_tables t on t.tablename = c.relname
                    WHERE t.schemaname = '".$this->tableSchema."'
                        and d.objsubid = 0
                        and a.attname = 'tableoid'";
            $command = Yii::app()->db->createCommand($sql);
            $tables = $command->queryAll();
            $this->answer['model'] = new FormTableModel();
            $tablesArr = array('-1' => 'Не выбрано');
            foreach($tables as $table) {
                $tablesArr[$table['relname']] = $table['relname'].' ('.$table['description'].')';
            }
            $this->answer['tables'] = $tablesArr;
            // Модель полей база-поле импортируемого документа
            $this->answer['modelTasuImportField'] = new FormTasuImportField();

            // Теперь смотрим список файлов
            $filesList = $this->getTasuFilesList();
            $this->answer['filesList'] = $filesList;

            // Модели шаблонов полей и ключа
            $this->answer['modelAddFieldTemplate'] = new FormTasuFieldTemplate();
            $this->answer['modelAddKeyTemplate'] = new FormTasuKeyTemplate();
            $this->answer['fieldsTemplatesList'] =  array();
            $this->answer['keysTemplatesList'] = array();
            $this->render('view', $this->answer);
        }
    }

    public function actionReloadFilesList() {
        echo CJSON::encode(
            array('success' => true,
                  'data' => $this->getTasuFilesList()
            )
        );
    }

    public function getTasuFilesList() {
        if(file_exists(getcwd().'/uploads/tasu')) {
            // Получим список всех файлов в директории
            $files = scandir(getcwd().'/uploads/tasu');
            // Первые два файла отрезаем: это директории
            $files = array_slice($files, 2);
            // Теперь ищем по базе реальные имена файлов
            $result = array();
            foreach($files as &$file) {
                $row = FileModel::model()->find('path = :path', array(':path' => '/uploads/tasu/'.$file));
                if($row != null && file_exists(getcwd().$row->path)) {
                    // Фильтруем: если файл не CSV, то пропускаем
                    if(strtolower(substr($row->path, strrpos($row->path, '.') + 1)) != 'csv') {
                        continue;
                    }
                    $result[] = array(
                        'realName' => $row->filename,
                        'icon' => 'csv.png',
                        'id' => $row->id
                    );
                }
            }
            return $result;
        } else {
            return array();
        }
    }

    // Загрузка ОМС
    public function actionUploadOms() {
        // Файлы есть, будем грузить
        if(Yii::app()->user->isGuest) {
            exit('Недостаточно прав для выполнения операции.');
        }
        if(count($_FILES) > 0) {
            ini_set('max_execution_time', 0);
            $uploadedFile = CUploadedFile::getInstanceByName('uploadedFile');

            // Создадим директории, если не существуют
            if(!file_exists(getcwd().'/uploads')) {
                mkdir(getcwd().'/uploads');
            }
            if(!file_exists(getcwd().'/uploads/tasu')) {
                mkdir(getcwd().'/uploads/tasu');
            }
            $filenameGenered = md5(date('Y-m-d h:m:s').$_FILES['uploadedFile']['name']);
            $path = '/uploads/tasu/'.$filenameGenered.'.'.$uploadedFile->getExtensionName();

            $fileModel = new FileModel();
            $fileModel->owner_id = Yii::app()->user->id;
            $fileModel->type = 1; // Файл для ТАСУ
            $fileModel->filename = $uploadedFile->getName();
            $fileModel->path = $path;
            if(!$fileModel->save()) {
                // Файл не начат качаться
                echo CJSON::encode(array(
                        'success' => false,
                        'error' => 'Невозможно сохранить файл в базе!'
                    )
                );
            }

            $uploadedFile->saveAs(getcwd().$path);
            $_SESSION['uploadedFile'] = getcwd().$path; // Файл закачан, глупый фикс
            $req = new CHttpRequest();
            if(!isset($_SERVER['HTTP_REFERER'])) {
                echo "";
                exit();
            } else {
                $req->redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function actionGetUploadProgressInfo() {
        if(isset($_SESSION[ini_get('session.upload_progress.prefix').'upfile'])) {
            $fileInfo = $_SESSION[ini_get('session.upload_progress.prefix').'upfile'];
            echo CJSON::encode(array('success' => true,
                    'data' => array(
                        'uploaded' => $fileInfo['bytes_processed'],
                        'filesize' => $fileInfo['content_length'],
                        'done' => $fileInfo['done']
                    )
                )
            );
            exit();
        } elseif(isset($_SESSION['uploadedFile']) && file_exists($_SESSION['uploadedFile'])) { // Временный фикс, пока нельзя отследить по-нормальному загрузку файлов
            unset($_SESSION['uploadedFile']);

            // Файл закачан полностью
            echo CJSON::encode(array(
                    'success' => true,
                    'data' => array(
                        'uploaded' => 100,
                        'filesize' => 100
                    )
                )
            );
        } else {
            // Файл не начат качаться
            echo CJSON::encode(array(
                    'success' => true,
                    'data' => array(
                        'uploaded' => 0,
                        'filesize' => 100
                    )
                )
            );
        }
    }

    public function actionGetCsvFields() {
        if(!isset($_GET['tasufile'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                    )
                )
            );
            exit();
        }

        $fileFields = $this->getTasuCsvFileHeaders($_GET['tasufile']);
        echo CJSON::encode(array(
                'success' => true,
                'data' => array(
                    'fileFields' => $fileFields
                )
            )
        );
    }

    public function actionGetTableFields() {
        if(!isset($_GET['table'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                    )
                )
            );
            exit();
        }

        $columns = $this->getTableColumns($_GET['table']);
        $dbFields = array();

        foreach($columns as $column) {
            $dbFields[$column['column_name']] = $column['column_name'];
        }
        // Выясняем реальное имя файла
        $file = FileModel::model()->findByPk($_GET['tasufile']);
        if($file != null) {
            $fileFields = $this->getTasuCsvFileHeaders($file['path']);
            echo CJSON::encode(array(
                    'success' => true,
                    'data' => array(
                        'dbFields' => $dbFields,
                        'fileFields' => $fileFields
                    )
                )
            );
        } else {
            echo CJSON::encode(array(
                    'success' => false,
                    'error' => 'Невозможно найти файл в базе!'
                )
            );
        }
    }

    private function getTableColumns($table, $objSubId = false) {
        /*$sql = "SELECT t.schemaname, c.relname, d.description, a.attname, d.objsubid, a.attnum
                    FROM pg_class c
                    join pg_description d on d.objoid = c.oid
                    join pg_attribute a on a.attrelid = c.oid
                    join pg_tables t on t.tablename = c.relname
                    WHERE  c.relname = '".$table."'
                           and t.schemaname = '".$this->tableSchema."'
                           and a.attstattarget = -1 "; */
       /* $sql = "SELECT
                     a.attname, d.description
                FROM
                    pg_catalog.pg_attribute a
                    INNER JOIN pg_catalog.pg_class c on a.attrelid = c.oid
                    INNER JOIN pg_description d on d.objoid = c.oid
                WHERE
                    c.relname = '".$table."'
                    and a.attnum > 0
                    and a.attisdropped = false"; */
      /*  if($objSubId === false) {
            $sql .= "and d.objsubid > 0";
        } else {
            $sql .= "and d.attnum = ".$objSubId; // Выборка наименования столбцов
        } */
        $sql = "SELECT *
                FROM information_schema.columns
                WHERE information_schema.columns.table_name = '".$table."'";

        $command = Yii::app()->db->createCommand($sql);
        $columns = $command->queryAll();
        return $columns;
    }

    public function getTasuCsvFileHeaders($filePath) {
        // Создадим директории, если не существуют
        if(!file_exists(getcwd().'/uploads')) {
            mkdir(getcwd().'/uploads');
        }
        if(!file_exists(getcwd().'/uploads/tasu')) {
            mkdir(getcwd().'/uploads/tasu');
        }
        if(!file_exists(getcwd().$filePath)) {
            return array();
        } else {
            $file = fopen(getcwd().$filePath, 'r');
            // Смотрим заголовки
            $firstRow = fgetcsv($file);
            return $firstRow;
            fclose($file);
        }
    }

    public function actionAddFieldTemplate() {
        $model = new FormTasuFieldTemplate();
        if(isset($_POST['FormTasuFieldTemplate'])) {
            $model->attributes = $_POST['FormTasuFieldTemplate'];
            if($model->validate()) {
                $template = new TasuFieldsTemplate();
                $template->name = $model->name;
                $template->template = $model->template;
                $template->table = $model->table;
                if(!$template->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'errors' => $model->errors));
                } else {
                    echo CJSON::encode(array('success' => true,
                                             'data' => array()));
                }
            } else {
                echo CJSON::encode(array('success' => false,
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionAddKeyTemplate() {
        $model = new FormTasuKeyTemplate();
        if(isset($_POST['FormTasuKeyTemplate'])) {
            $model->attributes = $_POST['FormTasuKeyTemplate'];
            if($model->validate()) {
                $template = new TasuKeysTemplate();
                $template->name = $model->name;
                $template->template = $model->template;
                $template->table = $model->table;
                if(!$template->save()) {
                    echo CJSON::encode(array('success' => false,
                        'errors' => $model->errors));
                } else {
                    echo CJSON::encode(array('success' => true,
                                             'data' => array()));
                }
            } else {
                echo CJSON::encode(array('success' => false,
                                         'errors' => $model->errors));
            }
        }
    }

    public function actionGetFieldsTemplates() {
        if(!isset($_GET['table'])) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array()));
            exit();
        }

        echo CJSON::encode(array('success' => true,
                                 'data' => TasuFieldsTemplate::model()->findAll('t.table = :table', array(':table' => $_GET['table']))));
    }

    public function actionGetKeysTemplates() {
        if(!isset($_GET['table'])) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array()));
            exit();
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => TasuKeysTemplate::model()->findAll('t.table = :table', array(':table' => $_GET['table']))));
    }

    public function actionGetOneKeyTemplate() {
        if(!isset($_GET['id'])) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array()));
            exit();
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => TasuKeysTemplate::model()->findByPk($_GET['id'])));
    }

    public function actionGetOneFieldTemplate() {
        if(!isset($_GET['id'])) {
            echo CJSON::encode(array('success' => false,
                                     'data' => array()));
            exit();
        }
        echo CJSON::encode(array('success' => true,
                                 'data' => TasuFieldsTemplate::model()->findByPk($_GET['id'])));
    }

    public function actionDeleteFieldsTemplate($id) {
        try {
            $template = TasuFieldsTemplate::model()->findByPk($id);
            $template->delete();
            echo CJSON::encode(array('success' => true,
                                     'data' => 'Шаблон успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => false,
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    public function actionDeleteKeysTemplate($id) {
        try {
            $template = TasuKeysTemplate::model()->findByPk($id);
            $template->delete();
            echo CJSON::encode(array('success' => true,
                                     'data' => 'Шаблон успешно удалён.'));
        } catch(Exception $e) {
            // Это нарушение целостности FK
            echo CJSON::encode(array('success' => false,
                                     'error' => 'На данную запись есть ссылки!'));
        }
    }

    // Проверяем есть ли файл на диске и в базе
    private function checkDbFile($fileFromDb)
    {
        if($fileFromDb == null) {
            echo CJSON::encode(array('success' => false,
                                     'error' => 'Такого файла не существует.'));
            exit();
        }
        if(!(file_exists(getcwd().$fileFromDb->path))) {
            echo CJSON::encode(array('success' => false,
                                     'error' => 'Такого файла нет на диске'));
            exit();
        }
    }
    
    // Считает кол-во строк в файле CSV
    private function countRowFile($file)
    {
        $result = 0;
        while(!feof($file)) {
            $row = fgets($file);
            $result ++;
        }
        return $result;
    }
    
    // Сама процедура импорта
    public function actionImport($table, $fields, $key, $file, $per_query, $rows_num, $rows_numall, $rows_accepted, $rows_discarded, $rows_error, $processed) {
        // Вынимаем файл из базы
        $fileFromDb = FileModel::model()->findByPk($file);
        // Проверим наличие файла на диске и в базе
        $this->checkDbFile($fileFromDb);

        $csvHeaders = $this->getTasuCsvFileHeaders($fileFromDb->path);

        $filesize = filesize(getcwd().$fileFromDb->path);
        $file = fopen(getcwd().$fileFromDb->path, 'r');

        // Если кол-во строк нулевое - посчитаем общее количество строк
        if($rows_numall == 0) {
          $this->countRowFile($file);
        }

        fseek($file, $processed); // Это позволяет читать файл с того места, где закончили

        // Расписываем фильтры: формировать sql-запрос можно один раз
        $fields = CJSON::decode($fields);
        $key = CJSON::decode($key);
        // Формирование происходит в два этапа. Сначала расписывается ключ
        $sql = 'SELECT * FROM mis.'.$table.' t WHERE ';
        $sqlWhere = ''; // Строка для предложения Where
        $sqlInsert = 'INSERT INTO mis.'.$table.' ';
        $sqlInsertFields = '(';
        $sqlInsertPlaceholders = 'VALUES(';
        $data = array();
        $updateFieldValues = array();
        
        foreach($fields as $obj) {
            $sqlWhere .= 't.'.$obj['dbField'].' = :'.$obj['dbField'].' AND ';
            $data[':'.$obj['dbField']] = $obj['tasuField']; // Пока нет данных, то ставим соответствие номер поля в CSV
            $sqlInsertFields .= $obj['dbField'].',';
            $sqlInsertPlaceholders .= ':'.$obj['dbField'].',';
            $updateFieldValues[$obj['dbField']] = '';
        }
        $sqlWhere = mb_substr($sqlWhere, 0, mb_strlen($sqlWhere) - 5);
        $sqlInsertFields = mb_substr($sqlInsertFields, 0, mb_strlen($sqlInsertFields) - 1);
        $sqlInsertFields .= ')';
        $sqlInsertPlaceholders = mb_substr($sqlInsertPlaceholders, 0, mb_strlen($sqlInsertPlaceholders) - 1);
        $sqlInsertPlaceholders .= ')';

        $count = 0;
        $headerFlag = false; // Флаг о том, что заголовки прочитаны
        while(!feof($file)) {
            if($rows_num == 0 && $headerFlag === false) {
                $currentRow = fgets($file); // Это строка заголовков
                $headerFlag = true;
                continue;
            }
            if($count < $per_query) {
                $currentRow = fgets($file);
                $processed += mb_strlen($currentRow);
                // Делим строку в CSV в массив
                $csvArr = explode(',', $currentRow);
                $tempArr = $data;
                $sqlCopy = $sql.$sqlWhere;
                $sqlInsertPlaceholdersCopy = $sqlInsertPlaceholders;
                foreach($data as $key => &$field) {
                    $field = $csvArr[$field];
                    // Заменяем в запросе
                   // $sqlCopy = str_replace($key, "'".str_replace("'","''",mb_convert_encoding($field, "UTF-8"))."'", $sqlCopy);
                    //$sqlInsertPlaceholdersCopy  = str_replace($key, "'".str_replace("'","''",mb_convert_encoding($field, "UTF-8"))."'", $sqlInsertPlaceholdersCopy);
                    $sqlCopy = str_replace($key, "'".str_replace("'","''",$field)."'", $sqlCopy);
                    $sqlInsertPlaceholdersCopy  = str_replace($key, "'".str_replace("'","''",$field)."'", $sqlInsertPlaceholdersCopy);
                }

                $command = Yii::app()->db->createCommand($sqlCopy);
                $elements = @$command->queryAll(true, array(), true);
                //var_dump($elements);
                //exit();
                if($elements === false) { // Строка с ошибкой
                    $rows_error++;
                } elseif(count($elements) > 0) {
                    // Строка есть, пропускать-не импортировать
                    //   TODO: Сделать обновление строки
                    //  Перебираем обновляемые поля текущей записи и добавляемой строки и сравниваем.
                    //  Если хотя бы значение одного поля в файле не равно хотя бы одному полю в базе -
                    //    выполняем update
                    
                    
                    
                    $rows_discarded++;
                } else {
                    $rows_accepted++;
                    // TODO: здесь сделать вставку в таблицу новых данных
                    $query = $sqlInsert.' '.$sqlInsertFields.' '.$sqlInsertPlaceholdersCopy;
                    $command = Yii::app()->db->createCommand($query);
                    // Пытаемся выполнить команду
                    try
                    {
                        $result = @$command->execute(array(), true);
                        if($result === false)
                        {
                            // Запрос не выполнен, т.к. строка не вставлена. Это ошибка
                            $rows_error++;
                        }
                    }
                    catch (Exception $e)
                    {
                        // Если произошло исключение - значит строка не вставлена
                        $rows_error++;
                    }
                    
                }
                // После того, как запрос прошёл, сбросить аргументы на то, что было
                $data = $tempArr;
                $rows_num++;
                $count++;
                $command = Yii::app()->db->createCommand($sql, $data);
            } else {
                break;
            }
        }
        fclose($file);
        echo CJSON::encode(array('success' => true,
                                 'data' => array(
                                     'rowsNumAll' => $rows_numall,
                                     'rowsNum' => $rows_num,
                                     'rowsError' => $rows_error,
                                     'rowsAccepted' => $rows_accepted,
                                     'rowsDiscarded' => $rows_discarded,
                                     'filesize' => $filesize,
                                     'processed' => $processed // Кол-во обработанных байт
                                 )));
    }


    public function actionViewIn() {
		//$tasuTap = new TasuTap();
		
        $this->render('viewin', array(
            'modelAdd' => new FormTasuBufferAdd()
        ));
    }

    // Получить все приёмы для импорта в ТАСУ для последнего задания
    public function actionGetBufferGreetings() {
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

        $model = new TasuGreetingsBuffer();
        $num = $model->getLastBuffer($filters);

        $totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;

        $buffer = $model->getLastBuffer($filters, $sidx, $sord, $start, $rows);
        foreach($buffer as &$element) {
            $parts = explode('-', $element['patient_day']);
            $element['patient_day'] = $parts[2].'.'.$parts[1].'.'.$parts[0];
            if($element['is_beginned'] == null && $element['is_accepted'] == null) {
                $element['status'] = 'Не начат';
            } elseif($element['is_beginned'] == 1 && $element['is_accepted'] == null) {
                $element['status'] = 'Начат, идёт';
            } elseif($element['is_beginned'] == 1 && $element['is_accepted'] == 1) {
                $element['status'] = 'Окончен';
            } else {
                $element['status'] = 'Неизвестно';
            }
        }

        echo CJSON::encode(array(
            'success' => true,
            'rows' => $buffer,
            'total' => $totalPages,
            'records' => count($num)));
    }

    public function actionGetBufferHistoryGreetings() {
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

        $model = new TasuGreetingsBufferHistory();
        $num = $model->getRows($filters);

        $totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;

        $buffer = $model->getRows($filters, $sidx, $sord, $start, $rows);
        foreach($buffer as &$element) {
            $parts = explode(' ', $element['create_date']);
            $parts2 = explode('-', $parts[0]);
            $element['create_date'] = $parts2[2].'.'.$parts2[1].'.'.$parts2[0].' '.$parts[1];

            if($element['status'] == 1) {
                $element['status'] = 'Завершена';
            } else {
                $element['status'] = 'Не завершена';
            }
        }

        echo CJSON::encode(array(
            'success' => true,
            'rows' => $buffer,
            'total' => $totalPages,
            'records' => count($num)));
    }

    /* Получить все приёмы, которые не занесены в буфер */
    public function actionGetNotBufferedGreetings() {
        $notBuffered = TasuGreetingsBuffer::model()->getAllNotBuffered();
        $forCombo = array();
        foreach($notBuffered as &$element) {
            $parts = explode('-', $element['patient_day']);
            $forCombo[$element['id']] = '№'.$element['id'].', пациент '.$element['patient_fio'].', врач '.$element['doctor_fio'].', дата приёма - '.$parts[2].'.'.$parts[1].'.'.$parts[0]; // TODO
        }
        echo CJSON::encode(array(
            'success' => true,
            'data' => $forCombo
        ));
    }

    /* Добавить приём к буферу */
    public function actionAddGreetingToBuffer() {
        if(isset($_POST['FormTasuBufferAdd'], $_POST['FormTasuBufferAdd']['greetingId'])) {
            // Проверим, есть ли такой приём, и, если есть добавим в буфер
            $issetGreeting = SheduleByDay::model()->findByPk($_POST['FormTasuBufferAdd']['greetingId']);
            if($issetGreeting != null) {
                $buffer = new TasuGreetingsBuffer();
                $buffer->greeting_id = $issetGreeting->id;
                $buffer->import_id = $buffer->getLastImportId();
                if(!$buffer->save()) {
                    echo CJSON::encode(array('success' => false,
                                             'text' => 'Ошибка сохранения буфера выгрузки ТАСУ.'));
                }
            }
            echo CJSON::encode(array(
                'success' => true,
                'data' => array()
            ));
        }
    }

    /* Удалить элемент из выгрузки */
    public function actionDeleteFromBuffer() {
        if(!isset($_GET['id'])) {
            echo CJSON::encode(array(
                'success' => false,
                'error' => 'Не задан элемент для удаления!'
            ));
            exit();
        }
        TasuGreetingsBuffer::model()->deleteByPk($_GET['id']);
        echo CJSON::encode(array(
            'success' => true,
            'data' => 'Приём успешно удалён из очереди для выгрузки.'
        ));
    }

    /* Очистка всего буфера */
    public function actionClearBuffer() {
        TasuGreetingsBuffer::model()->deleteAll('status != 1');
        echo CJSON::encode(array(
            'success' => true,
            'data' => 'Буфер успешно очищен.'
        ));
    }

    /* Добавление всех возможных приёмов */
    public function actionAddAllGreetings() {
        $notBuffered = TasuGreetingsBuffer::model()->getAllNotBuffered();
        $lastImportId = null;
        foreach($notBuffered as $key => $element) {
            $buffer = new TasuGreetingsBuffer();
            if($lastImportId == null) {
                $lastImportId = $buffer->getLastImportId();
            }
            $buffer->greeting_id = $element['id'];
            $buffer->import_id = $lastImportId;
            if(!$buffer->save()) {
                echo CJSON::encode(array(
                    'success' => false,
                    'data' => 'Невозможно добавить приём в буфер выгрузки!'
                ));
                exit();
            }
        }

        echo CJSON::encode(array(
            'success' => true,
            'data' => 'Успешно сформирован список приёмов на выгрузку!'
        ));
    }

    public function actionImportGreetings() {
        // Делаем выборку чанка приёмов с целью последующей выгрузки
        $currentGreeting = isset($_GET['currentGreeting']) ? $_GET['currentGreeting'] : false;
        $limit = isset($_GET['rowsPerQuery']) ? $_GET['rowsPerQuery'] : false;
        $sord = 'asc';
        $sidx = 'id';
        $start = 0;

        $buffer = TasuGreetingsBuffer::model()->getLastBuffer(false, $sidx, $sord, $start, $limit, $currentGreeting);

        $this->log = array();
        $this->lastId = null;
        $importId = null;
        if(isset($_GET['totalRows']) && $_GET['totalRows'] == null) {
            $rows = TasuGreetingsBuffer::model()->getLastBuffer(false);
            $this->totalRows = count($rows);
            // Это прогон в первый раз. Если totalRows = 0, то заданий нет, это ошибка
            if($this->totalRows == 0) {
                echo CJSON::encode(array(
                    'success' => false,
                    'error' => 'Нет приёмов для выгрузки!'
                ));
                exit();
            }
        } elseif(isset($_GET['totalRows'])) {
            $this->totalRows = $_GET['totalRows'];
        } else {
            $this->totalRows = null;
        }
        // Смотрим буфер
        foreach($buffer as $element) {
            $bufferGreetingModel = TasuGreetingsBuffer::model()->findByPk($element['id']);
            if($importId == null) {
                $importId = $element['import_id'];
            }
            /* --------------- Интеграция с удалённой базой ------------- */
            if(Yii::app()->db2 != null) {
                $this->log[] = '<strong class="text-sucess">[ТАСУ OK]</strong> Соединились с базой ТАСУ...';
            } else {
                $this->log[] = '<strong class="text-danger">[ТАСУ Ошибка]</strong> Потеряно соединение с базой ТАСУ';
                continue;
            }

            $this->moveGreetingToTasuDb($element);
			
            /* --------------- */
            if($bufferGreetingModel != null) {
                $bufferGreetingModel->status = 1;
                if(!$bufferGreetingModel->save()) {
                    $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно изменить статус приёма в буфере c ID'.$bufferGreetingModel->id;
                } else {
                    $this->log[] = '<strong class="text-success">[OK]</strong> Статус приёма в буфере c ID'.$bufferGreetingModel->id.' успешно изменён.';
                }
            }
            $this->lastId = $bufferGreetingModel->id;
        }

        // Делаем контрольный запрос. Если на выборку контрольного запроса выбирается 0 строк, то, значит, это окончание выгрузки. Надо перенести выгрузку в историю
        $moreBuffer = TasuGreetingsBuffer::model()->getLastBuffer(false, $sidx, $sord, $start, $limit, $this->lastId);
        if(count($moreBuffer) == 0) {
            $historyBuffer = new TasuGreetingsBufferHistory();
            $historyBuffer->num_rows = $this->totalRows;
            $historyBuffer->create_date = date('Y-m-d h:i');
            $historyBuffer->status = 1; // Выгружено
            $historyBuffer->import_id = $importId;
            if(!$historyBuffer->save()) {
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно занести выгрузку c ID'.$importId.' в историю выгрузок.';
            } else {
                $this->log[] = '<strong class="text-success">[OK]</strong> Выгрузка с ID'.$importId.' успешно сохранена в истории выгрузок.';
            }
        }

        echo CJSON::encode(array(
            'success' => true,
            'data' => array(
                'processed' => count($buffer),
                'lastGreetingId' => $this->lastId,
                'totalRows' => $this->totalRows,
                'logs' => $this->log,
				'numAddedPatients' => $this->numAddedPatients,
				'numAddedDoctors' => $this->numAddedDoctors,
				'numAdded' => $this->numAdded,
				'numErrors' => $this->numErrors
            )
        ));
    }

    private function moveGreetingToTasuDb($greeting) {
        $patients = $this->searchTasuPatient($greeting);
		$oms = Oms::model()->findByPk($greeting['oms_id']);
        // Добавление пациента, если такой не найден
        if(count($patients) == 0) {
            $medcard = Medcard::model()->findByPk($greeting['medcard']);
            $result = $this->addTasuPatient($medcard, $oms);
            if($result === false) {
                $this->numErrors++;
				return false;
            } else {
				$this->numAddedPatients++;
			}
            $patients = $this->searchTasuPatient($greeting);
        }

        $patient = $patients[0];
        // Добавляем приём (талон, ТАП) к пациенту
        $tap = $this->addTasuTap($patient, $greeting, $oms);
		if($tap !== false) {
			$this->log[] = '<strong class="text-success">[OK]</strong> ТАП на приём '.$greeting['greeting_id'].' добавлен в базу ТАСУ.';
			// Добавляем MKБ-10 диагнозы к приёму
			$this->setMKB10ByTap($tap, $greeting, $oms);
		} else {
			$this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно добавить приём с ID'.$greeting['greeting_id'].' в базу: возможно, полис пациента записан в неправильном формате...?.';
		}
    }

    private function searchTasuPatient($greeting) {
        $conn = Yii::app()->db2;
        try {
            // Номер полиса состоит из двух частей: серия (пробел) номер
            $policyParts = explode(' ', $greeting['oms_number']);
            // Неправильный номер полиса по формату
            if(count($policyParts) != 2) {
                throw new Exception();
            }

            $sql = "SELECT DISTINCT
	                  [pat].[uid] AS [PatientUID],
	                  NULL AS [_guid],
	                  [pat].[fam_18565] AS [Fam],
	                  [pat].[im_53316] AS [Im],
	                  [pat].[ot_48206] AS [Ot],
	                  [pat].[birthday_38523] AS [Birthday],
	                  [pat].[socialstatus_59270] AS [SocialStatus],
	                  [p].[series_14820] AS [Series],
	                  [p].[number_12574] AS [Number],
	                  [p].[state_19333] AS [PolicyState],
	                  [pat].[closeregistrationcause_59292] AS [CloseRegistrationCause]
                    FROM
	                  PDPStdStorage.dbo.t_patient_10905 AS [pat]
	                INNER JOIN PDPStdStorage.dbo.t_policy_43176 AS [p] ON (([p].[patientuid_09882] =
[pat].[uid])) AND ([p].version_end = ".$this->version_end.")
                    INNER JOIN PDPStdStorage.dbo.t_smo_30821 AS [s] ON (([p].[smouid_25976] =
[s].[uid])) AND ([s].version_end = ".$this->version_end.")
                    WHERE
	                  ((([p].[series_14820] = '".trim($policyParts[0])."') AND ([p].[number_12574] = '".trim($policyParts[1])."')
	                  AND ([pat].[closeregistrationcause_59292] IS NULL)))
	                  AND [pat].version_end = ".$this->version_end;

            $resultPatient = $conn->createCommand($sql)->queryAll();
            return $resultPatient;
        } catch(Exception $e) {
			$this->numErrors++;
            return false;
        }
    }

    private function addTasuPatient($medcard, $oms) {
        $conn = Yii::app()->db2;
        $transaction = $conn->beginTransaction();
        try {
            // Номер полиса состоит из двух частей: серия (пробел) номер
            $policyParts = explode(' ', $oms->oms_number);
            // Неправильный номер полиса по формату
            if(count($policyParts) != 2) {
                throw new Exception();
            }

            // Пациент такой должен быть всего один
            $sql = "EXEC PDPStdStorage.dbo.p_patset_20892
                        0,
                        '',
                        '".$oms->last_name."',
                        '".$oms->first_name."',
                        '".$oms->middle_name."',
                        '".$oms->birthday."',
                        '".(($oms->gender == 1) ? 1 : 2)."',
                        NULL,
                        '0',
                        0,
                        0,
                        0,
                        NULL,
                        NULL,
                        NULL,
                        '',
                        NULL,
                        '',
                        '',
                        '".$medcard->work_place.", ".$medcard->work_address."',
                        '".$medcard->profession."',
                        '".$medcard->post."',
                        0,
                        'III(B)',
                        NULL,
                        1,
                        NULL,
                        NULL,
                        NULL,
                        '',
                        ''";
            $result = $conn->createCommand($sql)->execute();

            $birthdayParts = explode('-', $oms->birthday);

            $sql = "SELECT
	                  [_unmdtbl2636].[uid] AS [PatientUID]
                    FROM
                        PDPStdStorage.dbo.t_patient_10905 AS [_unmdtbl2636]
                    WHERE
	                    ((([_unmdtbl2636].[fam_18565] = '".$oms->last_name."')
	                      AND ([_unmdtbl2636].[im_53316] = '".$oms->first_name."')
	                      AND ([_unmdtbl2636].[ot_48206] = '".$oms->middle_name."')
	                      AND (DATEPART(year,[_unmdtbl2636].[birthday_38523]) = ".(int)$birthdayParts[0].")
	                      AND (DATEPART(month,[_unmdtbl2636].[birthday_38523]) = ".(int)$birthdayParts[1].")
	                      AND (DATEPART(day,[_unmdtbl2636].[birthday_38523]) = ".(int)$birthdayParts[2].")
                          AND ([_unmdtbl2636].[uid] <> 0)))
                          AND [_unmdtbl2636].version_end = ".$this->version_end;

            $patientRow = $conn->createCommand($sql)->queryRow();

            $regionCode = '77'; // TODO: времянка
            $smoCode = '025';

            /* Выборка СМО по региону и ID СМО */
            $sql = "SELECT
                        [_unmdtbl2635].[enabled_56485] AS [Enabled],
                        [_unmdtbl2635].[shortname_50023] AS [ShortName]
                    FROM
                        PDPStdStorage.dbo.t_smo_30821 AS [_unmdtbl2635]
                    WHERE
                        ((([_unmdtbl2635].[coderegion_54021] = '".$regionCode."')
                        AND ([_unmdtbl2635].[codesmo_46978] = '".$smoCode."')))
                        AND [_unmdtbl2635].version_end = ".$this->version_end;

            $smoRow = $conn->createCommand($sql)->queryRow();
            if($smoRow == null) {
                return false;
            }

            $sql = "SELECT TOP 1
	                    [_unmdtbl2638].[uid] AS [SMOUID]
                    FROM
                        PDPStdStorage.dbo.t_smo_30821 AS [_unmdtbl2638]
                    WHERE
                    ((([_unmdtbl2638].[coderegion_54021] = '".$regionCode."')
                    AND ([_unmdtbl2638].[codesmo_46978] = '".$smoCode."')))
                    AND [_unmdtbl2638].version_end = ".$this->version_end."
                    ORDER BY
                        [_unmdtbl2638].[enabled_56485] DESC";

            $smoIdRow = $conn->createCommand($sql)->queryRow();
            if($smoIdRow == null) {
                return false;
            }

            $sql = "EXEC PDPStdStorage.dbo.p_patsetpol_48135
                        ".$patientRow['PatientUID'].",
                        0,
                        ".$smoIdRow['SMOUID'].",
                        '".$policyParts[0]."',
                        '".$policyParts[1]."',
                        '".$smoRow['ShortName']."',
                        '".(($oms->status == 0) ? 1 : 3)."',
                        '1',
                        '".$oms->givedate."',
                        '".$oms->enddate."',
                        NULL,
                        '',
                        '',
                        '',
                        NULL,
                        NULL,
                        NULL";

            $result = $conn->createCommand($sql)->execute();

            $sql = "EXEC PDPStdStorage.dbo.p_patsetdul_54915
                        ".$patientRow['PatientUID'].",
                        0,
                        '14',
                        '".$medcard->serie."',
                        '".$medcard->docnumber."',
                        '00',
                        '".$oms->last_name."',
                        '".$oms->first_name."',
                        '".$oms->middle_name."',
                        0,
                        '".(($oms->gender == 1) ? 1 : 2)."',
                        '".$oms->birthday."',
                        '".$medcard->gived_date."',
                        NULL,
                        '',
                        NULL";

            $result = $conn->createCommand($sql)->execute();
            // Вынимаем данные об адресе из МИС-базы

            $addressData = $this->getAddressObjects(CJSON::decode($medcard->address));
            $addressRegData = $this->getAddressObjects(CJSON::decode($medcard->address_reg));

            // Запихнём адреса в ТАСУ
            $sql = "EXEC PDPStdStorage.dbo.p_patsetaddress_06599
                0,
                ".$patientRow['PatientUID'].",
                '1',
                '643',
                ".($addressData['region'] != null ? "'".$addressData['region']->code_cladr."'" : 'NULL').",
                ".($addressData['district'] != null ? "'".$addressData['district']->code_cladr."'" : 'NULL').",
                ".($addressData['settlement'] != null ? "'".$addressData['settlement']->code_cladr."'" : 'NULL').",
                ".($addressData['street'] != null ? "'".$addressData['street']->code_cladr."'" : 'NULL').",
                ".($addressData['region'] != null ? "'".$addressData['region']->name."'" : 'NULL').",
                ".($addressData['district'] != null ? "'".$addressData['district']->name."'" : 'NULL').",
                ".($addressData['settlement'] != null ? "'".$addressData['settlement']->name."'" : 'NULL').",
                ".($addressData['street'] != null ? "'".$addressData['street']->name."'" : 'NULL').",
                ".($addressData['house'] != null ? '"'.$addressData['house'].'"' : 'NULL').",
                ".($addressData['building'] != null ? '"'.$addressData['building'].'"' : 'NULL').",
                ".($addressData['flat'] != null ? '"'.$addressData['flat'].'"' : 'NULL').",
                ".($addressData['postindex'] != null ? '"'.$addressData['postindex'].'"' : 'NULL').",
                0";

            $conn->createCommand($sql)->execute();

            $sql = "EXEC PDPStdStorage.dbo.p_patsetaddress_06599
                0,
                ".$patientRow['PatientUID'].",
                '2',
                '643',
                '".($addressRegData['region'] != null ? $addressRegData['region']->code_cladr : '')."',
                '".($addressRegData['district'] != null ? $addressRegData['district']->code_cladr : '')."',
                '".($addressRegData['settlement'] != null ? $addressRegData['settlement']->code_cladr : '')."',
                '".($addressRegData['street'] != null ? $addressRegData['street']->code_cladr : '')."',
                '".($addressRegData['region'] != null ? $addressRegData['region']->name : '')."',
                '".($addressRegData['district'] != null ? $addressRegData['district']->name : '')."',
                '".($addressRegData['settlement'] != null ? $addressRegData['settlement']->name : '')."',
                '".($addressRegData['street'] != null ? $addressRegData['street']->name : '')."',
                '".($addressRegData['house'] != null ? $addressRegData['house'] : '')."',
                '".($addressRegData['building'] != null ? $addressRegData['building'] : '')."',
                '".($addressRegData['flat'] != null ? $addressRegData['flat'] : '')."',
                '".($addressRegData['postindex'] != null ? $addressRegData['postindex'] : '')."',
                0";

            $conn->createCommand($sql)->execute();
            $transaction->commit();
            return $result;
        } catch(Exception $e) {
			$this->numErrors++;
            return false;
        }
    }

    /* Получить объекты, соотнесённые с адресом */
    private function getAddressObjects($addressDataJson) {
        $answer = array();
        if(isset($addressDataJson['regionId']) && $addressDataJson['regionId'] != null) {
            $answer['region'] = CladrRegion::model()->findByPk($addressDataJson['regionId']);
        } else {
            $answer['region'] = null;
        }
        if(isset($addressDataJson['districtId']) && $addressDataJson['districtId'] != null) {
            $answer['district'] = CladrDistrict::model()->findByPk($addressDataJson['districtId']);
        } else {
            $answer['district'] = null;
        }
        if(isset($addressDataJson['settlementId']) && $addressDataJson['settlementId'] != null) {
            $answer['settlement'] = CladrSettlement::model()->findByPk($addressDataJson['settlementId']);
        } else {
            $answer['settlement'] = null;
        }
        if(isset($addressDataJson['streetId']) && $addressDataJson['streetId'] != null) {
            $answer['street'] = CladrSettlement::model()->findByPk($addressDataJson['streetId']);
        } else {
            $answer['street'] = null;
        }

        $answer['house'] = isset($addressDataJson['house']) ? $addressDataJson['house'] : '';
        $answer['building'] = isset($addressDataJson['building']) ? $addressDataJson['building'] : '';
        $answer['flat'] = isset($addressDataJson['flat']) ? $addressDataJson['flat'] : '';
        $answer['postindex'] = isset($addressDataJson['postindex']) ? $addressDataJson['postindex'] : '';

        return $answer;
    }

    private function addTasuTap($patient, $greeting, $oms) {
        $conn = Yii::app()->db2;
        try {
			$professional = $this->getTasuProfessional($greeting);
			if($professional == false) {
				throw new Exception('Сотрудника не удалось создать / найти');
			}
			
			// Номер полиса состоит из двух частей: серия (пробел) номер
            $policyParts = explode(' ', $oms->oms_number);
            // Неправильный номер полиса по формату

			if(count($policyParts) != 2) {
                throw new Exception();
            }

			$omsRow = $sql = "SELECT
						[a].[uid]
					FROM
						PDPStdStorage.dbo.t_policy_43176 AS [a]
					WHERE
						[a].[series_14820] = '".$policyParts[0]."'
						AND [a].[number_12574] = '".$policyParts[1]."'
						AND [a].version_end = ".$this->version_end;
			
			$omsRow = $conn->createCommand($sql)->queryRow();
			if($omsRow == null) {
				return false;
			}
		
			$sql = "SELECT
						[_unmdtbl6510].[uid] AS [DULUID]
					FROM
						PDPStdStorage.dbo.t_dul_44571 AS [_unmdtbl6510]
					WHERE
						[_unmdtbl6510].[patientuid_53984] = ".$patient['PatientUID']."
						AND [_unmdtbl6510].version_end = ".$this->version_end;
			
			$policyRow = $conn->createCommand($sql)->queryRow();
			if($policyRow == null) {
				return false;
			}
		
			$sql = 'SELECT
						[_unmdtbl13955].[uid] AS [AddressUID]
					FROM
						PDPStdStorage.dbo.t_address_47256 AS [_unmdtbl13955]
					WHERE
						[_unmdtbl13955].[patientuid_32736] = '.$patient['PatientUID'].'
						AND [_unmdtbl13955].addresstype_31280 = 2
						AND [_unmdtbl13955].version_end = '.$this->version_end;
			
			$addressRow = $conn->createCommand($sql)->queryAll();

			if($addressRow == null) {
				// Пробуем найти по адресу регистрации
				$sql = 'SELECT
						[_unmdtbl13955].[uid] AS [AddressUID]
					FROM
						PDPStdStorage.dbo.t_address_47256 AS [_unmdtbl13955]
					WHERE
						[_unmdtbl13955].[patientuid_32736] = '.$patient['PatientUID'].'
						AND [_unmdtbl13955].addresstype_31280 = 1
						AND [_unmdtbl13955].version_end = '.$this->version_end;
				$addressRow = $conn->createCommand($sql)->queryAll();
				if($addressRow == null) {
					return false;
				}
			}

			$tasuTap = new TasuTap();
			$tasuTap->uid = TasuTap::getLastUID() + 1;
			$tasuTap->version_begin = '';
			$tasuTap->version_end = $this->version_end;
			$tasuTap->is_top = 1;
			$tasuTap->created_by = 2;
			$tasuTap->lpuuid_25856 = 55959;
			$tasuTap->patientuid_40511 = $patient['PatientUID'];
			$tasuTap->fillingdate_36966 = $greeting['patient_day'];
			$tasuTap->doctoruid_47963 = $professional['ProfessionalUID'];
			$tasuTap->medicalprogramm_28647 = 'ОМСМО';
			$tasuTap->serviceplace_59680 = '1';
			$tasuTap->dvnaction_51723 = '';
			$tasuTap->dvnsex_24796 = '0';
			$tasuTap->dvnage_00771 = 0;
			$tasuTap->policyuid_53853 = $omsRow['uid'];
			$tasuTap->duluid_44636 = $policyRow['DULUID'];
			$tasuTap->addressuid_30547 = $addressRow[0]['AddressUID'];
			$tasuTap->bookuid_60769 = null;
			$tasuTap->dvnseries_45145 = '';
			$tasuTap->dvnnumber_55059 = '';
			
			if(!$tasuTap->save()) {
				throw new Exception();
			}

            return $tasuTap;
        } catch(Exception $e) {
			$this->numErrors++;
            return false;
        }
    }
	
	private function setMKB10ByTap($tap, $greeting) {
		$diagnosises = PatientDiagnosis::model()->findAll('greeting_id = :greeting_id', array(':greeting_id' => $greeting['greeting_id']));
		foreach($diagnosises as $diagnosis) {
			$mkb10Diag = Mkb10::model()->findByPk($diagnosis['mkb10_id']);
			if($mkb10Diag == null) {
				continue;
			}
			$parts = explode(' ', $mkb10Diag['description']);
			$parts[0] = trim($parts[0]);
			try {
				$tapDiagnosis = new TasuTapDiagnosis();
				$tapDiagnosis->uid = TasuTapDiagnosis::getLastUID() + 1;
				$tapDiagnosis->version_begin = '';
				$tapDiagnosis->version_end = $this->version_end;
				$tapDiagnosis->is_top = 1;
				$tapDiagnosis->created_by = 2;
				$tapDiagnosis->tapuid_30432 = $tap->uid;
				$tapDiagnosis->ismain_36277 = ($diagnosis['type'] == 0) ? 1 : 0;
				$tapDiagnosis->icdcode_39884 = $parts[0];
				$tapDiagnosis->deseasenature_42940 = 1;
				$tapDiagnosis->monitoringstate_54640 = null;
				$tapDiagnosis->trauma_34421 = '';
				
				if(!$tapDiagnosis->save()) {
					throw new Exception();
				}	
				
				// Добавляем услуги
				$this->setTapServices($tapDiagnosis, $greeting);
			} catch(Exception $e) {
				$this->numErrors++;
				return false;
			}			
		}
	}
	
	private function setTapServices($tapDiagnosis, $greeting) {
		// Пока зашиваем жёстко
		$conn = Yii::app()->db2;
		try {
			$tasuTapService = new TasuTapService();
			$tasuTapService->version_begin = '';
			$tasuTapService->version_end = $this->version_end;
			$tasuTapService->is_top = 1;
			$tasuTapService->created_by = 2;
			$tasuTapService->diagnosisuid_34765 = $tapDiagnosis->uid; 
			$tasuTapService->servicecode_20924 = '010106';
			$tasuTapService->count_23546 = 1;
			
			if(!$tasuTapService->save()) {
				throw new Exception();
			}	
			return $tasuTapService;
		} catch(Exception $e) {
			$this->numErrors++;
			return false;
		}
		
	}
	
	private function addTasuProfessional($doctor) {
		// Получим последний ID для таблицы врачей
		$conn = Yii::app()->db2;
		$lastDoctorId = TasuEmployee::getLastUID();
		$tasuEmployee = new TasuEmployee();
		$tasuEmployee->uid = $lastDoctorId + 1;
		$tasuEmployee->version_begin = '';
		$tasuEmployee->version_end = $this->version_end;
		$tasuEmployee->is_top = 1;
		$tasuEmployee->created_by = 2;
		$tasuEmployee->deleted_by = null;
		$tasuEmployee->guid_61986 = null;
		$tasuEmployee->lpuuid_64519 = 55959;
		$tasuEmployee->code_47321 = $doctor->tabel_number;
		$tasuEmployee->fam_45430 = $doctor->last_name;
		$tasuEmployee->im_03922 = $doctor->first_name;
		$tasuEmployee->ot_43242 = $doctor->middle_name;
		$tasuEmployee->fio_24180 = $doctor->last_name.' '.$doctor->first_name.' '.$doctor->middle_name;
		$tasuEmployee->shortfio_00269 = '';
		$tasuEmployee->takeondate_51957 = $doctor->date_begin;
		$tasuEmployee->discharged_46785 = 0;
		$tasuEmployee->dischargedate_63406 = $doctor->date_end;
		if(!$tasuEmployee->save()) {
			throw new Exception();
		}
		
		$lastServiceId = TasuServiceProfessional::getLastUID();
		$tasuService = new TasuServiceProfessional();
		$tasuService->uid = $lastServiceId + 1;
		$tasuService->version_begin = '';
		$tasuService->version_end = $this->version_end;
		$tasuService->is_top = 1;
		$tasuService->created_by = 2;
		$tasuService->deleted_by = null;
		$tasuService->lpuuid_10148 = 55959;
		$tasuService->employeeuid_41855 = $tasuEmployee->uid;
		$tasuService->provideruid_65300 = 54;
		$tasuService->code_03423 = $doctor->tabel_number;
		$tasuService->medicalservice_24160 = 32;
		$tasuService->name_38209 = $doctor->last_name.' '.$doctor->first_name.' '.$doctor->middle_name;
		$tasuService->positionuid_31250 = null;
		$tasuService->fromdate_04636 = null;
		$tasuService->todate_02649 = null;
		if(!$tasuService->save()) {
			throw new Exception();
		}
		
		return $tasuEmployee;
	}
	
	private function getTasuProfessional($greeting) {
		$conn = Yii::app()->db2;
		$doctor = Doctor::model()->findByPk($greeting['doctor_id']);
		if($doctor == null || $doctor->tasu_id != null) {
			return false;
		}

		$sql = "SELECT
					[sp].[uid] AS [ProfessionalUID]
				FROM
					PDPStdStorage.dbo.t_serviceprofessional_43322 AS [sp] 
				INNER JOIN PDPStdStorage.dbo.t_employee_22089 AS [e] ON 
					[sp].[employeeuid_41855] = [e].[uid] 
					AND [e].version_end = ".$this->version_end."
				WHERE
					[sp].[code_03423] = '".$doctor->tabel_number."' 
					AND [e].[lpuuid_64519] = 55959 
					AND [e].[discharged_46785] = 0 
					AND [sp].version_end = ".$this->version_end;
		$professional = $conn->createCommand($sql)->queryRow();
		if($professional == null) {
			$professional = $this->addTasuProfessional($doctor);
			$this->numAddedDoctors++;
		}
		return $professional;
	}

    /* Посмотреть страницу синхронизации с ТАСУ */
    public function actionViewSync() {
        $timestamps = Syncdate::model()->findAll();
        $toTempl = array();
        foreach($timestamps as &$timestamp) {
            $parts = explode(' ', $timestamp['syncdate']);
            $parts2 = explode('-', $parts[0]);
            $timestamp['syncdate'] = $parts2[2].'.'.$parts2[1].'.'.$parts2[0].' '.$parts[1];
            $toTempl[$timestamp['name']] = $timestamp['syncdate'];
        }

        $this->render('viewsync', array(
            'timestamps' => $toTempl
        ));
    }
	
	public function actionSyncOms() {
		if(!isset($_GET['rowsPerQuery'], $_GET['totalMaked'], $_GET['totalRows'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Недостаточно информации о считывании данных!'
                    ))
            );
            exit();
        }
		
		$this->processed = 0;
        $this->numErrors = 0;
        $this->numAdded = 0;

        $this->log = array();

        $omss = TasuAllOms::model()->getRows(false, 'ENP', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);
	
		if($_GET['totalRows'] == null) {
            $this->totalRows = TasuAllOms::model()->getNumRows();
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('oms');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'oms';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $this->log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $this->totalRows = $_GET['totalRows'];
        }
		
		foreach($omss as $oms) {
            $this->processed++;
			
			if(trim($oms['ENP']) == '' || mb_strlen($oms['ENP']) != 16) {
				continue;
			}
			
			$serie = mb_substr($oms['ENP'], 0, 6);
			$number = mb_substr($oms['ENP'], 6);

			$issetOms = Oms::model()->find('t.oms_number = :oms_number',
                array(
                    ':oms_number' => $serie.' '.$number,
                )
            );

			if($issetOms == null) {
                // Добавляем пациента, если его нет
				try {
					$newOms = new Oms();
					$newOms->first_name = $oms['IM'];
					$newOms->last_name = implode('', array_reverse(preg_split('//u', $oms['FAM'], -1, PREG_SPLIT_NO_EMPTY)));
					$newOms->type = 0; // Пока временно так
					$newOms->middle_name = $oms['OT'];
					$newOms->oms_number = $serie.' '.$number;
					$newOms->gender = $oms['SEX'] == 1 ? 1 : 0;
					$newOms->birthday = $oms['BIRTHDAY'];
					$newOms->givedate = $oms['DATE_N'];
					$newOms->status = 0;
					$newOms->enddate = $oms['DATE_E'];
					if(!$newOms->save()) {
						$this->log[] = 'Невозможно импортировать полис с кодом '.$serie.' '.$number;
						$this->numErrors++;
					} else {
						$this->numAdded++;
					}					
				} catch(Exception $e) {
					$this->numErrors++;
				}
            }
		}
		 echo CJSON::encode(array(
                'success' => true,
                'data' => array(
                    'log' => $this->log,
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $this->processed).' пациентов.',
                    'processed' => $this->processed,
                    'totalRows' => $this->totalRows,
                    'numErrors' => $this->numErrors,
                    'numAdded' => $this->numAdded
                ))
        );
	}
	

    /* Синхронизация пациентов: ТАСУ в МИС */
    public function actionSyncPatients() {
        if(!isset($_GET['rowsPerQuery'], $_GET['totalMaked'], $_GET['totalRows'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Недостаточно информации о считывании данных!'
                    ))
            );
            exit();
        }

        $this->processed = 0;
        $this->numErrors = 0;
        $this->numAdded = 0;

        $this->log = array();

        $patients = TasuPatient::model()->getRows(false, 'uid', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);

        if($_GET['totalRows'] == null) {
            $this->totalRows = TasuPatient::model()->getNumRows();
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('patients');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'patients';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $this->log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $this->totalRows = $_GET['totalRows'];
        }

        foreach($patients as $patient) {
            $this->processed++;
            $tasuOms = TasuOms::model()->find('patientuid_09882 = :patient_uid AND version_end = :version_end AND t.state_19333 = :state', array(
               ':patient_uid' => $patient['uid'],
               ':version_end' => $this->version_end,
               ':state' => 1 // Полис активен
            ));
            if($tasuOms == null) {
                continue;
            }

            $issetPatient = Oms::model()->find('t.oms_number = :oms_number',
                array(
                    ':oms_number' => $tasuOms['series_14820'].' '.$tasuOms['number_12574'],
                )
            );

            if($issetPatient == null) {
                // Добавляем пациента, если его нет
				try {
					$newOms = new Oms();
					$newOms->first_name = $patient['im_53316'];
					$newOms->last_name = implode('', array_reverse(preg_split('//u', $patient['fam_18565'], -1, PREG_SPLIT_NO_EMPTY)));
					$newOms->type = 0; // Пока временно так
					$newOms->middle_name = $patient['ot_48206'];
					$newOms->oms_number = $tasuOms['series_14820'].' '.$tasuOms['number_12574'];
					$newOms->gender = $patient['sex_40994'] == 1 ? 1 : 0;
					$newOms->birthday = $patient['birthday_38523'];
					$newOms->givedate = $tasuOms['issuedate_60296'];
					$newOms->status = $tasuOms['state_19333'];
					$newOms->enddate = $tasuOms['voiddate_10849'];
					$newOms->tasu_id = $patient['uid'];
					if(!$newOms->save()) {
						$this->log[] = 'Невозможно импортировать пациента с кодом '.$tasuOms['uid'];
						$this->numErrors++;
					} else {
						$this->numAdded++;
					}
					
					$issetPatient = $newOms;
					
				} catch(Exception $e) {
					$this->numErrors++;
				}
            }
			
			$issetMedcard = TasuMedcard::model()->find('patientuid_37756 = :patient_uid AND version_end = :version_end', 
					array(
						':version_end' => $this->version_end,
						':patient_uid' => $patient['uid']
					)
			);

			$parts = explode('/', $issetMedcard['number_50713']);
			if($issetMedcard != null && count($parts) == 2 && array_search($parts[1], array(11, 12, 13, 14)) !== false) {
				// Создаём медкарту, если такой нет в базе
				$misMedcard = Medcard::model()->findByPk($issetMedcard['number_50713']);
				if($misMedcard == null) {
					$tasuDul = TasuDul::model()->find('patientuid_53984 = :patient_uid AND version_end = :version_end', 
							array(
								':version_end' => $this->version_end,
								':patient_uid' => $patient['uid']
							)
					);

					$misMedcard = new Medcard();
					$misMedcard->card_number = $issetMedcard['number_50713'];
					$misMedcard->enterprise_id = 1; // Монииаг
					$misMedcard->snils = $patient['snils_34985'];
					$tel = '';
					if($patient['homephone_02050'] != null && trim($patient['homephone_02050']) != '') {
						$tel .= $patient['homephone_02050'].' (домашний), ';
					}
					if($patient['workphone_39150'] != null && trim($patient['workphone_39150']) != '') {
						$tel .= $patient['workphone_39150'].' (рабочий), ';
					}
					$misMedcard->contact = $tel;
					$misMedcard->work_place = $patient['employmentplace_12520'];
					$misMedcard->snils = $patient['snils_34985'];
					$misMedcard->profession = $patient['profession_56032'];
					$misMedcard->post = $patient['position_61591'];
					if($tasuDul != null) {
						$misMedcard->serie = $tasuDul['dulseries_30145'];
						$misMedcard->docnumber = $tasuDul['dulnumber_50657'];
						$misMedcard->doctype = 1; // Паспорт
						$misMedcard->gived_date = $tasuDul['issuedate_42162'];
					}
					$misMedcard->policy_id = $issetPatient->id;
					if($patient['invgroup_59187'] != 4) { // Ребёнок-инвалид...? У нас такого нет
						$misMedcard->invalid_group = $patient['invgroup_59187'];
					}
					// Вынимаем адрес. Адрес, если нет о нём данных в КЛАДР в ТАСУ, добавляется в справочники
					$conn = Yii::app()->db2;
					$addresses = TasuAddress::model()->findAll('patientuid_32736 = :patient_uid AND version_end = :version_end', 
							array(
								':version_end' => $this->version_end,
								':patient_uid' => $patient['uid']
							)
					);

					// Два адреса только: адрес проживания и адрес регистрации
					foreach($addresses as $address) {
						$this->createPatientAddressFormTasu($misMedcard, $address);
					}
					if(!$misMedcard->save()) {
						$this->log[] = 'Невозможно создание / перенос медкарты пациента '.$tasuOms['uid'];
						$this->numErrors++;
					} else {
						$this->numAdded++;
					}
				}
			}
        }

        echo CJSON::encode(array(
                'success' => true,
                'data' => array(
                    'log' => $this->log,
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $this->processed).' пациентов.',
                    'processed' => $this->processed,
                    'totalRows' => $this->totalRows,
                    'numErrors' => $this->numErrors,
                    'numAdded' => $this->numAdded
                ))
        );
    }
	
	public function createPatientAddressFormTasu($medcard, $address) {
		$region = CladrRegion::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $address['coderegion_37290']));
		if($region == null) {
			$region = new CladrRegion();
			$region->name = $address['regionname_60536'];
			$region->code_cladr = $address['coderegion_37290'];
			if(!$region->save()) {
				$this->numErrors++;
				throw new Exception();
			}
		}
		$district = CladrDistrict::model()->find('code_cladr = :code_cladr AND code_region = :code_region', array(':code_cladr' => $address['codedistrict_63369'], ':code_region' => $address['coderegion_37290']));
		if($district == null) {
			$district = new CladrDistrict();
			$district->name = $address['districtname_52162'];
			$district->code_region = $address['coderegion_37290'];
			$district->code_cladr = $address['codedistrict_63369'];
			if(!$district->save()) {
				$this->numErrors++;
				throw new Exception();
			}
		}
		$settlement = CladrSettlement::model()->find('code_cladr = :code_cladr AND code_region = :code_region AND code_district = :code_district', array(':code_cladr' => $address['codesettlement_46311'], ':code_district' => $address['codedistrict_63369'], ':code_region' => $address['coderegion_37290']));
		if($settlement == null) {
			$settlement = new CladrSettlement();
			$settlement->name = $address['settlementname_17779'];
			$settlement->code_region = $address['coderegion_37290'];
			$settlement->code_district = $address['codedistrict_63369'];
			$settlement->code_cladr = $address['codesettlement_46311'];
			if(!$settlement->save()) {
				$this->numErrors++;
				throw new Exception();
			}
		}

		$street = CladrStreet::model()->find('code_cladr = :code_cladr AND code_region = :code_region AND code_district = :code_district AND code_settlement = :code_settlement', array(':code_cladr' => $address['codestreet_11408'], ':code_district' => $address['codedistrict_63369'], ':code_region' => $address['coderegion_37290'], ':code_settlement' => $address['codesettlement_46311']));
		if($street == null) {
			$street = new CladrStreet();
			$street->name = $address['streetname_22113'];
			$street->code_region = $address['coderegion_37290'];
			$street->code_district = $address['codedistrict_63369'];
			$street->code_settlement = $address['codesettlement_46311'];
			$street->code_cladr = $address['codestreet_11408'];
			if(!$street->save()) {
				$this->numErrors++;
				throw new Exception();
			}
		}
		
		$addressData = CJSON::encode(array(
			'regionId' => $region->id,
			'districtId' => $district->id,
			'settlementId' => $settlement->id,
			'streetId' => $street->id,
			'house' => $address['housenumber_07908'],
			'flat' => $address['flatnumber_60133'],
			'building' => $address['buildingnumber_35985'],
			'postindex' => $address['postindex_62744']
		));
		
		if($address['addresstype_31280'] == 1) { // Адрес регистрации
			$medcard->address_reg = $addressData;
		}
		if($address['addresstype_31280'] == 2) { // Адрес проживания
			$medcard->address = $addressData;
		}
		
		$patientController = Yii::app()->createController('reception/patient');
		$addressData = $patientController[0]->getAddressStr($addressData, true);
		if($address['addresstype_31280'] == 1) { // Адрес регистрации
			$medcard->address_reg_str = $addressData['addressStr'];
		}
		if($address['addresstype_31280'] == 2) { // Адрес проживания
			$medcard->address_str = $addressData['addressStr'];
		}
	}
	
    /* Синхронизация врачей: ТАСУ в МИС */
    public function actionSyncDoctors() {
		if(!isset($_GET['rowsPerQuery'], $_GET['totalMaked'], $_GET['totalRows'])) {
            echo CJSON::encode(array(
                    'success' => false,
                    'data' => array(
                        'error' => 'Недостаточно информации о считывании данных!'
                    ))
            );
            exit();
        }

        $this->processed = 0;
        $this->numErrors = 0;
        $this->numAdded = 0;

        $this->log = array();

        $doctors = TasuEmployee::model()->getRows(false, 'uid', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);

        if($_GET['totalRows'] == null) {
            $this->totalRows = TasuEmployee::model()->getNumRows();
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('doctors');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'doctors';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $this->log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $this->totalRows = $_GET['totalRows'];
        }

        foreach($doctors as $doctor) {
            $this->processed++;

            $issetDoctor = Doctor::model()->find('t.tasu_id = :tasu_id',
                array(
                    ':tasu_id' => $doctor['uid'],
                )
            );

            if($issetDoctor != null) {
                continue;
            }

            // Добавляем пациента, если его нет
            try {
                $newDoctor = new Doctor();
                $newDoctor->first_name =  $doctor['im_03922'];
                $newDoctor->last_name =  $doctor['fam_45430'];
                $newDoctor->middle_name = $doctor['ot_43242'];
				$newDoctor->tabel_number = $doctor['code_47321'];
				$newDoctor->degree_id = -1;
				$newDoctor->titul_id = -1;
				$newDoctor->date_begin = $doctor['takeondate_51957'];
				$newDoctor->date_end = $doctor['dischargedate_63406'];
                $newDoctor->tasu_id = $doctor['uid'];
                if(!$newDoctor->save()) {
                    $this->log[] = 'Невозможно импортировать врача с кодом '.$doctor['uid'];
                    $this->numErrors++;
                } else {
                    $this->numAdded++;
                }
            } catch(Exception $e) {
                $this->numErrors++;
            }
        }

        echo CJSON::encode(array(
                'success' => true,
                'data' => array(
                    'log' => $this->log,
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $this->processed).' врачей.',
                    'processed' => $this->processed,
                    'totalRows' => $this->totalRows,
                    'numErrors' => $this->numErrors,
                    'numAdded' => $this->numAdded
                ))
        );
    }
}
?>