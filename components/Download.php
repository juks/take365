<?php

namespace app\components;

/**
 * Downloading data from internet, posting data 
 */

class Download {
	protected $tmpPath = '';
	protected $timeout = 15;
	protected $userAgent = '';
	
	# Константы кодов ошибок
	const invalid 				= 1001;
	const fileSizeFailed 		= 1002;
	const fileSizeExceeded 		= 1003;
	const downloadFailed		= 1004;
	const fileOpenFailed 		= 1005;
	const fileNotFound 			= 1006;
	const noUrl					= 1007;
	const forbitten 			= 1008;
	const badUrl 				= 1009;
	const sendFailed			= 1010;
	
	# Какие случаются ошибки
	private $errorMsg = array(
		self::invalid			=> 'Invalid values',
		self::fileSizeFailed 	=> 'Failed to get file siez',
		self::fileSizeExceeded 	=> 'The downloaded file size exceeds the limit',
		self::fileNotFound		=> 'File not found',
		self::downloadFailed 	=> 'File download failed',
		self::fileOpenFailed 	=> 'Failed to create file',
		self::noUrl 			=> 'No file URL',
		self::forbitten 		=> 'Forbidden',
		self::badUrl			=> 'Invalid recource address',
		self::sendFailed 		=> 'Data IO error'
	);
	
	function __construct($tmpPath = null){
		if($tmpPath) $this->tmpPath = $tmpPath;
		$this->userAgent = 'File Downloader';
	}
	
	/**
	 * Получить файл или документ по протоколу HTTP(S)
	 *
	 * @param string $url 			адрес скачеваемого документа
	 * @param int $maxFileSize 		максимальный размер скачиваемого файла в байтах
	 * @param string $tmpPath 		путь ко временному файлу
	 * @param array $extra 			массив с разными дополнительными пареметрами
	 * @return string				результат: путь к файлу или содержимое полученного документа
	 */
	function get($url, $maxFileSize = null, $tmpPath  = null, $extra = null) {
		if(!$maxFileSize) $maxFileSize = 102400;
		
		if(!empty($extra['timeout'])) $this->timeout = $extra['timeout'];
	
		# Имя сохраняемого файла
		if ($tmpPath != 'memory') {
			if (!$tmpPath) $fileName = $this->tmpPath; else $fileName = $tmpPath;
			if (substr($fileName, -1) != '/') $fileName .= '/';

			$fileName .= self::randomString(20) . '.' . $this->getFileExt($url);
		}
		
		# Проверка url
		if(!$url) throw new \Exception($this->errorMsg[self::noUrl], self::noUrl);
		if(!preg_match('!^https?://!i', $url)) $url = 'http://' . $url;
				
		# Если качаем не в память
		if($fileName != 'memory') {
			# Проверка размера файла
			$fileSize = $this->getFileSize($url);
			
			# Если нет размера файла, то просто поменяем таймаут
			if(!$fileSize) {
				$this->timeout = 7;
			}
			
			if($fileSize > $maxFileSize) throw new \Exception($this->errorMsg[self::fileSizeExceeded], self::fileSizeExceeded);

			if(file_exists($fileName)) unlink($fileName);
			$fileHandler = fopen($fileName, "w");
			if(!$fileHandler) throw new \Exception($this->errorMsg[self::fileOpenFailed] . " ($fileName)", self::fileOpenFailed);
		}

		# Настройка curl
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 				$url);
		if(!empty($extra['timeout'])) curl_setopt($ch, CURLOPT_TIMEOUT, $extra['timeout']); else curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 	true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 		3);
		
		# GET/POST
		if(!empty($extra['post'])) {
			curl_setopt($ch, CURLOPT_POST, 			true);
		} else {
			curl_setopt($ch, CURLOPT_HTTPGET, 		true);
		}
		
		if($fileName != 'memory') {
			curl_setopt($ch, CURLOPT_FILE, $fileHandler);
		} else {
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}
		
		curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
		
		# Загрузка файла
		try {
			$result = curl_exec($ch);
		} catch (Exception $e) {
			if($fileName != 'memory') fclose($fileHandler);
			if(file_exists($fileName)) unlink($fileName);
			
			throw new \Exception($e->getMessage(), $e->getCode());
		}
		
		$info = curl_getinfo($ch);
		
		if($fileName != 'memory') fclose($fileHandler);

        if(curl_errno($ch)) {
        	throw new \Exception($this->errorMsg[self::downloadFailed] . ' (' . curl_error($ch) . ')' , self::downloadFailed);
        } else {
        	if($fileName == 'memory') {
        		$contentType = !empty($info['content_type']) ? $info['content_type'] : null;
        		$matches = array();
        		if(preg_match('/charset=([^ ]+)/i', $contentType, $matches)) $charset = $matches[1]; else $charset = null;
 
        		return [
    						'data'			=> $result,
    						'contentType'	=> $contentType,
    						'charset'		=> strtolower($charset),
    						'code'			=> curl_getinfo($ch, CURLINFO_HTTP_CODE)
        				];
        	} else {
        		return [
        					'filePath' 	 => $fileName,
        					'fileOrigin' => $this->getFileName($url, false)
        				];
        	}
        } 
	}
	
	/**
	 * Получение размера удалённого файла
	 *
	 * @param string $url адрес файла
	 * @return int размер
	 */
	function getFileSize($url, $recursion = 0) {
		if($recursion > 2) return 0;
		
	    $urlP = parse_url($url);
   
	    if(!$urlP) throw new \Exception($this->errorMsg[self::badUrl], self::badUrl);

	    $fileSize = 0;

	    $fp = @fsockopen($urlP["host"], 80, $errNo, $errStr, $this->timeout);

	    if(!$fp) {
	        throw new \Exception($errStr, $errNo);
	    } else {
	        fputs($fp, "HEAD " . $urlP['path'] . " HTTP/1.1\r\n");
	        fputs($fp, "Host: " . $urlP['host'] . "\r\n");
	        fputs($fp, "User-Agent: " . $this->userAgent . "\r\n");
	        fputs($fp, "Connection: close\r\n\r\n");
	        stream_set_timeout($fp, $this->timeout);
       
	        $headers = fread($fp, 512);

	        if($headers) {
	        	$matches = array();
	        	if(!preg_match('!^HTTP/1\.[01] ([0-9]{3})!i', $headers, $matches)) {
	        		throw new \Exception($this->errorMsg[self::fileSizeFailed], self::fileSizeFailed);
	        	}
      	
	        	# Получение кода ответа
	        	$code = intval($matches[1]);

	        	# Анализ кода ответа
	        	# Ok
	        	if($code == 200) {
	        		if(preg_match('!Content-Length: ?([0-9]+)!i', $headers, $matches))
        				$fileSize = $matches[1];
        		# Редирект
	        	} elseif ($code == 301) {
	        		if(preg_match('!location: ?([^\s]+)!is', $headers, $matches))
        				return $this->getFileSize($matches[1], $recursion + 1);
        		} elseif ($code == 404) {
        			throw new \Exception($this->errorMsg[self::fileNotFound], self::fileNotFound);
        		} elseif ($code == 403) {
        			throw new \Exception($this->errorMsg[self::forbitten], self::forbitten);
        		} else {
        			throw new \Exception($this->errorMsg[self::fileSizeFailed], self::fileSizeFailed);
        		}
	        }
	    }
	    
	    fclose ($fp);
	    
	   	return $fileSize;
	}
	
	/**
	 * Get filename
	 *
	 * @param string $filePath путь к файлу
	 * @return string результат
	 */
	public function getFileName($filePath) {
		$matches = [];
		
		if(preg_match('!([^/]+)$!i', $filePath, $matches)) {
			return $matches[1];
		} else {
			return '';
		}
	}

	/**
	 * Get file extension
	 *
	 * @param string $filePath путь к файлу
	 * @return string результат
	 */
    public function getFileExt($fileName) {
   		$matches = array();

   		if(preg_match('!\.([^.]+)$!i', $fileName, $matches))
        {
   			return strtolower($matches[1]);
   		} else {
   			return '';
   		}
   	}
	
	/**
	 * Отправка данных методом POST и получение ответа
	 * 
	 * @param string $url адрес, куда обращаться
	 * @param string $data отправляемые данные
	 * @param array $extra дополнительные параметры
	 */
	function sendData($url, $data, $extra = null) {
		# Настройка curl
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 	true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 		2);
		
		if(!empty($extra['timeout'])) curl_setopt($ch, CURLOPT_TIMEOUT, $extra['timeout']); else curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		
		if(empty($extra['get'])) {
			curl_setopt($ch, CURLOPT_POST, 				true);
			if(is_array($data)) {
				if (!empty($extra['postJson'])) {
					$data = json_encode($data);
					curl_setopt($ch, CURLOPT_HTTPHEADER, [
							'Content-Type: application/json',
							'Content-Length: ' . strlen($data)]
					);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				} else {
					curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				}
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}
		} else {
			curl_setopt($ch, CURLOPT_POST, 				false);
			
			if(!empty($data)) {
				if(is_array($data)) {
					$url .= '?' . http_build_query($data);
				} else {
					$url .= '?' . $data;
				}
			}
		}
		
		curl_setopt($ch, CURLOPT_URL, 				$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	true);
		curl_setopt($ch, CURLOPT_USERAGENT, 		$this->userAgent);
		
		# Авторизация
		if(!empty($extra['username']) && !empty($extra['password'])) {
			curl_setopt($ch, CURLOPT_USERPWD, 	$extra['username'] . ':' . $extra['password']);
		}

		if(!empty($extra['disableSSLVerification'])) {
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}

		# Установка заголовков
		if(!empty($extra['headers'])) {
			if(!empty($extra['headers'][0])) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, 	$extra['headers']);
			} else {
				$headers = array();
				foreach($extra['headers'] as $header => $value) {
					$headers[] = $header . ': ' . $value;
				}
				
				curl_setopt($ch, CURLOPT_HTTPHEADER, 	$headers);
			}
		# Автоматическое определение content-type для XML
		} elseif(!is_array($data) && preg_match('/^\s*<\?xml/', $data)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, 	array('Content-type: text/xml'));
		}
		
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		
		if(!empty($extra['isJson'])) {
			$result = json_decode($result);
		} elseif (!empty($extra['isJsonA'])) {
			$result = json_decode($result, true);
		}

        if(curl_errno($ch)) {
        	throw new \Exception($this->errorMsg[self::sendFailed] . ' (' . curl_error($ch) . ')' , self::sendFailed);
		} else {
    		$contentType = !empty($info['content_type']) ? $info['content_type'] : null;
    		
    		$matches = array();
    		if(preg_match('/charset=([^ ]+)/i', $contentType, $matches)) $charset = $matches[1]; else $charset = null;

    		return array(	
    						'data' 			=> $result,
    						'contentType' 	=> $contentType,
    						'charset' 		=> strtolower($charset),
    						'code' 			=> curl_getinfo($ch, CURLINFO_HTTP_CODE)
    					);
        }
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
}

?>