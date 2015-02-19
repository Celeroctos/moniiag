<?php

class LDirection extends LModel {

    /**
     * @param string $className
     * @return LDirection - Cached model instance
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return "lis.direction";
    }
} 