<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2014-12-10
 * Time: 17:28
 */

interface ReadableController {

    /**
     * Implement this method and revoke your <code>getOne</code> method
     * @param $arguments, ... Arguments
     * @return mixed
     */
    public function apiReadOne($arguments);

    /**
     * Implement this method and revoke <code>get</code> method
     * @param $arguments, ... Arguments
     * @return mixed
     */
    public function apiRead($arguments);
} 