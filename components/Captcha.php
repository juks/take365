<?php

namespace app\components;

use Yii;
use app\components\Helpers;

/******************************************************************
Projectname:   CAPTCHA class
Version:       2.0
Author:        Pascal Rehfeldt <Pascal@Pascal-Rehfeldt.com>
Last modified: 15. January 2006
 * GNU General Public License (Version 2, June 1991)
 *
 * This program is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
Description:
This class can generate CAPTCHAs, see README for more details!
 ******************************************************************/
class Captcha {
	public $Length;
	public $CaptchaString;
	public $fontpath;
	public $fonts;

	function __construct($length = 6, $blur = 0, $noise = 0, $text = '') {
		$this->Length = $length;
		$this->blur = $blur;
		$this->noise = $noise;

		$this->fontpath = Helpers::tailSlash(Yii::$app->params['fontsPath']);
		$this->fonts = $this->getFonts ();

		if (! $text)
			$this->stringGen ($length); else
			$this->CaptchaString = $text;
	} //captcha
	

	function getFonts() {
		$fonts = array ( );
		$handle = @opendir ( $this->fontpath );

		if ($handle) {
			while ( ($file = readdir ( $handle )) !== FALSE ) {
				$extension = strtolower ( substr ( $file, strlen ( $file ) - 3, 3 ) );
				if ($extension == 'ttf') {
					$fonts [] = $file;
				}
			}
			closedir ( $handle );
		} else {
			return FALSE;
		}

		if (count ( $fonts ) == 0) {
			return FALSE;
		} else {
			return $fonts;
		}
	} //getFonts
	function getRandFont() {
		return $this->fontpath . $this->fonts [mt_rand ( 0, count ( $this->fonts ) - 1 )];
	} //getRandFont
	
	function stringGen($length, $dataSet = '') {
		if(!$dataSet) $dataSet = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
		$result = '';
		$mo = strlen($dataSet);
		
		for($i = 0; $i < $length; $i++) {
			srand((double)microtime()*1000000); 
			$result .= substr($dataSet, rand(0, $mo - 1), 1);
		}
		
		$this->CaptchaString = $result;
	}//StringGen

	function makeCaptcha() {
		Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'image/png');
		
		$imagelength = $this->Length * 25 + 16;
		$imageheight = 75;
		$image = imagecreate ( $imagelength, $imageheight );
		imagecolorallocate ( $image, 255, 255, 255 );
		$stringcolor = imagecolorallocate ( $image, 0, 0, 0 );
		$this->signs ( $image, $this->getRandFont () );

		for($i = 0; $i < strlen ( $this->CaptchaString ); $i ++) {
			imagettftext ( $image, 25, mt_rand ( - 10, 10 ), $i * 25 + 10, mt_rand ( 40, 60 ), $stringcolor, $this->getRandFont (), $this->CaptchaString {$i} );
		}
		
		if ($this->blur)
			$this->blur ( $image, $this->blur );
		if ($this->noise)
			$this->noise ( $image, $this->noise );
		
		ob_start();
		imagepng($image);
		$imageData = ob_get_contents();
		ob_end_clean();
		\Yii::$app->response->data = $imageData;
		imagedestroy ( $image );
	} //MakeCaptcha
	
	function getCaptchaString() {
		return $this->CaptchaString;
	} //GetCaptchaString
	

	function noise(&$image, $runs = 30) {
		
		$w = imagesx ( $image );
		$h = imagesy ( $image );
		
		for($n = 0; $n < $runs; $n ++) {
			for($i = 1; $i <= $h; $i ++) {
				$randcolor = imagecolorallocate ( $image, mt_rand ( 0, 255 ), mt_rand ( 0, 255 ), mt_rand ( 0, 255 ) );
				imagesetpixel ( $image, mt_rand ( 1, $w ), mt_rand ( 1, $h ), $randcolor );
			}
		}
	
	} //noise
	

	function signs(&$image, $font, $cells = 4) {
		$w = imagesx ( $image );
		$h = imagesy ( $image );
		for($i = 0; $i < $cells; $i ++) {
			
			$centerX = mt_rand ( 1, $w );
			$centerY = mt_rand ( 1, $h );
			$amount = mt_rand ( 15, 30 );
			$stringcolor = imagecolorallocate ( $image, 205, 205, 205 );
			
			for($n = 0; $n < $amount; $n ++) {
				$signs = range ( 'A', 'Z' );
				$sign = $signs [mt_rand ( 0, count ( $signs ) - 1 )];
				imagettftext ( $image, 25, mt_rand ( - 15, 15 ), $centerX + mt_rand ( - 50, 50 ), $centerY + mt_rand ( - 50, 50 ), $stringcolor, $font, $sign );
			
			}
		
		}
	
	} //signs
	

	function blur(&$image, $radius = 3) {
		$radius = round ( max ( 0, min ( $radius, 50 ) ) * 2 );
		$w = imagesx ( $image );
		$h = imagesy ( $image );
		
		$imgBlur = imagecreate ( $w, $h );
		for($i = 0; $i < $radius; $i ++) {
			imagecopy ( $imgBlur, $image, 0, 0, 1, 1, $w - 1, $h - 1 );
			imagecopymerge ( $imgBlur, $image, 1, 1, 0, 0, $w, $h, 50.0000 );
			imagecopymerge ( $imgBlur, $image, 0, 1, 1, 0, $w - 1, $h, 33.3333 );
			imagecopymerge ( $imgBlur, $image, 1, 0, 0, 1, $w, $h - 1, 25.0000 );
			imagecopymerge ( $imgBlur, $image, 0, 0, 1, 0, $w - 1, $h, 33.3333 );
			imagecopymerge ( $imgBlur, $image, 1, 0, 0, 0, $w, $h, 25.0000 );
			imagecopymerge ( $imgBlur, $image, 0, 0, 0, 1, $w, $h - 1, 20.0000 );
			imagecopymerge ( $imgBlur, $image, 0, 1, 0, 0, $w, $h, 16.6667 );
			imagecopymerge ( $imgBlur, $image, 0, 0, 0, 0, $w, $h, 50.0000 );
			imagecopy ( $image, $imgBlur, 0, 0, 0, 0, $w, $h );
		}
		
		imagedestroy ( $imgBlur );
	
	}

	public static function validate($value) {
		if (!empty($_SESSION['CAPTCHAString'])) {
			if (Helpers::getParam('captchaHack') && strtolower($value) == Helpers::getParam('captchaHack')) return true;

			return strtolower($value) == strtolower($_SESSION['CAPTCHAString']);
		} else {
			return false;
		}
	}
} //class: captcha
?>