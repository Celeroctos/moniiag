<?php
/**
 * Поддержка схем PostgreSQL
 * @author Dzhamal Tayibov <prohps@yandex.ru>
 */
class DbConnection extends CDbConnection
{
	public $defaultSchema;
	
	protected function initConnection($pdo)
	{
		parent::initConnection($pdo);
		
		if($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) == 'pgsql')
		{
			$this->driverMap['pgsql']='PgsqlSchema';
			$request=$pdo->prepare("SET search_path to " . $this->defaultSchema);
			$request->execute();
		}
	}
}