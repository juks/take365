<?php

namespace app\components\traits;

use Yii;

trait TModelExtra {
    protected $_oldAttributes;

    /**
    * Saves old attributes
    */
    public function afterFind() {
        $this->_oldAttributes = $this->attributes;

        return parent::afterFind();
    }

    /** Performs select by string array condition
     * @param $columns
     * @param null $condition
     * @param null $order
     * @param null $limit
     * @return mixed
     */
    public static function sqlSelect($columns, $condition = null, $order = null, $limit = null) {
        $st = (new \yii\db\Query())
            ->select($columns)
            ->from(self::tableName())
            ->where(self::makeCondition($condition));

        if ($order) $st->order($order);
        if ($limit) {
            if(!is_array($limit)) $st->limit($limit); else $st->limit($limit[1], $limit[0]);
        }

        return $st->all();
    }

    /** Performs insert
     * @param array|null $fieldValues
     * @return bool|null
     */
    public static function sqlInsert($fieldValues) {
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
                    $value = self::quote($value);
                }
            }

            if($valString) $valString .= ', ';
            $valString .= '(' . implode(', ', array_values($values)) . ')';
        }

        $fields = '`'.implode('`, `', array_keys($values)).'`';

        $sql = "INSERT INTO `" . self::tableName() . "` ($fields) VALUES $valString";

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
    public static function sqlUpdate($condition, $fieldValues, $replace = false, $limit = false) {
        # Дополнительные настройки из условия
        $extra = self::getExtraOptions($fieldValues);

        # Поле = значение
        if(is_array($fieldValues)) {
            $tmp = array();

            foreach($fieldValues as $field=>$value) {
                if(!is_array($value)) {
                    if($value === null) {
                        $newValue = 'NULL';
                    } else {
                        $newValue = self::quote($value);
                    }
                } else {
                    if(isset($value[1])) {
                        $newValue = $field.$value[0].self::quote($value[1]);
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
            $condition = " WHERE ".self::makeCondition($condition);
        }

        if(!$replace) $action = 'UPDATE'; else $action = 'REPLACE';

        if(!empty($extra['lowPriority'])) $action .= ' LOW_PRIORITY';

        if($limit) $limit = ' LIMIT ' . $limit; else $limit = '';

        $sql = $action . ' `' . self::tableName() . '` SET ' . $fields . ' ' . $condition . $limit;

        $command = Yii::$app->db->createCommand($sql);
        $command->execute();
    }

    public static function getExtraOptions(&$values) {
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
    public static function sqlDelete($conditions) {
        $sql = 'DELETE FROM `' . self::tableName() . '` WHERE ' . self::makeCondition($conditions);

        $command = Yii::$app->db->createCommand($sql);
        $command->execute();
    }

    public static function getCount($condition = null, $columnName = '*') {
        return self::sqlGetFuncValue($columnName, $condition, 'count');
    }

    /*
     * Returns max, min, avg or what ever other func value
     */
    public static function sqlGetFuncValue($columnName, $condition = null, $funcName = 'max') {
        $columnString = $columnName == '*' ? '*' : '`' . $columnName . '`';
        $q = 'SELECT ' . $funcName . '(' . $columnString . ') result FROM ' . self::tableName();
        
        if ($condition) $q .= ' WHERE ' . self::makeCondition($condition);
        $row = Yii::$app->db->createCommand($q)->queryOne();

        return $row['result'];
    }

    /*
     * Thanks propeller this can turn arrat into sql condition
     *
     */
    public static function makeCondition($condition, $logicType = 'AND') {
        $result = null;

        if(is_array($condition)) {
            $result = '';
            foreach($condition as $key=>$value) {
                # 'OR' condition
                if($key == 'OR') {
                    if($result) $result .= ' OR ';
                    $result .= '('.self::makeCondition($value, 'OR').')';
                    continue;
                    # 'AND' condition
                } elseif($key == 'AND') {
                    $result .= '('.self::makeCondition($value, 'AND').')';
                    continue;
                } elseif($key == 'MULTI') {
                    if(isset($value['OR'])) { $value = $value['OR']; $logicType = 'OR'; } else { $logicType = 'AND'; }
                    foreach($value as $multiCond) {
                        $result .= ' ' . $logicType . ' ' . self::makeCondition($multiCond, $logicType);
                    }
                    continue;
                } else {
                    if($result) $result.= " $logicType\n\t";
                }
                if(is_array($value)) {
                    switch ($value[0]) {
                        case 'BETWEEN':
                            $cond = 'BETWEEN ' . self::quote($value[1]) . ' AND ' . self::quote($value[2]);
                            break;
                        case 'LIKE':
                            $cond = 'LIKE ' . self::quote($value[1]);
                            break;
                        case 'NOT IN':
                        case 'IN':
                            if(is_array($value[1])) {
                                self::uoteArray($value[1]);
                                $value[1] = implode(', ', $value[1]);
                            }
                            $cond = $value[0] . ' (' . $value[1] . ')';
                            break;
                        default:
                            # >, <, >=, <=, IS NULL, etc
                            $cond = $value[0];
                            if(isset($value[1])) {
                                $cond .= self::quote($value[1]);
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
                        $cond = '= ' . self::quote($value);
                    }
                }
                $result.= '`' . $key . '` ' . $cond;
            }
        } elseif(is_string($condition)) {
            $result = $condition;
        }

        return $result;
    }

    public static function quoteArray(&$array) {
        foreach($array as &$value) {
            $value = self::quote($value);
        }

        return $array;
    }

    /**
     * Quoting a variable or ant array
     * 'asd', 'asf', 'dfgf'
     * @param mixed $value
     * @return string
     */
    public static function quote($value) {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = self::quote($val);
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