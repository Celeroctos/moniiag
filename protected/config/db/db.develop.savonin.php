<?php
/**
 * Конфиг разработчика для создания на него символической ссылки.
 */

return array(
	'class'=>'DbConnection',
	'connectionString' => 'pgsql:host=localhost;port=5432;dbname=postgres;',
	'username' => 'moniiag',
	'password' => '12345',
	'defaultSchema'=>'system', //используется исключительно в консоли.
);
