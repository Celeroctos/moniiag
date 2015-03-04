<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 2015-03-04
 * Time: 16:31
 */

class LActiveDataProvider extends CActiveDataProvider {

    /**
     * Fetches the data from the persistent data storage.
     * @return array - List of data items
     */
    protected function fetchData() {
        $criteria = clone $this->getCriteria();
        if(($pagination = $this->getPagination()) !== false) {
            $pagination->setItemCount($this->getTotalItemCount());
            $pagination->applyLimit($criteria);
        }
        $baseCriteria = $this->model->getDbCriteria(false);
        if(($sort = $this->getSort()) !== false) {
            if($baseCriteria !== null) {
                $c = clone $baseCriteria;
                $c->mergeWith($criteria);
                $this->model->setDbCriteria($c);
            } else {
                $this->model->setDbCriteria($criteria);
            }
            $sort->applyOrder($criteria);
        }
        $this->model->setDbCriteria($baseCriteria !== null ? clone $baseCriteria : null);
        $data = $this->getModel()->getGridViewData();
        if (isset($data[0]) && !($data[0] instanceof CActiveRecord)) {
            $data = $this->model->populateRecords($data);
        }
        $this->model->setDbCriteria($baseCriteria);
        return $data;
    }

    /**
     * Get model class instance
     * @return LModel - Model class instance
     */
    public function getModel() {
        return $this->model;
    }
} 