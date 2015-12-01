<?php

namespace app\components;

use YII;

class Helpers {
    /**
     * @static Returns file extension
     * @param string $fileName
     * @return string
     */
    public static function getFileExt($fileName) {
   		$matches = array();

   		if(preg_match('!\.([^.]+)$!i', $fileName, $matches))
        {
   			return strtolower($matches[1]);
   		} else {
   			return '';
   		}
   	}

    /**
     * @static
     * @param $name
     * @param null $default
     * @return null
     */
    public static function getParam($name, $default = null) {
        if (isset(Yii::$app->params[$name])) {
            return Yii::$app->params[$name];
        } else {
            return $default;
        }
    }

    /**
     * Gets full url (base url + requested url)
     * @static
     * @param string $urlName
     * @return string
     */
    public static function getUrl($urlName) {
        return self::getParam($urlName, 'null');
    }

    /**
     * Check if url is valid
     * @static
     * @param $url
     * @return int
     */
    public static function checkUrl($url) {
        return preg_match('!^https?://!i', $url);
    }

    /**
     * Check if email is in valid format
     * @static
     * @param $email
     * @return int
     */
    public static function checkEmail($email){
   		return preg_match('/^[a-z0-9][a-z0-9.\-_\+]*@[a-z0-9_.\-]+\.[a-z]{2,7}$/i', $email);
   	}

    /**
     * Check if given number is natural
     * @static
     * @param $num
     * @return int
     */
    public static function checkNaturalNum($num) {
        if (is_array($num)) return false;
   		return preg_match('/^[0-9]+$/', $num);
   	}

    /**
     * Takes certain object fields to return them as an array     *
     * @static
     * @param $obj
     * @param $fields
     * @return array
     */
    public static function fetchObjFields($obj, $fields, $extra = null) {
        $result = !empty($extra['isSingle']) ? [] : null;

        foreach ($fields as $index => $field) {
            if (!is_int($index)) {
                $srcName = $index;
                $dstName = $field;
            } else {
                $srcName = $field;
                $dstName = $field;
            }

            if (empty($extra['isSingle'])) {
                $result[$dstName] = isset($obj->$srcName) ? $obj->$srcName : null;
            } else {
                $result = isset($obj->$srcName) ? $obj->$srcName : null;
            }
        }

        return $result;
    }

    /**
     * Takes certain array fields to return them as an array
     * @static
     * @param $obj
     * @param $fields
     * @return array
     */
    public static function fetchArrayFields($arr, $fields, $extra = null) {
        $result = !empty($extra['isSingle']) ? [] : null;

        foreach ($fields as $index => $field) {
            if (!is_int($index)) {
                $srcName = $index;
                $dstName = $field;
            } else {
                $srcName = $field;
                $dstName = $field;
            }

            if (empty($extra['isSingle'])) {
                $result[$dstName] = isset($arr[$srcName]) ? $arr[$srcName] : null;
            } else {
                $result = isset($arr[$srcName]) ? $arr[$srcName] : null;
            }
        }

        return $result;
    }

    /**
     * Returns set of object or array fields
     *
     * @static
     * @param $objs
     * @param $fields
     * @param null $extra
     * @return array
     */
    public static function fetchFields($objs, $fields, $extra = null) {
        $result = [];

        if (!is_array($fields)) $fields = [$fields];

        foreach ($objs as $obj) {
            $result[] = is_array($obj) ? Helpers::fetchArrayFields($obj, $fields, $extra) : Helpers::fetchObjFields($obj, $fields, $extra);
        }

        return $result;
    }

    /**
     * Gets file name from file path
     *
     * @static
     * @param $filePath
     * @param bool $cutExt
     * @return string
     */
    public static function getFileName($filePath, $cutExt = true) {
        $matches = [];

        if (preg_match('!([^/]+)$!i', $filePath, $matches)) {
            if ($cutExt) {
                $len = strlen($matches[1]);
                $dotPos = strrpos($matches[1], '.');
                if ($dotPos && $len - $dotPos <= 5) $matches[1] = substr($matches[1], 0, $dotPos);
            }

            return strtolower($matches[1]);
        } else {
            return '';
        }
    }

    /**
     * Removes junk characters from file name
     *
     * @static
     * @param $fileName
     * @return mixed|string
     */
    public static function cleanFileName($fileName) {
        $fileName = urldecode($fileName);
        $fileName = preg_replace('/[^.a-z0-9_!@$%^&()+={} \[\]\-]/ui', '', $fileName);
        $fileName = preg_replace('/  +/', ' ', $fileName);
        if(preg_match('/^ +$/i', $fileName)) $fileName = ''; else $fileName = str_replace(' ', '_', $fileName);

        return $fileName ? $fileName : 'none';
    }

    /**
     * Makes dictionary based on array
     * @static
     * @param $data
     * @param string $fieldName
     * @param null $extra
     * @return array|bool
     */
    public static function makeDict($data, $fieldName = 'id', $extra = null) {
        if(!$data || !is_array($data)) return false;

        $result = array();

        foreach($data as $item) {
            if(!empty($item[$fieldName])) {
                $index = !empty($extra['intKeys']) ? intval($item[$fieldName]) : $item[$fieldName];

                if (isset($result[$index])) {
                    if (!empty($extra['multi'])) {
                        $result[$index] = [$result[$index]];
                    } else {
                        break;
                    }
                }

                if(!empty($result[$index]) && is_array($result[$index]) && isset($result[$index][0])) {
                    $result[$index][] = !empty($extra['binary']) ? true : $item;
                } else {
                    $result[$index] = !empty($extra['binary']) ? true : $item;
                }
            }
        }

        return $result;
    }

    /**
     * Random string generator
     *
     * @param int $length string length
     * @param string $dataSet character set
     * @return string $result
     */
    static function randomString($length, $dataSet = '') {
        if(!$dataSet) $dataSet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $result = '';
        $mo = strlen($dataSet);

        for($i = 0; $i < $length; $i++) {
            srand((double)microtime()*1000000);
            $result .= substr($dataSet, rand(0, $mo - 1), 1);
        }

        return $result;
    }

    /**
     * Substantive declension
     *
     * @param int $count
     * @param string $case1 genitive case, single
     * @param string $case2 genitive case, multi
     * @param string $case3 subjective case
     * @return string результат
     */
    static function countCase($count, $case1, $case2, $case3) {
        $len = strlen($count);

        $ld = substr($count, $len - 1);
        if($count > 999) $l2d = substr($count, $len - 2); else $l2d = 0;

        if(($count >= 10 && $count <= 20) || ($l2d >= 10 && $l2d <= 20)) $sign = $case1;
        elseif($ld == 2 || $ld == 3 || $ld == 4) $sign = $case2;
        elseif($ld == 1) $sign = $case3;
        else $sign = $case1;

        return  $sign;
    }

    /**
     * recursively deletes given path
     * @static
     * @param $path
     * @return bool|null
     * @throws Exception
     */
    public static function deleteRecursive($path) {
        if (is_dir($path) && !is_link($path)) {
            $dh = opendir($path);
            if ($dh) {
                while (($sf = readdir($dh)) !== false) {
                    if ($sf == '.' || $sf == '..') continue;

                    if(!self::deleteRecursive($path . '/' . $sf)) throw new Exception('Unable to Delete Folder');
                }

                closedir($dh);
            }

            return rmdir($path);
        } elseif(file_exists($path)) {
            return unlink($path);
        } else {
            return null;
        }
    }

    public static function friendlyErrorType($type) {
        switch($type) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_CORE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_CORE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return "";
    }

    /**
     * Replaces or removes value within given query string
     * @static
     * @param $query
     * @param $values
     * @return string
     */
    public static function injectUriArgs($query, $values) {
        $args = [];

        parse_str($query, $args);

        foreach ($values as $key => $value) {
            if ($values !== null)
                $args[$key] = $value;
            else
                unset($args[$key]);
        }

        $result = http_build_query($args);

        return $result ? '?' . $result : '';
    }

    /**
     * Fetches request parameter with the given name
     * @param string $name
     * @param array $extra
     * @return array|bool|float|int|mixed|null|string
     */
    public static function getRequestParams($type) {
        if (defined('YII_ENV') && YII_ENV == 'dev') {
            return $_REQUEST;
        } elseif ($type == 'post') {
            return Yii::$app->request->post();
        } else {
            return Yii::$app->request->get();
        }
    }

    public static function isConsole() {
        if (empty($_SERVER['argv']) && empty($_SERVER['argc'])) {
            return false;
        } else {
            return true;
        }
    }
}