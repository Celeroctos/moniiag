<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-08
 * Time: 16:20
 */

class ApiController extends Controller {

    /**
     * Register new API 20 bytes SSL key
     */
    public function actionRegister() {
        if (isset(Yii::app()->user->roleId) && Yii::app()->user->roleId == 1) {
            print json_encode(array(
                "key" => ApiModel::model()->register(),
                "success" => true
            ));
        } else {
            print json_encode(array(
                "message" => "Недостаточно прав",
                "success" => false,
                "role" => Yii::app()->user
            ));
        }
    }

    /**
     * Get row for current API key
     * @param $key string - 20 bytes SLL API key
     */
    public function actionGet($key) {
        print json_encode(array(
            "key" => ApiModel::model()->get($key),
            "success" => true
        ));
    }

    /**
     * Delete API key from database
     * @param $key string - 20 bytes SLL API key
     */
    public function actionDelete($key) {
        ApiModel::model()->delete($key);
        print json_encode(array(
            "success" => true
        ));
    }
} 