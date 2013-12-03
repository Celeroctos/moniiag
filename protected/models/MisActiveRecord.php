<?php
class MisActiveRecord extends CActiveRecord {
    /**
     * @param $conn
     * @param $filters
     * @param $multipleFields - массив, раскрывающий значение "сборных" полей в интерфейсе. Например, поле ФИО, являющееся Именем + Фамилией + Отчеством. Формат:
     * array('interfaceField' => array('dbfield1' ... ) ... )
     * @param $aliases - массив в виде array('aliasOfTable' => array(field1, ...) ... ) - алиасы таблиц для поиска. Первое измерение - псевдоним таблицы, второе - поля данной таблицы-алиаса
     * @param $fieldAliases - массив алиасов полей
     */

    protected function getSearchConditions($conn, $filters, $multipleFields, $aliases, $fieldAliases) {
        foreach($filters['rules'] as $index => $filter) {
	        if(trim($filter['data']) == '') { // При пустых входных данных не нужно делать доп. условие
        		continue;
        	}
            if(isset($multipleFields[$filter['field']])) {
                // Условия по всем полям, которые попадают под составное поле
                $opCounter = 0;
                foreach($multipleFields[$filter['field']] as $key => $dbField) {
                    if(isset($fieldAliases[$dbField])) {
                        $dbFieldReal = $fieldAliases[$dbField];
                    } else {
                        $dbFieldReal = $dbField;
                    }

                    if($opCounter == 0) {
                        $groupFilter = $filters['groupOp'];
                        $opCounter++;
                    } else {
                        $groupFilter = 'OR';
                    }

                    $filterByField = array(
                        'field' => $dbFieldReal,
                        'op' => $filter['op'],
                        'data' => $filter['data']
                    );
                    $this->getSearchOperator($conn, $groupFilter, $filterByField, $this->searchAlias($dbField, $aliases));
                }
            } else {
                if(isset($fieldAliases[$filter['field']])) {
                    $dbFieldReal = $fieldAliases[$filter['field']];
                } else {
                    $dbFieldReal = $filter['field'];
                }

                $filterByField = array(
                    'field' => $dbFieldReal,
                    'op' => $filter['op'],
                    'data' => $filter['data']
                );

                $this->getSearchOperator($conn, $filters['groupOp'], $filterByField, $this->searchAlias($filter['field'], $aliases));
            }
        }
    }

    // Поиск алиаса для поля
    protected function searchAlias($field, $aliasesArr) {
        foreach($aliasesArr as $alias => $fields) {
            foreach($fields as $key => $fieldName) {
                if($field == $fieldName) {
                    return $alias;
                }
            }
        }
        exit('Алиас таблицы для поля '.$field.' в списке таблиц не найден!');
    }

    /**
     * @param $conn
     * @param $chainOp - оператор, который будет связывать sql-условия: AND или OR
     * @param $filter
     */
    protected function getSearchOperator($conn, $chainOp, $filter, $alias) {
        $filter['data'] = mb_strtolower($filter['data'], 'UTF-8'); // Прикольно. Он не может без необязательного параметра различить кодировку.
        switch($filter['op']) {
            case 'eq' :
                $chainOp == 'AND' ? $conn->andWhere($alias.'.'.$filter['field'].' = :'.$filter['field'], array(':'.$filter['field'] => $filter['data'])) : $conn->orWhere($alias.'.'.$filter['field'].' = :'.$filter['field'], array(':'.$filter['field'] => $filter['data']));
            break;
            case 'ne' :
                $chainOp == 'AND' ? $conn->andWhere($alias.'.'.$filter['field'].' != :'.$filter['field'], array(':'.$filter['field'] => $filter['data'])) : $conn->orWhere($alias.'.'.$filter['field'].' != :'.$filter['field'], array(':'.$filter['field'] => $filter['data']));
            break;
            case 'lt' :
                $chainOp == 'AND' ? $conn->andWhere($alias.'.'.$filter['field'].' < :'.$filter['field'], array(':'.$filter['field'] => $filter['data'])) : $conn->orWhere($alias.'.'.$filter['field'].' < :'.$filter['field'], array(':'.$filter['field'] => $filter['data']));
            break;
            case 'le' :
                $chainOp == 'AND' ? $conn->andWhere($alias.'.'.$filter['field'].' <= :'.$filter['field'], array(':'.$filter['field'] => $filter['data'])) : $conn->orWhere($alias.'.'.$filter['field'].' <= :'.$filter['field'], array(':'.$filter['field'] => $filter['data']));
            break;
            case 'gt' :
                $chainOp == 'AND' ? $conn->andWhere($alias.'.'.$filter['field'].' > :'.$filter['field'], array(':'.$filter['field'] => $filter['data'])) : $conn->orWhere($alias.'.'.$filter['field'].' > :'.$filter['field'], array(':'.$filter['field'] => $filter['data']));
            break;
            case 'ge' :
                $chainOp == 'AND' ? $conn->andWhere($alias.'.'.$filter['field'].' >= :'.$filter['field'], array(':'.$filter['field'] => $filter['data'])) : $conn->orWhere($alias.'.'.$filter['field'].' >= :'.$filter['field'], array(':'.$filter['field'] => $filter['data']));
            break;
            case 'bw' :
                $chainOp == 'AND' ? $conn->andWhere(array('like', 'LOWER('.$alias.'.'.$filter['field'].')', $filter['data'].'%')) : $conn->orWhere(array('like', 'LOWER('.$alias.'.'.$filter['field'].')', $filter['data'].'%'));
            break;
            case 'bn' :
                $chainOp == 'AND' ? $conn->andWhere(array('not like', 'LOWER('.$alias.'.'.$filter['field'].')', $filter['data'].'%')) : $conn->orWhere(array('not like', 'LOWER('.$alias.'.'.$filter['field'].')', $filter['data'].'%'));
            break;
            case 'in' :
                $chainOp == 'AND' ? $conn->andWhere(array('in', 'LOWER('.$alias.'.'.$filter['field'].')', array($filter['data']))) : $conn->orWhere(array('in', 'LOWER('.$alias.'.'.$filter['field'].')', array($filter['data'])));
            break;
            case 'ni' :
                $chainOp == 'AND' ? $conn->andWhere(array('not in', 'LOWER('.$alias.'.'.$filter['field'].')', array($filter['data']))) : $conn->orWhere(array('not in', 'LOWER('.$alias.'.'.$filter['field'].')', array($filter['data'])));
            break;
            case 'ew' :
                $chainOp == 'AND' ? $conn->andWhere(array('like', 'LOWER('.$alias.'.'.$filter['field'].')', '%'.$filter['data'])) : $conn->orWhere(array('like', 'LOWER('.$alias.'.'.$filter['field'].')', '%'.$filter['data']));
            break;
            case 'en' :
                $chainOp == 'AND' ? $conn->andWhere(array('not like', 'LOWER('.$alias.'.'.$filter['field'].')', '%'.$filter['data'])) : $conn->orWhere(array('like', 'LOWER('.$alias.'.'.$filter['field'].')', '%'.$filter['data']));
            break;
            case 'cn' :
                $chainOp == 'AND' ? $conn->andWhere(array('like', 'LOWER('.$alias.'.'.$filter['field'].')', '%'.$filter['data'].'%')) : $conn->orWhere(array('like', 'LOWER('.$alias.'.'.$filter['field'].')', '%'.$filter['data'].'%'));
            break;
            case 'nc' :
                $chainOp == 'AND' ? $conn->andWhere(array('not like', 'LOWER('.$alias.'.'.$filter['field'].')', '%'.$filter['data'].'%')) : $conn->orWhere(array('like', 'LOWER('.$alias.'.'.$filter['field'].')', '%'.$filter['data'].'%'));
            break;
            default:
                exit('Неверный оператор поиска.');
        }
    }
}
?>