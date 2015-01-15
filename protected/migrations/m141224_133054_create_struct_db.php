<?php
/**
 * Миграция на создание структуры БД всего проекта (Без данных).
 * *Схема public
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */

class m141224_133054_create_struct_db extends CDbMigration
{
	public function up()
	{
		$connection=Yii::app()->db;
		$transaction=$connection->beginTransaction();
		
		$sql_access_actions=<<<HERE
			CREATE TABLE IF NOT EXISTS access_actions
			(
			  id serial NOT NULL,
			  name character varying(100) DEFAULT NULL::character varying,
			  "group" integer,
			  "accessKey" character varying(100) DEFAULT NULL::character varying,
			  CONSTRAINT access_actions_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);	
HERE;
		
		$sql_access_actions_groups=<<<HERE
			CREATE TABLE IF NOT EXISTS access_actions_groups
			(
			  id serial NOT NULL,
			  name character varying(100), -- Название группы
			  CONSTRAINT access_actions_groups_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		
		$sql_cabinet_types=<<<HERE
			CREATE TABLE IF NOT EXISTS cabinet_types
			(
			  id integer NOT NULL,
			  name character varying(100), -- Название типа кабинета
			  CONSTRAINT cabinet_types_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		
		$sql_cabinets=<<<HERE
			CREATE TABLE IF NOT EXISTS cabinets
			(
			  id serial NOT NULL,
			  enterprise_id integer, -- Тип кабинета
			  ward_id integer, -- Отделение
			  cab_number character varying(10), -- Номер кабинета
			  description character varying(200), -- Наименование кабинета
			  CONSTRAINT cabinets_pkey PRIMARY KEY (id),
			  CONSTRAINT cabinets_enterprise_id_fkey FOREIGN KEY (enterprise_id)
			      REFERENCES enterprise_params (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION,
			  CONSTRAINT cabinets_ward_id_fkey FOREIGN KEY (ward_id)
			      REFERENCES wards (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);
HERE;
		
		$sql_cancelled_greetings=<<<HERE
			CREATE TABLE IF NOT EXISTS cancelled_greetings
			(
			  id serial NOT NULL,
			  doctor_id integer, -- Доктор
			  medcard_id character varying(50), -- Медкарта
			  patient_day date, -- Дата приёма
			  patient_time time without time zone, -- Время приёма
			  mediate_id integer, -- ID опосредованного пациента (если есть. В противном случае - NULL)
			  shedule_id integer, -- ID элемента расписания
			  greeting_type integer, -- Тип приёма (первичный-вторичный)
			  order_number integer,
			  policy_id integer, -- ИД полиса для упрощения запросов при выводе данных из это таблицы
			  deleted integer DEFAULT 0, -- Удалена ли запись. Сделано для того, чтобы можно было вытащить данные из таблицы после удаления приёма
			  comment text, -- Комментарий при записи
			  CONSTRAINT cancelled_greetings_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);
HERE;
		
		$sql_cladr_districts=<<<HERE
			CREATE TABLE IF NOT EXISTS cladr_districts
			(
			  id serial NOT NULL,
			  code_cladr character varying(32), -- Код в КЛАДР
			  code_region character varying(32), -- Код региона
			  name character varying(100), -- Название региона
			  fake_cladr integer DEFAULT 0,
			  CONSTRAINT cladr_regions_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);
HERE;

		$sql_cladr_regions=<<<HERE
			CREATE TABLE IF NOT EXISTS cladr_regions
			(
			  id serial NOT NULL,
			  code_cladr character varying(32), -- Код в КЛАДР
			  name character varying(100), -- Название
			  fake_cladr integer DEFAULT 0,
			  CONSTRAINT cladr_regions_pkey1 PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);
HERE;
		$sql_cladr_settlements=<<<HERE
			CREATE TABLE IF NOT EXISTS cladr_settlements
			(
			  id serial NOT NULL,
			  code_cladr character varying(50), -- Код в КЛАДР
			  name character varying(150), -- Название
			  code_region character varying(50), -- Код региона
			  code_district character varying(50), -- Код района
			  fake_cladr integer DEFAULT 0,
			  CONSTRAINT cladr_settlements_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);
HERE;

		$sql_cladr_streets=<<<HERE
			CREATE TABLE IF NOT EXISTS cladr_streets
			(
			  id serial NOT NULL,
			  code_cladr character varying(50), -- Код в КЛАДР
			  name character varying(150), -- Название улицы
			  code_region character varying(50), -- Код региона
			  code_district character varying(50), -- Код района
			  code_settlement character varying(50), -- Код населённого пункта
			  fake_cladr integer DEFAULT 0,
			  CONSTRAINT cladr_streets_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);
HERE;
		$sql_clinical_diagnosis=<<<HERE
			CREATE TABLE IF NOT EXISTS clinical_diagnosis
			(
			  id serial NOT NULL,
			  description character varying(512),
			  is_deleted integer,
			  CONSTRAINT clinical_diagnosis_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_clinical_diagnosis_per_patient=<<<HERE
			CREATE TABLE IF NOT EXISTS clinical_diagnosis_per_patient
			(
			  diagnosis_id integer,
			  greeting_id integer,
			  type integer
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_comments_oms=<<<HERE
			CREATE TABLE IF NOT EXISTS comments_oms
			(
			  id serial NOT NULL, -- Первичка
			  comment text, -- Сам текст комментария
			  id_oms integer, -- Ссылка на ОМС
			  create_date timestamp without time zone, -- Дата и время, когда комментарий был сделан
			  employer_id integer -- ИД работника, который сделал данный коммент
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_contacts=<<<HERE
			CREATE TABLE IF NOT EXISTS contacts
			(
			  id serial NOT NULL,
			  type integer, -- Тип контакта
			  contact_value character varying(200), -- Описание самого контакта
			  employee_id integer, -- Ключ на сотрудника
			  CONSTRAINT contacts_pkey PRIMARY KEY (id),
			  CONSTRAINT contacts_employee_id_fkey FOREIGN KEY (employee_id)
			      REFERENCES doctors (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_degrees=<<<HERE
			CREATE TABLE degrees
			(
			  id serial NOT NULL,
			  name character varying(150), -- Название степени
			  CONSTRAINT degrees_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_diagnosis_per_patient=<<<HERE
			CREATE TABLE diagnosis_per_patient
			(
			  mkb10_id integer,
			  greeting_id integer,
			  type integer -- Тип (0 - первичный, 1 - добавочный)
			)
			WITH (
			  OIDS=FALSE
			);
HERE;
		$sql_doctor_cabinet=<<<HERE
			CREATE TABLE  doctor_cabinet
			(
			  doctor_id integer NOT NULL, -- ID доктора
			  cabinet_id integer NOT NULL, -- ID кабинета
			  id integer NOT NULL DEFAULT nextval('"doctor-cabinet_id_seq"'::regclass),
			  CONSTRAINT "doctor-cabinet_pkey" PRIMARY KEY (id),
			  CONSTRAINT "doctor-cabinet_cabinet_id_fkey" FOREIGN KEY (cabinet_id)
			      REFERENCES cabinets (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION,
			  CONSTRAINT "doctor-cabinet_doctor_id_fkey" FOREIGN KEY (doctor_id)
			      REFERENCES doctors (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_doctor_shedule_by_day=<<<HERE
			CREATE TABLE doctor_shedule_by_day
			(
			  id serial NOT NULL,
			  doctor_id integer, -- Доктор
			  medcard_id character varying(50), -- Медкарта
			  patient_day date, -- Дата приёма
			  is_accepted integer, -- Принят или нет
			  patient_time time without time zone, -- Время приёма
			  is_beginned integer, -- Начат приём пациента или нет
			  time_begin time without time zone, -- Время начала приёма
			  time_end time without time zone, -- Время конца приёма
			  note text, -- Примечание к диагнозам (поставленным)
			  mediate_id integer, -- ID опосредованного пациента (если есть. В противном случае - NULL)
			  shedule_id integer, -- ID элемента расписания
			  comment text, -- Комментарий к приёму
			  greeting_type integer, -- Тип приёма (первичный-вторичный)
			  greeting_status integer DEFAULT 0, -- Статус приёма (да/нет)
			  order_number integer,
			  CONSTRAINT doctor_shedule_by_day_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_doctor_shedule_setted=<<<HERE
			CREATE TABLE doctor_shedule_setted
			(
			  id serial NOT NULL,
			  cabinet_id integer, -- ID кабинета
			  employee_id integer, -- ID сотрудника
			  weekday integer, -- ID дня (от 0 до 6, от Пн до Вс)
			  time_begin time without time zone, -- Время начала приёма
			  time_end time without time zone, -- Время конца приёма
			  type integer, -- Тип элемента расписания: 0 - расписание общее, 1 - день-исключение
			  day date, -- День (дата) для дня-исключения
			  date_id integer, -- Внешний ключ на таблицу того, каковы сроки расписания по датам
			  CONSTRAINT doctors_shedule_setted_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_doctor_shedule_setted_be=<<<HERE
			CREATE TABLE doctor_shedule_setted_be
			(
			  id serial NOT NULL,
			  date_begin date, -- Дата начала действия раписания
			  date_end date, -- Дата конца действия раписания
			  employee_id integer, -- ID сотрудника
			  CONSTRAINT doctor_shedule_setted_be_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_doctors=<<<HERE
			CREATE TABLE doctors
			(
			  id serial NOT NULL,
			  first_name character varying(50), -- Имя
			  middle_name character varying(50), -- Фамилия
			  last_name character varying(50), -- Отчество
			  post_id integer, -- Должность (ссылка на таблицу должностей)
			  tabel_number character varying(50), -- Табельный номер
			  degree_id integer, -- Степень
			  titul_id integer, -- Звание
			  date_begin date, -- Дата начала действия
			  date_end date, -- Дата конца действия
			  ward_code integer, -- Код отделения
			  tasu_id integer, -- ID врача в ТАСУ
			  greeting_type integer DEFAULT 0, -- Тип приёма (0 - любой, 1 - первичный, 2 - вторичный)
			  display_in_callcenter integer DEFAULT 1, -- Отображать в call-центре или нет
			  categorie integer, -- -- Категория врача
			  user_id integer, -- ID пользователя, к которому привязан сотрудник
			  CONSTRAINT doctors_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_doctors_timetables=<<<HERE
			CREATE TABLE doctors_timetables
			(
			  id serial NOT NULL, -- Первичка таблицы
			  id_doctor integer, -- Ссылка на врача
			  id_timetable integer -- Ссылка на расписание
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_doctypes=<<<HERE
			CREATE TABLE doctypes
			(
			  id serial NOT NULL,
			  name character varying(100), -- Название типа документов
			  CONSTRAINT doctypes_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_enterprise_params=<<<HERE
			CREATE TABLE enterprise_params
			(
			  address_fact character varying(200), -- Адрес фактический
			  address_jur character varying(200), -- Адрес юридический
			  phone character varying(50), -- Телефон
			  shortname character varying(80), -- Краткое название
			  fullname character varying(150), -- Полное название
			  bank character varying(50), -- Банк
			  bank_account character varying(50), -- Расчётный счёт
			  inn character varying(50), -- ИНН
			  kpp character varying(50), -- КПП
			  id serial NOT NULL,
			  type integer, -- Тип учреждения (поликлиника, больница и прочее)
			  ogrn character varying(30), -- ОГРН
			  CONSTRAINT enterprise_params_pkey PRIMARY KEY (id),
			  CONSTRAINT enterprise_params_type_fkey FOREIGN KEY (type)
			      REFERENCES enterprise_types (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_enterprise_types=<<<HERE
			CREATE TABLE enterprise_types
			(
			  id serial NOT NULL,
			  name character varying(100) NOT NULL, -- Название типа
			  CONSTRAINT enterprise_types_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_files=<<<HERE
			CREATE TABLE files
			(
			  id serial NOT NULL,
			  filename character varying(255), -- Имя файла (созданное)
			  path text, -- Путь до файла
			  owner_id integer, -- Кто загрузил
			  type integer, -- Тип файла (0 - аватар, 1 - файл для ТАСУ)
			  CONSTRAINT files_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_insurances=<<<HERE
			CREATE TABLE insurances
			(
			  id serial NOT NULL,
			  name character varying(150), -- Название компании
			  tasu_id integer, -- ID в ТАСУ (страховой компании)
			  code character varying(20), -- Код СМО
			  CONSTRAINT insurances_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_insurances_regions=<<<HERE
			CREATE TABLE insurances_regions
			(
			  id serial NOT NULL, -- Первичка
			  insurance_id integer, -- Ссылка на страховую компанию
			  region_id integer -- Ссылка на регион
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_logs=<<<HERE
			CREATE TABLE logs
			(
			  id serial NOT NULL,
			  user_id integer, -- ID пользователя
			  url text, -- Текст запроса
			  changedate date, -- Дата действия
			  changetime time without time zone, -- Время действия
			  CONSTRAINT logs_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_categories=<<<HERE
			CREATE TABLE medcard_categories
			(
			  id serial NOT NULL,
			  name character varying(150), -- Название категории
			  parent_id integer, -- Категория-родитель
			  "position" integer, -- Позиция в других категориях (среди категорий и элементов)
			  is_dynamic integer, -- Возможность размножения
			  path character varying(150), -- (Math. path)
			  is_wrapped integer DEFAULT 1, -- Развёрнута / свёрнута (1 / 0)
			  CONSTRAINT medcard_categories_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_comments=<<<HERE
			CREATE TABLE medcard_comments
			(
			  id serial NOT NULL, -- Первичка
			  comment text, -- Сам текст комментария
			  id_medcard integer, -- Ссылка на медкарту
			  create_date timestamp without time zone, -- Дата и время, когда комментарий был сделан
			  employer_id integer -- ИД работника, который сделал данный коммент
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_elements=<<<HERE
			CREATE TABLE medcard_elements
			(
			  id serial NOT NULL,
			  type integer, -- Тип контрола
			  categorie_id integer, -- Тип категории
			  label character varying(150), -- Метка для контрола до поля
			  guide_id integer, -- Справочник
			  allow_add integer DEFAULT 0, -- Можно ли добавлять новые значения или нет (комбо)
			  label_after character varying(200), -- Метка после поля
			  size integer, -- Размер поля
			  is_wrapped integer, -- Перенос строки (да/нет)
			  path character varying(150), -- (Путь math. path)
			  "position" integer, -- Позиция элемента в категории (приоритет)
			  config text, -- Конфигурация элемента. Используется, например, в таблицах
			  default_value character varying(300), -- Значение по умолчанию (используется для выпадающих списков)
			  label_display character varying(150), -- Метка для администратора (отображение)
			  is_required integer,
			  not_printing_values text, -- Значения справочников элементов, при выборе которых элемент не выводится на печать
			  hide_label_before integer, -- Скрывать ли метку до на печати
			  CONSTRAINT medcard_elements_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_elements_dependences=<<<HERE
			CREATE TABLE medcard_elements_dependences
			(
			  id serial NOT NULL,
			  element_id integer, -- ID элемента
			  value_id integer, -- ID значения элемента
			  dep_element_id integer, -- ID зависимого элемента
			  action integer, -- Действие (0 - показать, 1 - скрыть)
			  CONSTRAINT medcard_elements_dependences_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_elements_patient=<<<HERE
			CREATE TABLE medcard_elements_patient
			(
			  medcard_id character varying(50) NOT NULL, -- ID медкарты
			  element_id integer NOT NULL, -- ID элемента для него
			  value text, -- Значение элемента
			  history_id integer NOT NULL, -- ID для истории медкарты
			  change_date timestamp without time zone, -- Дата изменения
			  greeting_id integer NOT NULL, -- ID приёма, во время которого было изменено поле
			  categorie_name character varying(200), -- Название категории (история)
			  path character varying(100) NOT NULL, -- Путь (mathr. path)
			  label_before character varying(150), -- Метка для контрола до поля
			  label_after character varying(150), -- Метка после контрола
			  size integer, -- Размер поля
			  is_wrapped integer, -- Перенос строки (да/нет)
			  categorie_id integer NOT NULL, -- Ключ категории (если есть)
			  type serial NOT NULL, -- Тип контрола (-1, если категория)
			  template_id integer, -- ID шаблона
			  template_name character varying(150), -- Название шаблона
			  guide_id integer, -- ID справочника (комбо)
			  is_dynamic integer, -- Динамичность элемента
			  real_categorie_id integer, -- Реальный ID категории
			  allow_add integer, -- Можно ли добавлять новые значения или нет
			  config text, -- Запомненная конфигурация элемента
			  is_required integer,
			  is_record integer DEFAULT 0,
			  record_id integer,
			  template_page_id integer,
			  not_printing_values text,
			  hide_label_before integer, -- Скрывать ли метку до на печати
			  CONSTRAINT medcard_elements_patient_pkey PRIMARY KEY (medcard_id, history_id, path, greeting_id, categorie_id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_elements_patient_dependences=<<<HERE
			CREATE TABLE medcard_elements_patient_dependences
			(
			  element_path character varying(150), -- Путь элемента, от которого зависят
			  dep_element_path character varying(150), -- Пусть элемента, который зависит
			  action integer, -- Действие
			  medcard_id character varying(50), -- ID медкарты
			  greeting_id integer, -- ID приёма
			  value text, -- Значение, при котором срабатывает зависимость
			  dep_element_id integer, -- ID зависимого элемента
			  element_id integer -- ID элемента-инициатора
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_guide_values=<<<HERE
			CREATE TABLE medcard_guide_values
			(
			  id serial NOT NULL,
			  guide_id integer, -- ID медсправочника
			  value text, -- Значение
			  greeting_id integer, -- ID приёма (для частного значения справочника)
			  element_path character varying(50), -- Путь элемента (для частного значения справочника)
			  CONSTRAINT medcard_guide_values_pkey PRIMARY KEY (id),
			  CONSTRAINT medcard_guide_values_guide_id_fkey FOREIGN KEY (guide_id)
			      REFERENCES medcard_guides (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE CASCADE
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_guides=<<<HERE
			CREATE TABLE medcard_guides
			(
			  id serial NOT NULL,
			  name name, -- Название
			  CONSTRAINT medcard_guides_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_records=<<<HERE
			CREATE TABLE medcard_records
			(
			  id serial NOT NULL, -- Первичка
			  medcard_id character varying, -- Номер медкарты
			  greeting_id integer, -- Номер приёма
			  record_id integer, -- Номер записи в приёме в карте
			  template_name text, -- Имя шаблона
			  doctor_id integer, -- Ссылка на врача
			  record_date timestamp without time zone, -- Дата сохранения
			  template_id integer, -- ИД шаблона
			  is_empty integer -- Флаг пустоты шаблона. Если 0 - то в шаблоне нет ни одного заполненного поля, 1 - если хотя бы одно поле проставлено в шаблоне
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcard_templates=<<<HERE
			CREATE TABLE medcard_templates
			(
			  id serial NOT NULL,
			  name character varying(150), -- Название
			  page_id integer, -- ID страницы, где используется шаблон
			  categorie_ids text, -- IDS категорий в шаблоне
			  primary_diagnosis integer DEFAULT 0, -- Обязательность заполнения основного диагноза
			  index integer, -- Порядковый номер отображения
			  CONSTRAINT medcard_templates_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcards=<<<HERE
			CREATE TABLE medcards
			(
			  privelege_code integer, -- Код льготы
			  snils character varying(50), -- СНИЛС
			  address character varying(200), -- Адрес проживания фактический
			  address_reg character varying(200), -- Адрес регистрации
			  doctype integer, -- Тип документа
			  serie character varying(20), -- Серия
			  docnumber character varying(20), -- Номер
			  who_gived character varying(200), -- Кем выдан
			  gived_date date, -- Дата выдачи
			  contact character varying(200), -- Контакты
			  invalid_group integer, -- Группа инвалидности
			  card_number character varying(50) NOT NULL, -- Номер карты
			  enterprise_id integer, -- ID заведения
			  policy_id integer, -- Номер полиса
			  reg_date date, -- Дата регистрации карты
			  work_place character varying(100), -- Место работы
			  work_address character varying(100), -- Адрес работы
			  post character varying(100), -- Должность на работе
			  profession character varying(200), -- Профессия
			  motion integer DEFAULT 0, -- Статус движения медкарты
			  address_str text, -- Строковое представление адреса проживания для поиска
			  address_reg_str text, -- Строковое представление адреса регистрации для поиска
			  user_created integer,
			  date_created timestamp without time zone,
			  CONSTRAINT medcards_pkey PRIMARY KEY (card_number),
			  CONSTRAINT medcards_doctype_fkey FOREIGN KEY (doctype)
			      REFERENCES doctypes (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION,
			  CONSTRAINT medcards_enterprise_id_fkey FOREIGN KEY (enterprise_id)
			      REFERENCES enterprise_params (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION,
			  CONSTRAINT medcards_privelege_code_fkey FOREIGN KEY (privelege_code)
			      REFERENCES privileges (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcards_history=<<<HERE
			CREATE TABLE medcards_history
			(
			  id serial NOT NULL,
			  enterprise_id integer, -- ID заведения, у кого обозначена такая карта
			  "from" character varying(50), -- Номер "до"
			  "to" character varying(50), -- Номер "после"
			  policy_id integer, -- ID ОМСа, к которому привязана карта to
			  rule_id integer, -- ID правила, по которому сгенерирован номер
			  reg_date date, -- Дата произведения действия
			  CONSTRAINT medcards_history_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcards_postfixes=<<<HERE
			CREATE TABLE medcards_postfixes
			(
			  id serial NOT NULL,
			  value character varying(50),
			  CONSTRAINT medcards_postfixes_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcards_prefixes=<<<HERE
			CREATE TABLE medcards_prefixes
			(
			  id serial NOT NULL,
			  value character varying(50),
			  CONSTRAINT medcards_prefixes_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcards_rules=<<<HERE
			CREATE TABLE medcards_rules
			(
			  id serial NOT NULL,
			  prefix_id integer, -- ID префикса
			  postfix_id integer, -- ID постфикса
			  value integer, -- Правило формирования номера
			  parent_id integer, -- Унаследован от правила
			  name character varying(250),
			  participle_mode_prefix integer, -- Режим работы с предыдущим префиксом. 0 - замена, 1 - добавление к старым новых
			  participle_mode_postfix integer, -- Режим работы с предыдущим постфиксом. 0 - замена, 1 - добавление к старым новых
			  prefix_separator_id integer, -- ID разделителя префикса
			  postfix_separator_id integer, -- ID разделителя постфикса
			  type integer, -- Тип карты (0 - Амбулаторная, 1 - Стационарная)
			  CONSTRAINT medcards_rules_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medcards_separators=<<<HERE
			CREATE TABLE medcards_separators
			(
			  id serial NOT NULL,
			  value character varying(50), -- Сам разделитель
			  CONSTRAINT medcards_separators_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_mediate_patients=<<<HERE
			CREATE TABLE mediate_patients
			(
			  id serial NOT NULL,
			  first_name character varying(150), -- Имя
			  middle_name character varying(150), -- Отчество
			  last_name character varying(150), -- Фамилия
			  phone character varying(100), -- Контактный телефон
			  CONSTRAINT mediate_patients_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medpersonal=<<<HERE
			CREATE TABLE medpersonal
			(
			  id serial NOT NULL,
			  name character varying(200), -- Наименование типа работника
			  type integer, -- Тип медперсонала
			  is_for_pregnants integer, -- Может обслуживать беременных?
			  payment_type integer, -- Тип оплаты (Омс / бюджет (0 / 1))
			  is_medworker integer DEFAULT 1, -- Медработник или нет
			  CONSTRAINT medpersonal_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medpersonal_templates=<<<HERE
			CREATE TABLE medpersonal_templates
			(
			  id serial NOT NULL,
			  id_medpersonal integer, -- ИД должности
			  id_template integer -- ИД шаблона
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medpersonal_types=<<<HERE
			CREATE TABLE medpersonal_types
			(
			  id serial NOT NULL,
			  name character varying, -- Название типа персонала
			  CONSTRAINT medpersonal_types_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_medservices=<<<HERE
			CREATE TABLE medservices
			(
			  id serial NOT NULL,
			  name text, -- Описание услуги
			  tasu_code character varying(100), -- Код услуги в ТАСУ
			  is_default integer, -- Значение по умолчанию или нет
			  CONSTRAINT medservices_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_menu_pages=<<<HERE
			CREATE TABLE menu_pages
			(
			  id serial NOT NULL,
			  name character varying(150), -- Название страницы
			  url text, -- Адрес страницы
			  priority integer, -- Приоритет страницы
			  CONSTRAINT menu_pages_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_mkb10=<<<HERE
			CREATE TABLE mkb10
			(
			  description character varying(200), -- Описание
			  parent_id integer, -- Родительский элемент
			  level integer, -- Уровень дерева
			  id integer NOT NULL,
			  CONSTRAINT mkb10_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_mkb10_distrib=<<<HERE
			CREATE TABLE mkb10_distrib
			(
			  mkb10_id integer, -- ID диагноза в справочнике МКБ-10
			  employee_id integer -- ID специальности медработника
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_mkb10_likes=<<<HERE
			CREATE TABLE mkb10_likes
			(
			  mkb10_id integer NOT NULL,
			  medworker_id integer NOT NULL,
			  CONSTRAINT mkb10_likes_pkey PRIMARY KEY (mkb10_id, medworker_id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_monitoring_oms=<<<HERE
			CREATE TABLE monitoring_oms
			(
			  id serial NOT NULL, -- Первичка
			  id_patient integer, -- ИД пацента
			  monitoring_type integer -- Тип мониторинга (сахар/давление и пр)
			)
			WITH (
			  OIDS=FALSE
			);		
HERE;
		$sql_monitoring_types=<<<HERE
			CREATE TABLE monitoring_types
			(
			  id serial NOT NULL, -- Первичка
			  name character varying(150) -- Название мониторинга
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_oms=<<<HERE
			CREATE TABLE oms
			(
			  id serial NOT NULL,
			  first_name character varying(100), -- Имя
			  middle_name character varying(100), -- Отчество
			  last_name character varying(100), -- Фамилия
			  oms_number character varying(100), -- Номер полиса
			  gender integer, -- Пол
			  birthday date, -- День рождения
			  type integer, -- Тип (постоянный / временный)
			  givedate date, -- Дата выдачи
			  enddate date, -- Дата кокнчания действия (только для временных)
			  status integer, -- Активен / погашен (0 / 1)
			  tasu_id integer, -- ID записи в ТАСУ
			  insurance integer, -- ИД страховой компании, выдавшей полис
			  region character varying(50),
			  oms_series character varying(20), -- Серия полиса
			  oms_series_number character varying(60), -- Серия+номер ОМС - дефисы - пробелы
			  CONSTRAINT oms_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE,
			  autovacuum_enabled=true
			);			
HERE;
		$sql_oms_statuses=<<<HERE
			CREATE TABLE oms_statuses
			(
			  id serial NOT NULL, -- Первичка
			  tasu_id integer, -- Код в ТАСУ
			  name character varying(128) -- Название статуса
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_oms_types=<<<HERE
			CREATE TABLE oms_types
			(
			  id serial NOT NULL, -- Первичка
			  tasu_id integer, -- Код в ТАСУ
			  name character varying(128) -- Название типа полиса
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_patient_addresses=<<<HERE
			CREATE TABLE patient_addresses
			(
			  id serial NOT NULL,
			  region_id integer, -- ID региона из КЛАДР
			  district_id integer, -- ID района из КЛАДР
			  settlement_id integer, -- ID населённого пункта из КЛАДР
			  street_id integer, -- ID улицы из КЛАДР
			  building_data character varying(200), -- Данные о доме
			  type integer, -- Тип адреса (0 - регистрации, 1 - проживания)
			  medcard_id character varying(15), -- Номер медкарты
			  CONSTRAINT patient_addresses_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_payment_types=<<<HERE
			CREATE TABLE payment_types
			(
			  id serial NOT NULL,
			  name character varying(200), -- Название
			  tasu_string character varying(200), -- Строка для ТАСУ
			  CONSTRAINT payment_types_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);
HERE;
		$sql_phones=<<<HERE
			CREATE TABLE phones
			(
			  medcard_id character varying(50), -- Ссылка на медкарту
			  type integer, -- Тип (0 - домашний, 1 - сотовый, 2 - рабочий)
			  phone character varying(70), -- Телефон
			  id integer NOT NULL,
			  CONSTRAINT phones_pkey PRIMARY KEY (id),
			  CONSTRAINT phones_medcard_id_fkey FOREIGN KEY (medcard_id)
			      REFERENCES medcards (card_number) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_posts=<<<HERE
			CREATE TABLE posts
			(
			  id integer NOT NULL,
			  post_name character varying(150), -- Название должности
			  CONSTRAINT posts_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_pregnants=<<<HERE
			CREATE TABLE pregnants
			(
			  id serial NOT NULL,
			  card_id character varying(50), -- ID медкарты
			  register_type integer, -- Тип учёта (первичный или вторичный)
			  doctor_id integer, -- Врач, наблюдающий пациентку
			  CONSTRAINT pregnants_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_privileges=<<<HERE
			CREATE TABLE privileges
			(
			  id serual NOT NULL,
			  name character varying(150), -- Тип льготы (название)
			  code character varying(50), -- Код льготы
			  CONSTRAINT privileges_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_privileges_per_patient=<<<HERE
			CREATE TABLE privileges_per_patient
			(
			  id serial NOT NULL,
			  patient_id integer, -- ID пациента
			  privilege_id integer, -- ID льготы
			  docname character varying(200), -- Название документа
			  docnumber character varying(100), -- Номер документа
			  docserie character varying(100), -- Серия документа
			  docgivedate date, -- Дата выдачи
			  CONSTRAINT privileges_per_patient_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_quick_panel=<<<HERE
			CREATE TABLE quick_panel
			(
			  id serial NOT NULL,
			  user_id integer, -- Пользователь
			  href text, -- Ссылка с иконки
			  icon character varying(255), -- Файл с иконкой
			  CONSTRAINT quick_panel_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_rebinded_medcards=<<<HERE
			CREATE TABLE rebinded_medcards
			(
			  id serial NOT NULL, -- Первичка
			  card_number character varying(100), -- Номер карты
			  old_policy integer, -- Старый номер полиса
			  new_policy integer, -- Новый номер полиса
			  changing_timestamp timestamp without time zone, -- Дата изменения
			  worker_id integer -- ИД работника, выполнившего действие
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_remote_data=<<<HERE
			CREATE TABLE remote_data
			(
			  id serial NOT NULL, -- ИД записи
			  indicator_value character varying(100),
			  id_patient character varying(150), -- Идентификатор пациента (телефон или ещё что-то)
			  indicator_time timestamp without time zone, -- Время снятия показателя
			  is_read smallint DEFAULT 0, -- Прочитан ли показатель в интерфейс врача
			  id_monitoring integer -- Ссылка на мониторинг
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_role_action=<<<HERE
			CREATE TABLE role_action
			(
			  role_id integer NOT NULL, -- ID роли
			  action_id integer NOT NULL, -- ID экшена
			  employee_id integer NOT NULL, -- Частное правило: id сотрудника
			  mode integer, -- Частное правило: режим правила. 0 - добавить к роли, 1 - исключить из роли
			  CONSTRAINT "role-action_pkey" PRIMARY KEY (role_id, action_id, employee_id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_role_to_user=<<<HERE
			CREATE TABLE role_to_user
			(
			  user_id integer, -- ID пользователя
			  role_id integer -- ID роли
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_roles=<<<HERE
			CREATE TABLE roles
			(
			  id serial NOT NULL,
			  name character varying(150), -- Название роли
			  parent_id integer, -- Родитель
			  startpage_id integer, -- ID страницы, на которую идёт редирект после логина
			  CONSTRAINT roles_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_settings=<<<HERE
			CREATE TABLE settings
			(
			  id serial NOT NULL,
			  module_id integer, -- ID модуля (-1 - без модуля)
			  name character varying(100), -- Название настройки
			  value text, -- Значение настройки
			  CONSTRAINT settings_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_shedule_by_days=<<<HERE
			CREATE TABLE shedule_by_days
			(
			  shedule_global_id integer, -- ID расписания
			  action_date date, -- Дата (формат: год-месяц-день)
			  begin_time time without time zone, -- Время «с»
			  end_time time without time zone, -- Время «по»
			  "doctor-cabinet_id" integer, -- Внешний ключ на таблицу «Врач-кабинеты»)
			  id integer NOT NULL,
			  CONSTRAINT shedule_by_days_pkey PRIMARY KEY (id),
			  CONSTRAINT "shedule_by_days_doctor-cabinet_id_fkey" FOREIGN KEY ("doctor-cabinet_id")
			      REFERENCES doctor_cabinet (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION,
			  CONSTRAINT shedule_by_days_shedule_global_id_fkey FOREIGN KEY (shedule_global_id)
			      REFERENCES shedule_global (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_shedule_global=<<<HERE
			CREATE TABLE shedule_global
			(
			  id integer NOT NULL,
			  doctor_id integer, -- ID доктора
			  date_begin date, -- Дата начала действия
			  date_end date, -- Дата конца действия
			  CONSTRAINT shedule_global_pkey PRIMARY KEY (id),
			  CONSTRAINT shedule_global_doctor_id_fkey FOREIGN KEY (doctor_id)
			      REFERENCES doctors (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_shedule_rest=<<<HERE
			CREATE TABLE shedule_rest
			(
			  day integer NOT NULL, -- День (0 - 6), который считается выходным
			  CONSTRAINT shedule_rest_pkey PRIMARY KEY (day)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_shedule_rest_days=<<<HERE
			CREATE TABLE shedule_rest_days
			(
			  id serial NOT NULL,
			  date timestamp without time zone, -- Дата выходного дня
			  doctor_id integer,
			  type integer
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_shifts=<<<HERE
			CREATE TABLE shifts
			(
			  id serial NOT NULL,
			  time_begin time without time zone, -- Время начала приёма
			  time_end time without time zone, -- Время конца приёма
			  CONSTRAINT shifts_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_syncdates=<<<HERE
			CREATE TABLE syncdates
			(
			  syncdate timestamp without time zone, -- Дата синхронизации
			  name name NOT NULL, -- Ключ для доступа даты синхронизации
			  CONSTRAINT syncdates_pkey PRIMARY KEY (name)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_tasu_fake_greetings=<<<HERE
			CREATE TABLE tasu_fake_greetings
			(
			  id serial NOT NULL,
			  card_number character varying(20), -- Номер карты
			  doctor_id integer, -- ID доктора
			  primary_diagnosis_id integer, -- ID первичного диагноза (из МБК-10)
			  greeting_date date, -- Дата приёма
			  payment_type integer, -- ID типа оплаты
			  service_id integer, -- ID услуги для ТАСУ
			  CONSTRAINT tasu_fake_greetings_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_tasu_fake_greetings_secondary_diag=<<<HERE
			CREATE TABLE tasu_fake_greetings_secondary_diag
			(
			  id serial NOT NULL,
			  buffer_id integer, -- ID буфера выгрузки
			  diagnosis_id integer,
			  CONSTRAINT tasu_fake_greetings_secondary_diag_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_tasu_fields_templates_list=<<<HERE
			CREATE TABLE tasu_fields_templates_list
			(
			  name character varying(200), -- Название
			  template text, -- Сам шаблон
			  id serial NOT NULL,
			  "table" character varying(200), -- Таблица
			  CONSTRAINT tasu_fields_templates_list_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_tasu_greetings_buffer=<<<HERE
			CREATE TABLE tasu_greetings_buffer
			(
			  id serial NOT NULL,
			  greeting_id integer, -- ID приёма
			  import_id integer, -- ID импорта
			  status integer DEFAULT 0, -- Статус приёма: 0 - не выгружен, 1 - выгружен. Выгруженные приёмы можно не показывать.
			  fake_id integer, -- ID приёма "вручную"
			  CONSTRAINT tasu_greetings_buffer_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_tasu_greetings_buffer_history=<<<HERE
			CREATE TABLE tasu_greetings_buffer_history
			(
			  id serial NOT NULL,
			  num_rows integer, -- Кол-во выгруженных строк
			  create_date timestamp without time zone, -- Дата создания выгрузки
			  status integer, -- Статус выгрузки
			  import_id integer, -- ID импорта
			  log_path text, -- Путь до лога
			  CONSTRAINT tasu_greetings_buffer_history_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_tasu_history=<<<HERE
			CREATE TABLE tasu_history
			(
			  id serial NOT NULL,
			  obj_id character varying(50), -- ID объекта в базе
			  "table" character varying(100), -- Таблица в базе, где лежит объект
			  tasu_id character varying(50), -- ID в ТАСУ-файле
			  file_id integer, -- ID в файловой системе МИС
			  CONSTRAINT tasu_history_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_tasu_keys_templates_list=<<<HERE
			CREATE TABLE tasu_keys_templates_list
			(
			  id serial NOT NULL,
			  name character varying(200), -- Название
			  template text, -- Сам шаблон
			  "table" character varying(200), -- Таблица
			  CONSTRAINT tasu_keys_templates_list_pkey PRIMARY KEY (id)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_timetable=<<<HERE
			CREATE TABLE timetable
			(
			  id serial NOT NULL, -- Первичка
			  date_begin date, -- Дата начала действия графика
			  date_end date, -- Дата конца действия графика
			  timetable_rules text -- Правила, которые содержит расписание в формате JSON
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_timetable_facts=<<<HERE
			CREATE TABLE timetable_facts
			(
			  id serial NOT NULL, -- Первичка
			  is_range integer, -- Флаг о том, что нужно указать промежуток, а не день
			  name character varying(150)
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_tituls=<<<HERE
			CREATE TABLE tituls
			(
			  id serial NOT NULL,
			  name character varying(100) -- Название звания
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_users=<<<HERE
			CREATE TABLE users
			(
			  id serial NOT NULL,
			  username character varying(100), -- Отображаемое имя
			  login character varying(100), -- Логин
			  password character varying(100), -- Пароль
			  employee_id integer, -- ID сотрудника, ассоциированного с юзером
			  role_id integer,
			  CONSTRAINT users_pkey PRIMARY KEY (id),
			  CONSTRAINT users_employee_id_fkey FOREIGN KEY (employee_id)
			      REFERENCES doctors (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;
		$sql_wards=<<<HERE
			CREATE TABLE wards
			(
			  id integer NOT NULL DEFAULT nextval('wards_id_seq'::regclass),
			  name character varying(70), -- Название отделения
			  enterprise_id integer, -- Ссылка на предприятие
			  rule_id integer, -- ID правила генерации медкарт
			  CONSTRAINT wards_pkey PRIMARY KEY (id),
			  CONSTRAINT wards_enterprise_id_fkey FOREIGN KEY (enterprise_id)
			      REFERENCES enterprise_params (id) MATCH SIMPLE
			      ON UPDATE NO ACTION ON DELETE NO ACTION
			)
			WITH (
			  OIDS=FALSE
			);			
HERE;

		try
		{ // Выполнение транзакции
			$command=$connection->createCommand($sql_access_actions);
			$command->execute();
			unset($command); // На всякий случай.
			
			$command=$connection->createCommand($sql_access_actions_groups);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_cabinet_types);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_cabinets);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_cancelled_greetings);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_cladr_districts);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_cladr_regions);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_cladr_settlements);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_cladr_streets);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_clinical_diagnosis);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_clinical_diagnosis_per_patient);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_comments_oms);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_contacts);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_degrees);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_diagnosis_per_patient);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_doctor_cabinet);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_doctor_shedule_by_day);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_doctor_shedule_setted);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_doctor_shedule_setted_be);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_doctors);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_doctors_timetables);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_doctypes);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_enterprise_params);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_enterprise_types);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_files);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_insurances);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_insurances_regions);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_logs);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_categories);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_comments);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_elements);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_elements_dependences);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_elements_patient);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_elements_patient_dependences);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_guide_values);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_guides);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_records);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcard_templates);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcards);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcards_history);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcards_postfixes);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcards_prefixes);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcards_rules);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medcards_separators);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_mediate_patients);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medpersonal);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medpersonal_templates);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medpersonal_types);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_medservices);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_menu_pages);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_mkb10);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_mkb10_distrib);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_mkb10_likes);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_monitoring_oms);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_monitoring_types);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_oms);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_oms_statuses);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_oms_types);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_patient_addresses);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_payment_types);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_phones);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_posts);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_pregnants);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_privileges);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_privileges_per_patient);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_quick_panel);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_rebinded_medcards);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_remote_data);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_role_action);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_role_to_user);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_roles);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_settings);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_shedule_by_days);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_shedule_global);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_shedule_rest);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_shedule_rest_days);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_shifts);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_syncdates);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_tasu_fake_greetings);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_tasu_fake_greetings_secondary_diag);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_tasu_fields_templates_list);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_tasu_greetings_buffer);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_tasu_greetings_buffer_history);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_tasu_history);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_tasu_keys_templates_list);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_timetable);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_timetable_facts);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_tituls);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_users);
			$command->execute();
			unset($command);
			
			$command=$connection->createCommand($sql_wards);
			$command->execute();
			unset($command);
			
			$transaction->commit();
			
		}
		catch(Exception $e)
		{
			$transaction->rollback();
		}
	}

	public function down()
	{
	}
}
