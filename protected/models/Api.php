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
     * Find by user's identifier
     * @param $userID int - User's identifier
     * @return array - With row
     */
    public function findByUser($userID) {
        try {
            return Yii::app()->db->createCommand()
                ->select("*")
                ->from("mis.api")
                ->where("user_id = :id", array(
                    ":id" => $userID
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
     * Find by user's identifier
     * @param $login string - User's login
     * @return array - With row
     */
    public function findByUserLogin($login) {
        try {
            return Yii::app()->db->createCommand()
                ->select("*")
                ->from("mis.api a")
                ->join("mis.users u", "a.user_id = u.id")
                ->where("u.login = :login", array(
                    ":login" => $login
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
     * Register new key in API table
     * @return array - Registered row
     */
    public function register($userID) {
        try {
            $key = bin2hex(openssl_random_pseudo_bytes(20));
            Yii::app()->db->createCommand()
                ->insert("mis.api", array(
                    "key" => $key,
                    "user_id" => $userID
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
     * Delete API key from table
     * @param $key string - 20 bytes random SSL key
     */
    public function delete($key) {
        try {
            Yii::app()->db->createCommand()
                ->delete("mis.api", array(
                    "key = :key"
                ), array(
                    ":key" => $key
                ));
        } catch (Exception $e) {
            print json_encode(array(
                'message' => $e->getMessage(),
                'success' => false
            )); die;
        }
    }
} 