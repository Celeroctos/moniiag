<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-10
 * Time: 17:27
 */

interface WritableController {

    /**
     * Implement this method and revoke your <code>add</code> method
     * @param $arguments, ... Arguments
     * @return mixed
     */
    public function apiWriteOne($arguments);

    /**
     * Implement this method and revoke your <code>add</code> method
     * @param $arguments, ... Arguments
     * @return mixed
     */
    public function apiWrite($arguments);
} 