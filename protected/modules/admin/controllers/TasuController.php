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
	public $logStr = '';
	public $isErrorElement = false; // Флаг, который позволит отловить ошибку (для оставления элемента выгрузки в списке невыгруженных)
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

    public function actionAddFakeGreetingToBuffer() {
		if(isset($_POST['form'])) {
			$form = CJSON::decode($_POST['form']);
			foreach($form as $element) {
				$model = new FormTasuFakeBufferAdd();
				$model->attributes = $element;
				if($model->validate()) {
					$fakeModel = new TasuFakeGreetingsBuffer();
					$fakeModel->card_number = $model->cardNumber;
					$fakeModel->doctor_id = $model->doctorId;
					$fakeModel->primary_diagnosis_id = $model->primaryDiagnosis;
					$fakeModel->greeting_date = $model->greetingDate;
					if(!$fakeModel->save()) {
						echo CJSON::encode(array(
							'success' => false,
							'errors' => array(
								'fakeModel' => array(
									'Невозможно сохранить новый приём!'
								)
							)
						));
						exit();
					}

					// Добавляем вторичные диагнозы
					if($model->secondaryDiagnosis != null) {
						foreach($model->secondaryDiagnosis as $diagId) {
							$secondaryDiagnosisModel = new TasuFakeGreetingsBufferSecDiag();
							$secondaryDiagnosisModel->buffer_id = $fakeModel->id;
							$secondaryDiagnosisModel->diagnosis_id = $diagId;
							
							 if(!$secondaryDiagnosisModel->save()) {
								echo CJSON::encode(array(
									'success' => false,
									'errors' => array(
										'secondaryDiagnosisModel' => array(
											'Невозможно сохранить вторичные диагнозы для нового приёма!'
										)
									)
								));
								exit();
							}
						}
					}

					// Теперь добавление в таблицу обычного буфера
					$buffer = new TasuGreetingsBuffer();
					$lastImportId = $buffer->getLastImportId();
					$buffer->greeting_id = null;
					$buffer->import_id = $lastImportId['max_import_id'];
					$buffer->fake_id = $fakeModel->id;
					$buffer->status = 0;

					if(!$buffer->save()) {
						echo CJSON::encode(array(
							'success' => false,
							'errors' => array(
								'buffer' => array(
									'Невозможно добавить приём в буфер выгрузки!'
								)
							)
						));
						exit();
					}
				}
			}
		}

		echo CJSON::encode(array(
			'success' => true,
			'data' => 'Приёмы успешно добавлены!'
		));
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
         // Список отделений
		$wardsListDb = Ward::model()->getRows(false, 'name', 'asc');

		$wardsList = array('-1' => 'Нет');
		foreach($wardsListDb as $value) {
			$wardsList[(string)$value['id']] = $value['name'].', '.$value['enterprise_name'];
		}
		
		// Список врачей
		$doctorsListDb = Doctor::model()->getRows(false, 'last_name, first_name', 'asc');

		$doctorsList = array();
		foreach($doctorsListDb as $value) {
			if($value['last_name'] == null) {
				$value['middle_name'] = '';
			}
			if($value['tabel_number'] == null) {
				$value['tabel_number'] = 'отсутствует';
			}

			$doctorsList[(string)$value['id']] = $value['last_name'].' '.$value['first_name'].' '.$value['middle_name'].', '.$value['post'].', '.$value['ward'].', табельный номер '.$value['tabel_number'];
		}
		
		asort($doctorsList);

        $this->render('viewin', array(
            'modelAdd' => new FormTasuBufferAdd(),
            'modelAddFake' => new FormTasuFakeBufferAdd(),
			'modelFilter' => new FormTasuFilterExport(),
			'wardsList' => $wardsList,
			'doctorsList' => $doctorsList
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
		
		// Добавка по дате приёма
		if(isset($_GET['date'])) {
			$date = $_GET['date'];
		} else {
			$date = false;
		}
		
		if(isset($_GET['doctor_id']) && $_GET['doctor_id'] != -1) {
			$doctorId = $_GET['doctor_id'];
		} else {
			$doctorId = false;
		}
		
		if(isset($_GET['import_id'])) {
			$importId = $_GET['import_id'];
		} else {
			$importId = false;
		}

        $model = new TasuGreetingsBuffer();
        $num = $model->getLastBuffer($filters, false, false, false, false, false, $date, $doctorId, $importId);

        $totalPages = ceil(count($num) / $rows);
        $start = $page * $rows - $rows;

        $buffer = $model->getLastBuffer($filters, $sidx, $sord, $start, $rows, false, $date, $doctorId, $importId);
        $resultBuffer = array();

        foreach($buffer as &$element) {
            if($element['medcard'] == null) {
                continue;
            }

			if($doctorId !== false && $element['doctor_id'] != $doctorId) {
				continue;
			}
			
			if($date !== false && strtotime($date) != strtotime($element['patient_day'])) {
				continue;
			}

            // Проведён в МИС или нет. По fake_id
            if($element['fake_id'] == null) {
                $element['in_mis_desc'] = 'Да';
            } else {
                $element['in_mis_desc'] = 'Нет';
            }

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
			
			// Вычленяем код диагноза
			$diagnosisPr = Mkb10::model()->findByPk($element['primary_diagnosis_id']);
			if($diagnosisPr != null) {
				$element['pr_diag_code'] = mb_substr($diagnosisPr->description, 0, mb_strpos($diagnosisPr->description, ' '));
			} else {
				$element['pr_diag_code'] = '';
			}

            array_push($resultBuffer, $element);
        }

        echo CJSON::encode(array(
            'success' => true,
            'rows' => $resultBuffer,
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
				if(Yii::app()->user->checkAccess('canCancelImport')) {
					$element['cancel'] = '<a href="#" id="l'.$element['id'].'"><span class="glyphicon glyphicon-remove"></span></a>';
				} else {
					$element['cancel'] = '<span class="glyphicon glyphicon-remove not-active"></span>';
				}
            } elseif($element['status'] == 2) {
				$element['status'] = 'Отменена';
				$element['cancel'] = '-';
			} else {
                $element['status'] = 'Не завершена';
				$element['cancel'] = '-';
            }
			
			if($element['log_path'] != null) {
				$element['log'] = '<a href="/admin/tasu/sendlogfile/?bufferid='.$element['id'].'" target="_blank" id="l'.$element['id'].'"><span class="glyphicon glyphicon-download-alt"></span></a>';
			} else {
				$element['log'] = '-';
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
				$lastImportId = $buffer->getLastImportId();
                $buffer->import_id = $lastImportId['max_import_id'];
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
            $buffer->import_id = $lastImportId['max_import_id'];
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
				$this->logStr .= "[ТАСУ OK] Соединились с базой ТАСУ...\r\n";
            } else {
                $this->log[] = '<strong class="text-danger">[ТАСУ Ошибка]</strong> Потеряно соединение с базой ТАСУ';
				$this->logStr .= "[ТАСУ Ошибка] Потеряно соединение с базой ТАСУ\r\n";
                continue;
            }

            $this->moveGreetingToTasuDb($element);

            /* --------------- */
            if($bufferGreetingModel != null) {
				if(!$this->isErrorElement) {
					$bufferGreetingModel->status = 1;
					if(!$bufferGreetingModel->save()) {
						$this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно изменить статус приёма в буфере c ID'.$bufferGreetingModel->id;
						$this->logStr .= "[Ошибка] Невозможно изменить статус приёма в буфере c ID".$bufferGreetingModel->id."\r\n";
						$this->isErrorElement = true;
					} else {
						$this->log[] = '<strong class="text-success">[OK]</strong> Статус приёма в буфере c ID'.$bufferGreetingModel->id.' успешно изменён.';
						$this->logStr .= "[OK] Статус приёма в буфере c ID".$bufferGreetingModel->id." успешно изменён.\r\n";
					}
				} else {
					$this->isErrorElement = false;
				}
            }
            $this->lastId = $bufferGreetingModel->id;
        }

		if(!file_exists(getcwd().'/uploads/logs')) {
			mkdir(getcwd().'/uploads/logs');
		}
		if(!file_exists(getcwd().'/uploads/logs/tasu')) {
			mkdir(getcwd().'/uploads/logs/tasu');
		}
		
		if(Yii::app()->user->getState('tasuLog', -1) != -1) {
			$filepath = Yii::app()->user->getState('tasuLog');
		} else {
			$filename = md5(time());
			$filepath = '/uploads/logs/tasu/'.$filename.'.txt';
			Yii::app()->user->setState('tasuLog', $filepath);
		}
	//	var_dump(Yii::app()->user->getState('tasuLog', -1));
		file_put_contents(getcwd().'/'.$filepath, $this->logStr, FILE_APPEND);
		
		// Делаем контрольный запрос. Если на выборку контрольного запроса выбирается 0 строк, то, значит, это окончание выгрузки. Надо перенести выгрузку в историю
        $moreBuffer = TasuGreetingsBuffer::model()->getLastBuffer(false, $sidx, $sord, $start, $limit, $this->lastId);
        if(count($moreBuffer) == 0) {
            $historyBuffer = new TasuGreetingsBufferHistory();
            $historyBuffer->num_rows = $this->totalRows;
            $historyBuffer->create_date = date('Y-m-d h:i');
            $historyBuffer->status = 1; // Выгружено
            $historyBuffer->import_id = $importId;
			$historyBuffer->log_path = $filepath;
            if(!$historyBuffer->save()) {
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно занести выгрузку c ID'.$importId.' в историю выгрузок.';
				$this->logStr .= "[Ошибка] Невозможно занести выгрузку c ID".$importId." в историю выгрузок.\r\n";
				$this->isErrorElement = true;
            } else {
                $this->log[] = '<strong class="text-success">[OK]</strong> Выгрузка с ID'.$importId.' успешно сохранена в истории выгрузок.';
				$this->logStr .= "[OK] Выгрузка с ID".$importId." успешно сохранена в истории выгрузок.\r\n";
				$this->isErrorElement = true;
            }
			Yii::app()->user->setState('tasuLog', -1);
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
		$oms = Oms::model()->findByPk($greeting['oms_id']);
		$medcard = Medcard::model()->findByPk($greeting['medcard']);
        $patients = $this->searchTasuPatient($greeting, $oms);
        // Добавление пациента, если такой не найден
        if(count($patients) == 0 || !$patients) {
            $result = $this->addTasuPatient($medcard, $oms);
            if($result === false) {
                $this->numErrors++;
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно добавить пациента с ОМС '.$oms->oms_number.' ('.$oms->last_name.' '.$oms->first_name.' '.$oms->middle_name.')';
				$this->logStr .= "[Ошибка] Невозможно добавить пациента с ОМС ".$oms->oms_number." (".$oms->last_name." ".$oms->first_name." ".$oms->middle_name.")\r\n";
                $this->isErrorElement = true;
				return false;
            } else {
                $this->numAddedPatients++;
            }
            $patients = $this->searchTasuPatient($greeting, $oms);
        } else {
			$conn = Yii::app()->db2; 
			$sql = "SELECT * 
					FROM PDPStdStorage.dbo.t_book_65067
					WHERE (
							[patientuid_37756] = ".$patients[0]['PatientUID']."
						  )
						  AND [version_end] = '".$this->version_end."'";
			
			try {
				$medcardRow = $conn->createCommand($sql)->queryRow();
			} catch(Exception $e) {
				var_dump($e);
				exit();
			}
			
			if($medcardRow != null && $medcardRow['number_50713'] != $medcard->card_number) { // Сработало условие, при котором надо перерегистрировать карту
				// Обновим запись о медкарте в ТАСУ
				$sql = "UPDATE PDPStdStorage.dbo.t_book_65067 
						SET [number_50713] = '".$medcard->card_number."'
						WHERE 
							[patientuid_37756] = ".$patients[0]['PatientUID']." 
							AND [version_end] = '".$this->version_end."'";
				try {
					$conn->createCommand($sql)->execute();
				} catch(Exception $e) {
					return false;
				}
			} elseif($medcardRow == null) {
				// Создать медкарту
				// Максимальный UID
				$sql = "(SELECT MAX(uid) + 1 as nextUid FROM [PDPStdStorage].[dbo].[t_book_65067])";
				$nextUidRow = $conn->createCommand($sql)->queryRow();
				$sql = "INSERT INTO PDPStdStorage.dbo.t_book_65067 (
					[uid],
					[version_begin],
					[version_end], 
					[is_top], 
					[created_by], 
					[deleted_by], 
					[patientuid_37756],
					[booktype_55473],
					[number_50713],
					[disabled_11186])
					VALUES(
						".$nextUidRow['nextUid'].",
						1,
						".$this->version_end.",
						1,
						2,
						NULL,
						".$patients[0]['PatientUID'].",
						'01',
						'".$medcard->card_number."',
						0
					)";

				$result = $conn->createCommand($sql)->execute();
				$this->log[] = '<strong class="text-success">[OK]</strong> В ТАСУ успешно создана медкарта '.$medcard->card_number.'.';
				$this->logStr .= "[OK] В ТАСУ успешно создана медкарта ".$medcard->card_number.".\r\n";
			}
		}
		
        if(count($patients) > 0) {
            $patient = $patients[0];
            // Добавляем приём (талон, ТАП) к пациенту
            $tap = $this->addTasuTap($patient, $greeting, $oms, $medcard);
            if($tap !== false) {
                $this->log[] = '<strong class="text-success">[OK]</strong> ТАП на приём '.$greeting['greeting_id'].' добавлен в базу ТАСУ.';
				$this->logStr .= "[OK] ТАП на приём ".$greeting['greeting_id']." добавлен в базу ТАСУ.\r\n";
                // Добавляем MKБ-10 диагнозы к приёму
                $this->setMKB10ByTap($tap, $greeting, $oms);
            } else {
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно добавить приём с ID'.$greeting['greeting_id'].' в базу: возможно, полис пациента записан в неправильном формате...?.';
				$this->logStr .= "[Ошибка] Невозможно добавить приём с ID".$greeting['greeting_id']." в базу: возможно, полис пациента записан в неправильном формате...?\r\n";
				$this->isErrorElement = true;
            }
        } else {
            $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно найти пациента для приёма с ID'.$greeting['greeting_id'].' в ТАСУ: возможно, пациент не создался в ТАСУ...?.';
			$this->logStr .= "[Ошибка] Невозможно найти пациента для приёма с ID".$greeting['greeting_id']." в ТАСУ: возможно, пациент не создался в ТАСУ...?\r\n";
			$this->isErrorElement = true;
		}
    }

    private function searchTasuPatient($greeting, $oms) {
        $conn = Yii::app()->db2;
        try {
            if($oms->oms_series == null) { // Старый тип структуры базы
                // Номер полиса состоит из двух частей: серия (пробел) номер
                $policyParts = explode(' ', trim($greeting['oms_number']));
                // Неправильный номер полиса по формату
                if(count($policyParts) != 2) {
                    $policyParts = array();
                    if(mb_strlen(trim($greeting['oms_number'])) > 7) {
					
                        $policyParts[0] = mb_substr(trim($greeting['oms_number']), 0, 6);
                        $policyParts[1] = mb_substr(trim($greeting['oms_number']), 6);
                    } else {
                        throw new Exception();
                    }
                }
            } else {
                $policyParts[0] = $oms->oms_series;
                $policyParts[1] = $oms->oms_number;
            }
			
			if($oms->type == 5) { // Единый полис ОМС в одном поле, в номере
				$serie = '';
				$number = trim($policyParts[0]).trim($policyParts[1]);
			} else {
				$serie = $policyParts[0];
				$number = $policyParts[1];
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
	                  ((([p].[series_14820] = '".$serie."') AND ([p].[number_12574] = '".$number."')
	                  AND ([pat].[closeregistrationcause_59292] IS NULL)))
	                  AND [pat].version_end = ".$this->version_end;
//var_dump($sql);
		
            $resultPatient = $conn->createCommand($sql)->queryAll();
            //		var_dump(   $resultPatient);
		//	if($greeting['id'] == 6638) {
				//var_dump($sql);
			//}

            return $resultPatient;
        } catch(Exception $e) {
            $this->numErrors++;
            return false;
        }
    }

    private function addTasuPatient($medcard, $oms) {
		$conn = Yii::app()->db2;
		try {
			// ТАСУ правее нас: корректируем СМО и регион у полиса принудительно
			$this->getTasuPatientByPolicy($oms, 1);
            if($oms->oms_series == null) {
                // Номер полиса состоит из двух частей: серия (пробел) номер
                $policyParts = explode(' ', trim($oms->oms_number));
                // Неправильный номер полиса по формату
                if(count($policyParts) != 2) {
                    $policyParts = array();
                    if(mb_strlen(trim($oms->oms_number)) > 7) {
                        $policyParts[0] = mb_substr(trim($oms->oms_number), 0, 6);
                        $policyParts[1] = mb_substr(trim($oms->oms_number), 6);
                    } else {
                        throw new Exception();
                    }
                }
            } else {
                $policyParts[0] = $oms->oms_series;
                $policyParts[1] = $oms->oms_number;
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
                        '".$medcard->snils."',
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
                        NULL,
                        NULL,
                        1,
                        NULL,
                        NULL,
                        NULL,
                        '',
                        ''";

            $result = $conn->createCommand($sql)->execute();

           /* $transaction->commit(); // ?!
			if($conn->currentTransaction != null) {
				$transaction = $conn->beginTransaction();
			} else {
				$transaction = $conn->currentTransaction;
			} */

            $birthdayParts = explode('-', $oms->birthday);
			if($oms->middle_name == null) {
				$oms->middle_name = '';
			}

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

			// Максимальный UID
			$sql = "(SELECT MAX(uid) + 1 as nextUid FROM [PDPStdStorage].[dbo].[t_book_65067])";
		    $nextUidRow = $conn->createCommand($sql)->queryRow();

			// Крафтим медкарту
			// Посмотрим, есть ли такая медкарта, с таким номером. Если есть - создавать не надо
			$sql = "SELECT * 
					FROM PDPStdStorage.dbo.t_book_65067
					WHERE (
							[number_50713] = '".$medcard->card_number."' OR
							[patientuid_37756] = ".$patientRow['PatientUID']."
						  )
						  AND [version_end] = '".$this->version_end."'";
			try {
				$medcardRow = $conn->createCommand($sql)->queryRow();
			} catch(Exception $e) {
				var_dump($e);
				exit();
			}
 
			if($medcardRow == null) {
				$sql = "INSERT INTO PDPStdStorage.dbo.t_book_65067 (
				[uid],
				[version_begin],
				[version_end], 
				[is_top], 
				[created_by], 
				[deleted_by], 
				[patientuid_37756],
				[booktype_55473],
				[number_50713],
				[disabled_11186])
				VALUES(
					".$nextUidRow['nextUid'].",
					1,
					".$this->version_end.",
					1,
					2,
					NULL,
					".$patientRow['PatientUID'].",
					'01',
					'".$medcard->card_number."',
					0
				)";

				$result = $conn->createCommand($sql)->execute();
			} elseif($medcardRow['number_50713'] != $medcard->card_number) { // Сработало условие, при котором надо перерегистрировать карту
				// Обновим запись о медкарте в ТАСУ
				$sql = "UPDATE PDPStdStorage.dbo.t_book_65067 
						SET [number_50713] = '".$medcard->card_number."'
						WHERE 
							[patientuid_37756] = ".$patientRow['PatientUID']." 
							AND [version_end] = '".$this->version_end."'";
				try {
					$conn->createCommand($sql)->execute();
				} catch(Exception $e) {
					var_dump($e);
					exit();
				}
			}
			// Если не подгружены данные по полису из ТАСУ, самое время это сделать
			if($oms->insurance == null || $oms->region == null) {
				$this->getTasuPatientByPolicy($oms);
				// Если и после этого ничего не пометилось, то пациент не может быть перенесён: нет страховой и региона
			}
			// Выборка страховой компании
			$insurance = Insurance::model()->findByPk($oms->insurance);
			if($insurance != null) {
			 	$smoCode = $insurance->code;
			} else {
				// Может ли такое быть?
				$smoCode = null;
			}
			
            $region = CladrRegion::model()->findByPk($oms->region);
			if($region != null) {
			 	$regionCode = $region->code_cladr;
			} else {
				// Может ли такое быть?
				$regionCode = null;
			}
				
            /* Выборка СМО по региону и ID СМО */
            $sql = "SELECT
                        [t].[enabled_56485] AS [Enabled],
                        [t].[shortname_50023] AS [ShortName],
						[t].[uid] AS [SMOUID]
                    FROM
                        PDPStdStorage.dbo.t_smo_30821 AS [t]
                    WHERE
                        ((([t].[coderegion_54021] = '".$regionCode."')
                        AND ([t].[codesmo_46978] = '".$smoCode."')))
                        AND [t].version_end = ".$this->version_end;

            $smoRow = $conn->createCommand($sql)->queryRow();
            	
			if($smoRow == null) {
                return false;
            }
			
			// В едином номере всё пишется в одно поле
			if($oms->type == 5) {
				$serie = '';
				$number = $policyParts[0].$policyParts[1];
			} else {
				$serie = $policyParts[0];
				$number = $policyParts[1];
			}
			
            $sql = "EXEC PDPStdStorage.dbo.p_patsetpol_48135
                        ".$patientRow['PatientUID'].",
                        0,
                        ".$smoRow['SMOUID'].",
                        '".$serie."',
                        '".$number."',
                        '".$smoRow['ShortName']."',
                        '".(($oms->status == 0) ? 1 : 3)."',
                        '1',
                        '".$oms->givedate."',
                        ".(($oms->enddate == null) ? "NULL" : "'".$oms->enddate."'").",
                        NULL,
                        '',
                        '',
                        '',
                        NULL,
                        NULL,
                        NULL";

			try {
				$result = $conn->createCommand($sql)->execute();
			} catch(Exception $e) {
				var_dump($e);
				exit();
			}
			
			// Выберем последнюю добавленную строку (UID)
			
			// UID полиса
			$sql = "SELECT uid as lastUid 
					FROM [PDPStdStorage].[dbo].[t_policy_43176]
					WHERE [series_14820] = '".$serie."' AND 
						  [number_12574] = '".$number."' AND
						  [version_end] = '".$this->version_end."'";
						  
		    $currentUidRow = $conn->createCommand($sql)->queryRow();

			// Апдейтим полис: статус и тип
			$sql = "UPDATE
						[PDPStdStorage].[dbo].[t_policy_43176]
					SET 
						[state_19333] = ".$oms->status.", 
						[type_07731] = ".$oms->type."
					WHERE
						[uid] = ".$currentUidRow['lastUid'];
						
			try {
				$result = $conn->createCommand($sql)->execute();
			} catch(Exception $e) {
			}

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
            //var_dump($sql);
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
            $answer['street'] = CladrStreet::model()->findByPk($addressDataJson['streetId']);
        } else {
            $answer['street'] = null;
        }

        $answer['house'] = isset($addressDataJson['house']) ? $addressDataJson['house'] : '';
        $answer['building'] = isset($addressDataJson['building']) ? $addressDataJson['building'] : '';
        $answer['flat'] = isset($addressDataJson['flat']) ? $addressDataJson['flat'] : '';
        $answer['postindex'] = isset($addressDataJson['postindex']) ? $addressDataJson['postindex'] : '';

        return $answer;
    }

    private function addTasuTap($patient, $greeting, $oms, $medcard) {
        $conn = Yii::app()->db2;
        try {
            $professional = $this->getTasuProfessional($greeting);
            if($professional == false) {
                throw new Exception('Сотрудника не удалось создать / найти');
            }

            // Номер полиса состоит из двух частей: серия (пробел) номер
            if($oms->oms_series == null) {
                $policyParts = explode(' ', trim($oms->oms_number));
                // Неправильный номер полиса по формату

                if(count($policyParts) != 2) {
                    $policyParts = array();
                    if(mb_strlen(trim($oms->oms_number)) > 7) {
                        $policyParts[0] = mb_substr(trim($oms->oms_number), 0, 6);
                        $policyParts[1] = mb_substr(trim($oms->oms_number), 6);
                    } else {
                        throw new Exception();
                    }
                }
            } else {
                $policyParts[0] = $oms->oms_series;
                $policyParts[1] = $oms->oms_number;
            }
			
			// В едином номере всё пишется в одно поле
			if($oms->type == 5) {
				$serie = '';
				$number = $policyParts[0].$policyParts[1];
			} else {
				$serie = $policyParts[0];
				$number = $policyParts[1];
			}

            $omsRow = $sql = "SELECT
						[a].[uid]
					FROM
						PDPStdStorage.dbo.t_policy_43176 AS [a]
					WHERE
						[a].[series_14820] = '".$serie."'
						AND [a].[number_12574] = '".$number."'
						AND [a].version_end = ".$this->version_end;

            $omsRow = $conn->createCommand($sql)->queryRow();
            if($omsRow == null) {
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно найти полис '.$serie.' '.$number.'!';
				$this->logStr .= "[Ошибка] Невозможно найти полис ".$serie." ".$number."!\r\n";
                $this->isErrorElement = true;
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
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно найти пациента с ID '.$patient['PatientUID'].'!';
				$this->logStr .= "[Ошибка] Невозможно найти пациента с ID ".$patient['PatientUID']."!r\n";
                $this->isErrorElement = true;
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
                    $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно найти пациента с ID '.$patient['PatientUID'].' по адресу регистрации!';
					$this->logStr .= "[Ошибка] Невозможно найти пациента с ID ".$patient['PatientUID']." по адресу регистрации!\r\n";
                    $this->isErrorElement = true;
					return false;
                }
            }
			
			// Медкарта: прикрепление медкарты к ТАП
			$sql = "SELECT * 
					FROM PDPStdStorage.dbo.t_book_65067
					WHERE [number_50713] = '".$medcard->card_number."'
						  AND [version_end] = '".$this->version_end."'";
			try {
				$medcardRow = $conn->createCommand($sql)->queryRow();
			} catch(Exception $e) {
				var_dump($e);
				exit();
			}
			
			if($medcardRow == null) {
				return false;
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
            $tasuTap->bookuid_60769 = $medcardRow['uid'];
            $tasuTap->dvnseries_45145 = '';
            $tasuTap->dvnnumber_55059 = '';

            if(!$tasuTap->save()) {
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Невозможно сохранить TAP для пациента с ID '.$patient['PatientUID'].'!';
				$this->logStr .= "[Ошибка] Невозможно сохранить TAP для пациента с ID ".$patient['PatientUID']."!\r\n";
				$this->isErrorElement = true;
                throw new Exception();
            }
            $this->numAdded++;
            return $tasuTap;
        } catch(Exception $e) {
            $this->numErrors++;
            return false;
        }
    }

    private function setMKB10ByTap($tap, $greeting) {
        // У фейковых приёмов диагноз ищется иначе
        if($greeting['fake_id'] == null) {
            $diagnosises = PatientDiagnosis::model()->findAll('greeting_id = :greeting_id', array(':greeting_id' => $greeting['greeting_id']));
        } else {
            $diagnosises = array(
                array(
                    'mkb10_id' => $greeting['primary_diagnosis_id'],
                    'type' => 0 // Первичный
                )
            );
			// Ищем вторичные для фейковых
			$secondaryDiags =  TasuFakeGreetingsBufferSecDiag::model()->findAll('buffer_id = :buffer_id', array(
				':buffer_id' => $greeting['fake_id']
			));
			foreach($secondaryDiags as $diag) {
				$diagnosises[] = array(
					'mkb10_id' => $diag['diagnosis_id'],
					'type' => 1 // Вторичный
				);
			}

        }
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
                    throw new Exception('Не могу сохранить диагноз для приёма!');
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
        if($doctor == null || $doctor->tabel_number == null) {
            if($doctor == null) {
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Врач для приёма '.$greeting['id'].' не существует!';
				$this->logStr .= "[Ошибка] Врач для приёма ".$greeting['id']." не существует!\r\n";
				$this->isErrorElement = true;
			} elseif($doctor->tabel_number == null) {
                $this->log[] = '<strong class="text-danger">[Ошибка]</strong> Врач для приёма '.$greeting['id'].' не имеет табельного номера!';
				$this->logStr .= "[Ошибка] Врач для приёма ".$greeting['id']." не имеет табельного номера!";
				$this->isErrorElement = true;
            }
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

    /* Посмотреть страницу обсуживания связки МИС <-> ТАСУ */
    public function actionViewService() {
        $timestamps = Syncdate::model()->findAll();
        $toTempl = array();
        foreach($timestamps as &$timestamp) {
            $parts = explode(' ', $timestamp['syncdate']);
            $parts2 = explode('-', $parts[0]);
            $timestamp['syncdate'] = $parts2[2].'.'.$parts2[1].'.'.$parts2[0].' '.$parts[1];
            $toTempl[$timestamp['name']] = $timestamp['syncdate'];
        }

        $this->render('viewservice', array(
            'timestamps' => $toTempl
        ));
    }

    // --- Begin 17.06.2014 ---
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

        $omsStatuses = OmsStatus::model()->findAll();
        $omsStatusesSorted = array();
        foreach($omsStatuses as $omsStatus) {
            $omsStatusesSorted[(string)$omsStatus['tasu_id']] = $omsStatus['id'];
        }

        $omss = TasuAllOms::model()->getRows(false, 'ENP', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);
        if($_GET['totalRows'] == null || $_GET['totalRows'] == 0) {
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

        $omsTypes = OmsType::model()->findAll();
        $omsTypesSorted = array();
        foreach($omsTypes as $omsType) {
            $omsTypesSorted[(string)$omsType['tasu_id']] = $omsType['id'];
        }

        $omsStatuses = OmsStatus::model()->findAll();
        $omsStatusesSorted = array();
        foreach($omsStatuses as $omsStatus) {
            $omsStatusesSorted[(string)$omsStatus['tasu_id']] = $omsStatus['id'];
        }

        $conn = Yii::app()->db;
        $sql = "INSERT INTO mis.oms (first_name, middle_name, last_name, oms_number, oms_series, gender, birthday, type, givedate, enddate, status, oms_series_number) VALUES";
        $issetAnybody = false;

        foreach($omss as $oms) {
            $this->processed++;

            $serie = mb_substr($oms['ENP'], 0, 6);
            $number = mb_substr($oms['ENP'], 6);

            $issetOms = Oms::model()->find('
				(t.oms_number = :oms_number
				AND t.oms_series = :oms_series)
				OR t.oms_number = :oms_series_number',
                array(
                    ':oms_series' => $serie,
                    ':oms_number' => $number,
					':oms_series_number' => $serie.$number
                )
            );

            if($issetOms == null || $issetOms === false) {
                if(!$issetAnybody) {
                    $issetAnybody = true;
                }

                $omsNumber = $serie.$number;
                $omsNumber = str_replace('-', '', $omsNumber);
                $omsNumber = str_replace(' ', '', $omsNumber);

                if($oms['DATE_E'] != null && $oms['DATE_E'] != '') {
                    if(strtotime($oms['DATE_E']) < strtotime(date('Y-m-d'))) {
                        // Активен - 1, погашен - 3.
                        $omsStatus = $omsStatusesSorted[3];
                    } else {
                        $omsStatus = $omsStatusesSorted[1];
                    }
                } else {
                    $omsStatus = $omsStatusesSorted[5];
                }
				
                $omsStatus = $omsStatusesSorted[5];
				
				// Выбираем страховую компанию
				//$insurance = Insurance::model()->find('tasu_id = :tasu_id', array(':tasu_id' => $oms['SMOuid']));
				// Выбираем ID региона
				//$regionCode = CladrRegion::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $oms['CodeRegion']));
                // Добавляем пациента, если его нет
                try {
                    $oms['FAM'] = str_replace("'", "`", $oms['FAM']);
                    $sql .= "('".$oms['IM']."',".
                        "'".$oms['OT']."',".
                        "'".$oms['FAM']."',".
                        "'".$number."',".
                        "'".$serie."',".
                        ($oms['SEX'] == 1 ? "1" : "0").",".
                        "'".$oms['BIRTHDAY']."',
						6,".
                        "'".$oms['DATE_N']."',".
                        ($oms['DATE_E'] == '' ? 'NULL' : "'".$oms['DATE_E']."'").",
						".$omsStatus.",".
                        "'".$omsNumber."'),";

                    $this->numAdded++;
                } catch(Exception $e) {
                    $this->numErrors++;
                }
            }
        }

        if($issetAnybody) {
            $sql = mb_substr($sql, 0, mb_strlen($sql) - 1);
            $result = $conn->createCommand($sql)->execute();
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
    // --- End 17.06.2014 ---


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

        if($_GET['totalRows'] == null || $_GET['totalRows'] == 0) {
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

        $omsTypes = OmsType::model()->findAll();
        $omsTypesSorted = array();
        foreach($omsTypes as $omsType) {
            $omsTypesSorted[(string)$omsType['tasu_id']] = $omsType['id'];
        }

        $omsStatuses = OmsStatus::model()->findAll();
        $omsStatusesSorted = array();
        foreach($omsStatuses as $omsStatus) {
            $omsStatusesSorted[(string)$omsStatus['tasu_id']] = $omsStatus['id'];
        }
		
		$conn = Yii::app()->db2;

		$sql = "SELECT 
					t.*
				FROM
					PDPStdStorage.dbo.t_policy_43176 AS t
				WHERE [t].patientuid_09882 = :patient_uid
				AND [t].version_end = ".$this->version_end;

		$tasuOmsProc = $conn->createCommand($sql);
		
        foreach($patients as $patient) {
            $this->processed++;
			$tasuOms = $tasuOmsProc->queryRow(true, array(':patient_uid' => $patient['uid']));

            if($tasuOms == null) {
                continue;
            } 
			
            $issetPatient = Oms::model()->find('(t.oms_series = :oms_series AND t.oms_number = :oms_number) 
				OR t.oms_number = :oms_serie_number',
                array(
                    ':oms_series' => trim($tasuOms['series_14820']),
                    ':oms_number' => trim($tasuOms['number_12574']),
					':oms_serie_number' => $tasuOms['series_14820'].' '.$tasuOms['number_12574']
                )
            );
			
			$sql = 'SELECT [t].[coderegion_54021] 
					FROM PDPStdStorage.dbo.t_smo_30821 [t]
					WHERE [t].[uid] = '.$tasuOms['smouid_25976'];
			
			$tasuSmo = $conn->createCommand($sql)->queryRow();
			
			if($tasuSmo != null) {
				// Выбираем страховую компанию
				$insurance = Insurance::model()->find('tasu_id = :tasu_id', array(':tasu_id' => $tasuOms['smouid_25976']));
				// Выбираем ID региона
				$regionCode = CladrRegion::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $tasuSmo['coderegion_54021']));
			} else {
				$insurance = $regionCode = null;
			}

			if($issetPatient == null) {
			
                // Добавляем пациента, если его нет
                try {

                    $omsNumber = $tasuOms['series_14820'].$tasuOms['number_12574'];
                    $omsNumber = str_replace('-', '', $omsNumber);
                    $omsNumber = str_replace(' ', '', $omsNumber);

                    $newOms = new Oms();
                    $newOms->first_name = $patient['im_53316'];
                    $newOms->last_name = $patient['fam_18565'];
                    $newOms->type = $omsTypesSorted[$tasuOms['type_07731']];
                    $newOms->middle_name = $patient['ot_48206'];
                    $newOms->oms_number = $tasuOms['number_12574'];
                    $newOms->oms_series = $tasuOms['series_14820'];
                    $newOms->oms_series_number = $omsNumber;
                    $newOms->gender = $patient['sex_40994'] == 1 ? 1 : 0;
                    $newOms->birthday = $patient['birthday_38523'];
                    $newOms->givedate = $tasuOms['issuedate_60296'];
                    if($tasuOms['state_19333'] != null && isset($omsStatusesSorted[$tasuOms['state_19333']])) {
                        $newOms->status = $omsStatusesSorted[$tasuOms['state_19333']];
                    } else {
						$newOms->status = 5; // Неизвестно 
					}
                    $newOms->enddate = $tasuOms['voiddate_10849'];
                    $newOms->tasu_id = $patient['uid'];
					if($insurance != null) {
						$newOms->insurance = $insurance->id;
					}
					if($regionCode != null) {
						$newOms->region = $regionCode->id;
					}
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
            } else {  // Fix: update номеров полисов: разнос на два поля
                $issetPatient->oms_number = $tasuOms['number_12574'];
                $issetPatient->oms_series = $tasuOms['series_14820'];
				$issetPatient->enddate = $tasuOms['voiddate_10849'];
				if($tasuOms['state_19333'] != null && isset($omsStatusesSorted[$tasuOms['state_19333']])) {
					$issetPatient->status = $omsStatusesSorted[$tasuOms['state_19333']];
				} else {
					$issetPatient->status = 5; // Статус неизвестен
				}
				$issetPatient->type = $omsTypesSorted[$tasuOms['type_07731']];
				if($insurance != null) {
					$issetPatient->insurance = $insurance->id;
				}
				if($regionCode != null) {
					$issetPatient->region = $regionCode->id;
				}
				
                if(!$issetPatient->save()) {
                    $this->log[] = 'Невозможно проапдейтить пациента с кодом '.$issetPatient['id'];
                    $this->numErrors++;
                }
            }

            $issetMedcards = TasuMedcard::model()->findAll('patientuid_37756 = :patient_uid AND version_end = :version_end',
                array(
                    ':version_end' => $this->version_end,
                    ':patient_uid' => $patient['uid']
                )
            );
			$sql = "SELECT *
                    FROM
                        PDPStdStorage.dbo.t_book_65067 AS t
                    WHERE
                    [t].patientuid_37756  = ".$patient['uid']."
                    AND [t].version_end = ".$this->version_end;

            $issetMedcards = $conn->createCommand($sql)->queryAll(); 
			
            if(count($issetMedcards) > 0) {
                foreach($issetMedcards as $issetMedcard) {
                    $parts = explode('/', $issetMedcard['number_50713']);
                    if(count($parts) == 2 && array_search($parts[1], array(11, 12, 13, 14)) !== false) {
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
                            $misMedcard->profession = $patient['profession_56032'];
                            $misMedcard->post = $patient['position_61591'];
                            if($tasuDul != null) {
                                $misMedcard->serie = $tasuDul['dulseries_30145'];
                                $misMedcard->docnumber = $tasuDul['dulnumber_50657'];
                                $misMedcard->doctype = 1; // Паспорт
                                $misMedcard->gived_date = $tasuDul['issuedate_42162'];
                            }
                            $misMedcard->policy_id = $issetPatient['id'];
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
            }
        }
		
		unset($conn);
		unset(Yii::app()->db2);
		unset($patients);
        
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
	// Получить данные по полису
	// mode - режим работы, 1 - принудительное обновление, даже если есть данные по региону и страховой
	public function getTasuPatientByPolicy($oms, $mode = 0) {
		// Если серии нет, то нужно брать номер полиса в качестве опоры
       // return true;
		if($oms->region != null && $oms->insurance != null && !$mode) {
			return true;
		}		

        $conn2 = Yii::app()->db2;
		$conn3 = Yii::app()->db3;

		/*if(!$conn2->getActive() || !$conn3->getActive()) {
			return -1; // Нет соединения
		}*/

		// Меняем тактику: если режим работы без тасу, делаем его автономным
		$tasuMode = Setting::model()->find('module_id = -1 AND name = :name', array(':name' => 'tasuMode'));
		if($tasuMode->value == 1) { // Режим неработы с ТАСУ
			return -1;
		}
		if($oms->oms_series == null) {
			$policyParts = explode(' ', trim($oms->oms_number));
			// Неправильный номер полиса по формату....?
			if(count($policyParts) != 2) {
				$policyParts = array();
				if(mb_strlen(trim($oms->oms_number)) > 7) {
					$series = mb_substr(trim($oms->oms_number), 0, 6);
					$number = mb_substr(trim($oms->oms_number), 6);
				} else {
					//throw new Exception();
					return false;
				}
			} else {
				$series = $policyParts[0];
				$number = $policyParts[1];
			}
				
			if($oms->type == 5) {
				$number = $series.$number;
				$series = '';
			}
		} else {
			$series = $oms->oms_series;
			$number = $oms->oms_number;
			
			if($oms->type == 5) {
				$series = '';
				$number = $oms->oms_series.$oms->oms_number;
			} else {
				$series = $oms->oms_series;
				$number = $oms->oms_number;
			}
		}
		  
		// Ищем в местной базе пациентов
		$sql = "SELECT 
				t.*
			FROM
				PDPStdStorage.dbo.t_policy_43176 AS [t]
			WHERE 
			[t].series_14820 = '".$series."'
			AND [t].number_12574 = '".$number."'
			AND [t].version_end = ".$this->version_end;
			
		$tasuOmsRow = $conn2->createCommand($sql)->queryRow();

		// Если пациент не найден в базе местной, то будем искать в усечённом реестре
		if($tasuOmsRow == null) {
			$sql = "SELECT 
				O.*, 
				[oms].[smouid_20464] AS [SMOuid],
				[s].[coderegion_54021] AS [CodeRegion]
			FROM PDPRegStorage.ut.ut_PolReg_UsReg O 
			INNER JOIN PDPStdStorage.dbo.t_smo_09725 AS [oms] 
					ON (O.[Code_msk] = [oms].[code_43940] 
					AND [oms].version_end = ".$this->version_end."
					) 
			INNER JOIN PDPStdStorage.dbo.t_smo_30821 AS [s] ON [oms].[smouid_20464] = [s].[uid]
			WHERE O.ENP = '".$series.$number."'";
			$tasuOmsRow = $conn3->createCommand($sql)->queryRow();
			if($tasuOmsRow == null) {
				return false;
			} else { // Пишем поля региона, статус, дата окончания полиса и прочие штуки
				if($tasuOmsRow['DATE_E'] != null && $tasuOmsRow['DATE_E'] != '') {
					$oms->enddate = $tasuOmsRow['DATE_E']; // Дата окончания действия полиса
                    if(strtotime($tasuOmsRow['DATE_E']) < strtotime(date('Y-m-d'))) {
                        // Активен - 1, погашен - 3.
                        $oms->status = 3;
                    } else {
                        $oms->status = 1;
                    }
				} else {
					$oms->status = 1; // Считаем по умолчанию полис активным
				}
				
				//$oms->type = 1; // Территориальный полис ОМС
				$oms->type = 5; // Единый полис ОМС;
				
				// Вынимаем регион и страхкомпанию
				// Выбираем страховую компанию
				$insurance = Insurance::model()->find('tasu_id = :tasu_id', array(':tasu_id' => $tasuOmsRow['SMOuid']));
				if($insurance != null) {
					$oms->insurance = $insurance->id;
				}
				// Выбираем ID региона
				$regionCode = CladrRegion::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $tasuOmsRow['CodeRegion']));
				if($regionCode != null) {
					$oms->region = $regionCode->id;
				}
				
				// Разделяем поля и дополняем поисковое поле
				$oms->oms_series = $series;
				$oms->oms_number = $number;
				
				$omsNumber = $series.$number;
                $omsNumber = str_replace('-', '', $omsNumber);
                $omsNumber = str_replace(' ', '', $omsNumber);
				
				$oms->oms_series_number = $series.$number;
				
				// Сохраняем все данные
				if(!$oms->save()) {
					throw new Exception();
				}
				return true;
			}
		} else {
			$sql = 'SELECT [t].[coderegion_54021] 
					FROM PDPStdStorage.dbo.t_smo_30821 [t]
					WHERE [t].[uid] = '.$tasuOmsRow['smouid_25976'];
			
			$tasuSmoRow = $conn2->createCommand($sql)->queryRow();
			
			if($tasuSmoRow != null) {
				// Выбираем страховую компанию
				$insurance = Insurance::model()->find('tasu_id = :tasu_id', array(':tasu_id' => $tasuOmsRow['smouid_25976']));
				// Выбираем ID региона
				$regionCode = CladrRegion::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $tasuSmoRow['coderegion_54021']));
			} else {
				$insurance = $regionCode = null;
			}
			
			$oms->oms_number = trim($tasuOmsRow['number_12574']);
			$oms->oms_series = trim($tasuOmsRow['series_14820']);
			$oms->type = $tasuOmsRow['type_07731'];
			$oms->enddate = $tasuOmsRow['voiddate_10849'];
			
			if($tasuOmsRow['state_19333'] != null) {
				$oms->status = $tasuOmsRow['state_19333'];
			} else {
				$oms->status = 1; // Активен по умолчанию
			}
			
			$oms->type = $tasuOmsRow['type_07731'];
			if($insurance != null) {
				$oms->insurance = $insurance->id;
			}
			if($regionCode != null) {
				$oms->region = $regionCode->id;
			}
			
			// Сохраняем все данные
			if(!$oms->save()) {
				throw new Exception();
			}
			
			return true;
		}
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
            $street->name = $address['str00eetname_22113'];
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

//		if($address['addresstype_31280'] == 1) { // Адрес регистрации
        $medcard->address_reg = $addressData;
//		}
        //if($address['addresstype_31280'] == 2) { // Адрес проживания
        $medcard->address = $addressData;
//		}

        $patientController = Yii::app()->createController('reception/patient');
        $addressData = $patientController[0]->getAddressStr($addressData, true);
        //	if($address['addresstype_31280'] == 1) { // Адрес регистрации
        $medcard->address_reg_str = $addressData['addressStr'];
        //	}
        //	if($address['addresstype_31280'] == 2) { // Адрес проживания
        $medcard->address_str = $addressData['addressStr'];
        //	}
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

        if($_GET['totalRows'] == null || $_GET['totalRows'] == 0) {
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

    /* Синхронизация страховых компаний: ТАСУ */
    public function actionSyncInsurances() {
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
        $insurances = TasuInsurance::model()->getRows(false, 'uid', 'asc', $_GET['totalMaked'], $_GET['rowsPerQuery']);

        if($_GET['totalRows'] == null || $_GET['totalRows'] == 0) {
            $this->totalRows = TasuInsurance::model()->getNumRows();
            // Ставим отметку о дате синхронизации
            $syncdateModel = Syncdate::model()->findByPk('insurances');
            if($syncdateModel == null) {
                $syncdateModel = new Syncdate();
            }
            $syncdateModel->name = 'insurances';
            $syncdateModel->syncdate = date('Y-m-d h:i');
            if(!$syncdateModel->save()) {
                $this->log[] = 'Невозможно сохранить временную отметку о синронизации.';
            }
        } else {
            $this->totalRows = $_GET['totalRows'];
        }

        foreach($insurances as $insurance) {
            $this->processed++;

            $issetInsurance = Insurance::model()->find('t.tasu_id = :tasu_id',
                array(
                    ':tasu_id' => $insurance['uid'],
                )
            );

            if($issetInsurance != null) {
				// Надо обновить то, что нашли
				$issetInsurance->code = $insurance['codesmo_46978'];
				if(!$issetInsurance->save()) {
					$this->log[] = 'Невозможно обновить СМО-код для страховой компании '.$insurance['uid'];
                    $this->numErrors++;
				}
                continue;
            }

            try {
                $newInsurance = new Insurance();
                $newInsurance->name = $insurance['shortname_50023'];
                $newInsurance->tasu_id = $insurance['uid'];
				$newInsurance->code = $insurance['codesmo_46978'];
                if(!$newInsurance->save()) {
                    $this->log[] = 'Невозможно импортировать страховую компанию с кодом '.$insurance['uid'];
                    $this->numErrors++;
                } else {
                    $this->numAdded++;
                }

                // Добавляем связку страховая компания-регион
                $newInsuranceRegion = new InsuranceRegion();
                $newInsuranceRegion->insurance_id = $newInsurance->id;
                $region = CladrRegion::model()->find('code_cladr = :code_cladr', array(':code_cladr' => $insurance['coderegion_54021']));
                if($region != null) {
                    $newInsuranceRegion->region_id = $region->id;
                }

                if(!$newInsuranceRegion->save()) {
                    $this->log[] = 'Невозможно импортировать регион для страховой компании '.$insurance['uid'];
                    $this->numErrors++;
                }

            } catch(Exception $e) {
                $this->numErrors++;
            }
        }

        echo CJSON::encode(array(
                'success' => true,
                'data' => array(
                    'log' => $this->log,
                    'successMsg' => 'Успешно импортировано '.($_GET['totalRows'] + $this->processed).' страховых компаний.',
                    'processed' => $this->processed,
                    'totalRows' => $this->totalRows,
                    'numErrors' => $this->numErrors,
                    'numAdded' => $this->numAdded
                ))
        );
    }
	
	public function actionGetFios() {
		if(!isset($_GET['doctor_id']) || !isset($_GET['card_number']) || !isset($_GET['greeting_date']) || !isset($_GET['pr_diagnosis_id'])) {
			echo CJSON::encode(array(
                'success' => false,
                'data' => array(
				)
			));
			exit();
		}
		
		// Проверка даты на то, что она не больше текущей
		if(time() < strtotime($_GET['greeting_date'])) {
			echo CJSON::encode(array(
				'success' => false,
				'errors' => array(
					'greetingDate' => array(
						'Дата добавляемого приёма больше текущей!'
					)
				)
			));
			exit();
		}
			
		// Проверка на существование такого приёма (дубликат)
		$foundFakeGreeting = TasuFakeGreetingsBuffer::model()->find('
			t.card_number = :card_number
		   AND t.doctor_id = :doctor_id
		   AND t.greeting_date = :greeting_date
		', array(
			':card_number' =>$_GET['card_number'],
			':doctor_id' => $_GET['doctor_id'],
			':greeting_date' => $_GET['greeting_date']
		));

		if($foundFakeGreeting != null) {
			echo CJSON::encode(array(
				'success' => false,
				'errors' => array(
					'cardNumber' => array(
						'Такой приём уже внесён в список выгружаемых!'
					)
				)
			));
			exit();
		}

		// Проверка на существование такой медкарты
		$medcard = Medcard::model()->findByPk($_GET['card_number']);
		if($medcard == null) {
			echo CJSON::encode(array(
				'success' => false,
				'errors' => array(
					'cardNumber' => array(
						'Пациент с такой картой не найден!'
					)
				)
			));
			exit();
		} else {
			$oms = Oms::model()->findByPk($medcard->policy_id);
		}
		// Проверка на существование такого врача
		$doctor = Doctor::model()->findByPk($_GET['doctor_id']);
		if($doctor == null) {
			echo CJSON::encode(array(
				'success' => false,
				'errors' => array(
					'doctorId' => array(
						'Такой врач не найден!'
					)
				)
			));
			exit();
		}
		
		// Вынуть код диагноза
		$prDiag = Mkb10::model()->findByPk($_GET['pr_diagnosis_id']);
		if($prDiag == null) {
			echo CJSON::encode(array(
				'success' => false,
				'errors' => array(
					'prDiag' => array(
						'Такой диагноз не найден!'
					)
				)
			));
			exit();
		}
		$prDiagCode = mb_substr($prDiag->description, 0, strpos($prDiag->description, ' '));
		
		echo CJSON::encode(array(
			'success' => true,
			'data' => array(
				'doctorFio' => $doctor->last_name.' '.$doctor->first_name.' '.($doctor->middle_name == null ? '' : $doctor->middle_name),
				'patientFio' => $oms->last_name.' '.$oms->first_name.' '.($oms->middle_name == null ? '' : $oms->middle_name),
				'pr_diagnosis_code' => $prDiagCode
			)
		));
	}
	
	public function actionSendLogFile() {
		if(isset($_GET['bufferid'])) {
			$bufferH = TasuGreetingsBufferHistory::model()->findByPk($_GET['bufferid']);
			if($bufferH == null) {
				echo CJSON::encode(array(
					'success' => false,
					'errors' => array(
						'bufferH' => array(
							'Не найдена запись о выгрузке!'
						)
					)
				));
				exit();
			}
			$filepath = getcwd().$bufferH->log_path;
			header("Pragma: public");
			header('Content-Type: text/plain');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Disposition: attachment; filename="log.txt"');
			$content = file_get_contents($filepath);
			die($content);
		}		
	}
	
	public function actionCancelImport() {
		if(isset($_GET['bufferid'])) {
			$bufferH = TasuGreetingsBufferHistory::model()->findByPk($_GET['bufferid']);
			TasuGreetingsBuffer::model()->updateAll(array('status' => 0), 'import_id = :import_id', array(':import_id' => $bufferH->import_id));
			$bufferH->status = 2; // Статус "отменена"
			$bufferH->save();
		}
		echo CJSON::encode(array(
			'success' => true,
			'data' => array()
		));
	}
}
?>