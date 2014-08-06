<?php
/**
 * EDbConnection class file
 *
 * @author Mayur Ahir <ahirmayur@gmail.com>
 * @link http://www.mayurahir.com
 * @copyright Copyright &copy; 2013 e|antrix Labs.
 * @license GNU GPLv3
 */

/**
 * This class is extension of CDbConnection for optimizing
 * High load servers and passing default/additional config params
 */
class EDbConnection extends CDbConnection {

    /**
     * @var integer which specifies the timeout duration in seconds. 
     * Not all drivers support this option, and it's meaning may differ from driver to driver. 
     * For example, sqlite will wait for up to this time value before giving up on obtaining an writable lock, 
     * but other drivers may interpret this as a connect or a read timeout interval
     * Default value is 300.
     * @see http://php.net/manual/en/pdo.setattribute.php
     */
    public $pdoTimeout = 300;

    /**
     * Initializes the open db connection.
     * This method is invoked right after the db connection is established.
     * The default implementation is to set the charset for MySQL and PostgreSQL database connections.
     * @param PDO $pdo the PDO instance
     */
    protected function initConnection($pdo) {
        parent::initConnection($pdo);
        $pdo->setAttribute(PDO::ATTR_TIMEOUT, $this->pdoTimeout);
    }

}