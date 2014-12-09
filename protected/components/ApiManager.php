<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-08
 * Time: 16:03
 */

class ApiManager extends CComponent {

    /**
     * @var array - Array with all allowed for external API paths
     */
    private static $allowed = array(
        "users"
    );

    /**
     * Test path
     * @param $path string - Full url manager's path
     * @return bool - True if path can be used as API
     */
    public static function isAllowed($path) {
        foreach (self::$allowed as $i => $p) {
            if (strstr($path, $p) !== false) {
                return true;
            }
        }
        return false;
    }
} 