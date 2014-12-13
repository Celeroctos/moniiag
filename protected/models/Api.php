<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-08
 * Time: 16:07
 */

class Api extends MisActiveRecord {

    /**
     * @param string $className
     * @return mixed
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string - Table's name
     */
    public function tableName() {
        return 'mis.api';
    }

    /**
     * @param $key string - 20 bytes random SSL key
     * @return array|null - Found api row in table
     */
    public function findByKey($key) {
        try {
            return Yii::app()->db->createCommand()
                ->select("*")
                ->from("mis.api")
                ->where("key = :key", array(
                    ":key" => $key
                ))
                ->queryRow();
        } catch (Exception $e) {
            print json_encode(array(
                'message' => $e->getMessage(),
                'success' => false
            )); die;
        }
        return null;
    }

    /**
     * Register new key in API table
     * @param string $description - API key's description
     * @param string $path - Path to controller's action
     * @return array - Registered row
     */
    public function add($description, $path) {
        try {
            $key = bin2hex(openssl_random_pseudo_bytes(10));
            Yii::app()->db->createCommand()
                ->insert("mis.api", array(
                    "key" => $key,
                    "description" => $description,
                    "path" => $path
                ));
            return $key;
        } catch (Exception $e) {
            print json_encode(array(
                'message' => $e->getMessage(),
                'success' => false
            )); die;
        }
        return null;
    }

    /**
     * Register new key in API table
     * @param string $key - SSL API key
     * @param string $description - Key's description
     * @param string $path - Path to action
     * @return int - Count of updates
     */
    public function update($key, $description, $path) {
        try {
            return Yii::app()->db->createCommand()
                ->update("mis.api", array(
                    "description" => $description,
                    "path" => $path
                ), "key = :key", array(
                    ":key" => $key
                ));
        } catch (Exception $e) {
            print json_encode(array(
                'message' => $e->getMessage(),
                'success' => false
            )); die;
        }
        return 0;
    }

    /**
     * Delete API key from table
     * @param $key string - SSL API key
     */
    public function delete($key) {
        try {
            Yii::app()->db->createCommand()
                ->delete("mis.api", "key = :key", array(
                    ":key" => $key
                ));
        } catch (Exception $e) {
            print json_encode(array(
                'message' => $e->getMessage(),
                'success' => false
            )); die;
        }
    }

    /**
     * Get rows method for jqGrid component
     * @param $filters string - Mis internal filters
     * @param bool $sidx
     * @param bool $sord
     * @param bool $start
     * @param bool $limit
     * @return mixed
     */
    public function getRows($filters, $sidx = false, $sord = false, $start = false, $limit = false) {
        try {
            $items = Yii::app()->db->createCommand()
                ->select('*')
                ->from('mis.api a');
            if($filters !== false) {
                $this->getSearchConditions($items, $filters, array(), array(
                    'a' => array('key', 'description', 'path'),
                ), array());
            }
            if($start !== false && $limit !== false) {
                $items->limit($limit, $start);
            }
            if($sidx !== false && $sord !== false) {
                $items->order($sidx);
            }
            return $items->queryAll();
        } catch (Exception $e) {
            print json_encode(array(
                'message' => $e->getMessage(),
                'success' => false
            )); die;
        }
    }
} 