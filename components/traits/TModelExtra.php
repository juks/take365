<?php

namespace app\components\traits;

use Yii;

trait TModelExtra {
    /** Performs select by string array condition
     * @param $columns
     * @param null $condition
     * @param null $order
     * @param null $limit
     * @return mixed
     */
    public function sqlSelect($columns, $condition = null, $order = null, $limit = null) {
        $st = Yii::$app->db->createCommand()
            ->select($columns)
            ->from($this->tableName())
            ->where($this->makeCondition($condition));

        if ($order) $st->order($order);
        if ($limit) {
            if(!is_array($limit)) $st->limit($limit); else $st->limit($limit[1], $limit[0]);
        }

        return $st->queryAll();
    }

    /** Performs insert
     * @param array|null $fieldValues
     * @return bool|null
     */
    public function sqlInsert($fieldValues) {
        if(empty($fieldValues) || !count($fieldValues)) return null;

        // Is it multi insert?
        reset($fieldValues);
        $check = current($fieldValues);
        if(is_array($check)) {
            $max = count($fieldValues);
            $multi = true;
        } else {
            $max = 1;
            $multi = false;
        }

        $valString = '';

        for($i=0; $i < $max; $i++) {
            if(!$multi) $values = $fieldValues; else $values = $fieldValues[$i];

            foreach($values as &$value) {
                if($value === null) {
                    $value = 'NULL';
                } else {
                    $value = $this->quote($value);
                }
            }

            if($valString) $valString .= ', ';
            $valString .= '(' . implode(', ', array_values($values)) . ')';
        }

        $fields = '`'.implode('`, `', array_keys($values)).'`';

        $sql = "INSERT INTO `" . $this->tableName() . "` ($fields) VALUES $valString";

        $command = Yii::$app->db->createCommand($sql);
        $command->execute();
    }

    /**
     * Performs sql update
     * @param $condition
     * @param $fieldValues
     * @param bool $replace
     * @param bool $limit
     * @throws Exception
     */
    function sqlUpdate($condition, $fieldValues, $replace = false, $limit = false) {
        # Дополнительные настройки из условия
        $extra = $this->getExtraOptions($fieldValues);

        # Поле = значение
        if(is_array($fieldValues)) {
            $tmp = array();

            foreach($fieldValues as $field=>$value) {
                if(!is_array($value)) {
                    if($value === null) {
                        $newValue = 'NULL';
                    } else {
                        $newValue = $this->quote($value);
                    }
                } else {
                    if(isset($value[1])) {
                        $newValue = $field.$value[0].$this->quote($value[1]);
                    } else {
                        $newValue = $value[0];
                    }
                }
                $tmp[] = '`'.$field.'` = '.$newValue;
            }
            $fields = implode(', ',$tmp);
        } elseif(is_string($fieldValues)) {
            $fields = $fieldValues;
        } else {
            throw new Exception('Invalid parameter');
        }

        # Условие
        if($condition) {
            $condition = " WHERE ".$this->makeCondition($condition);
        }

        if(!$replace) $action = 'UPDATE'; else $action = 'REPLACE';

        if(!empty($extra['lowPriority'])) $action .= ' LOW_PRIORITY';

        if($limit) $limit = ' LIMIT ' . $limit; else $limit = '';

        $sql = $action . ' `' . $this->tableName() . '` SET ' . $fields . ' ' . $condition . $limit;

        $command = Yii::$app->db->createCommand($sql);
        $command->execute();
    }

    function getExtraOptions(&$values) {
        $extra = array();

        if(is_array($values) && !empty($values['default'])) {
            # Low Priority
            if(!empty($values['lowPriority'])) {
                $extra['lowPriority'] = true;
            }

            # Force index
            if(!empty($values['index'])) {
                $extra['index'] = $values['index'];
            }

            $values = $values['default'];
        }

        return $extra;
    }

    /**
     * Performs delete from object table by string or array condition
     * @param $conditions
     * @return bool|void
     */
    public function sqlDelete($conditions) {
        $sql = 'DELETE FROM `' . $this->tableName() . '` WHERE ' . $this->makeCondition($conditions);

        $command = Yii::$app->db->createCommand($sql);
        $command->execute();
    }

    public function getCount($condition, $columnName = '*') {
        return $this->sqlGetFuncValue($columnName, $condition, 'count');
    }

    /*
     * Returns max, min, avg or what ever other func value
     */
    public function sqlGetFuncValue($columnName, $condition = null, $funcName = 'max') {
        $columnString = $columnName == '*' ? '*' : '`' . $columnName . '`';
        $row = Yii::$app->db->createCommand('SELECT ' . $funcName . '(' . $columnString . ') result FROM ' . $this->tableName() . ' WHERE ' . $this->makeCondition($condition))
            ->queryOne();

        return $row['result'];
    }

    /*
     * Thanks propeller this can turn arrat into sql condition
     *
     */
    public function makeCondition($condition, $logicType = 'AND') {
        $result = null;

        if(is_array($condition)) {
            $result = '';
            foreach($condition as $key=>$value) {
                # 'OR' condition
                if($key == 'OR') {
                    if($result) $result .= ' OR ';
                    $result .= '('.$this->makeCondition($value, 'OR').')';
                    continue;
                    # 'AND' condition
                } elseif($key == 'AND') {
                    $result.= '('.$this->makeCondition($value, 'AND').')';
                    continue;
                } elseif($key == 'MULTI') {
                    if(isset($value['OR'])) { $value = $value['OR']; $logicType = 'OR'; } else { $logicType = 'AND'; }
                    foreach($value as $multiCond) {
                        $result .= ' ' . $logicType . ' ' . $this->makeCondition($multiCond, $logicType);
                    }
                    continue;
                } else {
                    if($result) $result.= " $logicType\n\t";
                }
                if(is_array($value)) {
                    switch ($value[0]) {
                        case 'BETWEEN':
                            $cond = 'BETWEEN ' . $this->quote($value[1]) . ' AND ' . $this->quote($value[2]);
                            break;
                        case 'LIKE':
                            $cond = 'LIKE '.$this->quote($value[1]);
                            break;
                        case 'NOT IN':
                        case 'IN':
                            if(is_array($value[1])) {
                                $this->quoteArray($value[1]);
                                $value[1] = implode(', ', $value[1]);
                            }
                            $cond = $value[0].' ('.$value[1].')';
                            break;
                        default:
                            # >, <, >=, <=, IS NULL, etc
                            $cond = $value[0];
                            if(isset($value[1])) {
                                $cond.= $this->quote($value[1]);
                            }
                    }
                } else {
                    if($value === null) {
                        $cond = 'IS NULL';
                    } elseif($value === true) {
                        $cond = '= 1';
                    } elseif($value === false) {
                        $cond = '= 0';
                    } else {
                        $cond = '= '.$this->quote($value);
                    }
                }
                $result.= $key.' '.$cond;
            }
        } elseif(is_string($condition)) {
            $result = $condition;
        }

        return $result;
    }

    public function quoteArray(&$array) {
        foreach($array as &$value) {
            $value = $this->quote($value);
        }

        return $array;
    }

    /**
     * Quoting a variable or ant array
     * 'asd', 'asf', 'dfgf'
     * @param mixed $value
     * @return string
     */
    public function quote($value) {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->quote($val);
            }

            return implode(', ', $value);
        }

        if (is_int($value)) return $value;

        if (is_float($value)) {
            return str_replace(',', '.', (string)$value);
        }

        return Yii::$app->db->quoteValue($value);
    }
}