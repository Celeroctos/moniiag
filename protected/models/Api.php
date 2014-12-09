<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-08
 * Time: 16:07
 */

class Api extends CModel {

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
    public function get($key) {
        try {
            return Yii::app()->db->createCommand()
                ->select("*")
                ->from("mis.api")
                ->where("key = :key")
                ->queryRow(array(
                    ":key" => $key
                ));
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
    public function register() {
        try {
            $key = openssl_random_pseudo_bytes(20);
            Yii::app()->db->createCommand()
                ->insert("mis.api", array(
                    "key" => $key
                ));
            return $this->get($key);
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