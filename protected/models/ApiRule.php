<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-08
 * Time: 16:07
 */

class ApiRule extends MisActiveRecord {

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
        return 'mis.api_rule';
    }

    /**
     * @param $key string - 20 bytes random SSL key
     * @return array|null - Found api row in table
     */
    public function findByKey($key) {
        try {
            return Yii::app()->db->createCommand()
                ->select("*")
                ->from("mis.api_rule")
                ->where("api_key = :key", array(
                    ":key" => $key
                ))
                ->queryAll();
        } catch (Exception $e) {
            print json_encode(array(
                'message' => $e->getMessage(),
                'success' => false
            )); die;
        }
        return null;
    }

	/**
	 * Add new API rule in database
	 * @param $key string - SSL API key
	 * @param $controller string - Path to controller
	 * @param $writable bool - Is writable
	 * @param $readable - Is readable
	 */
    public function add($key, $controller, $writable, $readable) {
        try {
            Yii::app()->db->createCommand()
                ->insert("mis.api_rule", array(
					"api_key" => $key,
					"controller" => $controller,
					"writable" => $writable,
					"readable" => $readable
                ));
        } catch (Exception $e) {
            print json_encode(array(
                'message' => $e->getMessage(),
                'success' => false,
				'key' => $key
            )); die;
        }
    }

	/**
	 * Update API rule
	 * @param $id int - Rule's identifier
	 * @param $key string - SSL API key
	 * @param $controller string - Path to controller
	 * @param $writable bool - Is writable
	 * @param $readable - Is readable
	 * @return int
	 */
    public function update($id, $key, $controller, $writable, $readable) {
        try {
            return Yii::app()->db->createCommand()
                ->update("mis.api_rule", array(
					"api_key" => $key,
					"controller" => $controller,
					"writable" => $writable,
					"readable" => $readable
                ), "id = :id", array(
                    ":id" => $id
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
	 * @param $id int - Rule's identifier
     */
    public function delete($id) {
        try {
            Yii::app()->db->createCommand()
                ->delete("mis.api_rule", "id = :id", array(
                    ":id" => $id
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
                ->from('mis.api_rule a');
            if($filters !== false) {
                $this->getSearchConditions($items, $filters, array(), array(
                    'a' => array('key', 'description'),
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