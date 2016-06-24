<?php

/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2016 Samuel Heer
 *
 * @license LGPL-3.0+
 */

namespace Diging\ChurchtoolsBundle;

class ChurchtoolsApi{

	protected $auth = array();


	public function __construct($email='', $password=''){

		if($email=='' && $GLOBALS['TL_CONFIG']['churchtools_email']==''){
			throw new \InvalidArgumentException('No E-Mail given. Please fill Churchtools-Mail Settings.');
		}
		if($password=='' && $GLOBALS['TL_CONFIG']['churchtools_password']==''){
			throw new \InvalidArgumentException('No Password given. Please fill Contao Settings Churchtools section.');
		}

		
		$options = array(
			CURLOPT_URL => $GLOBALS['TL_CONFIG']['churchtools_baseUrl'].'/?q=login',
			CURLOPT_POSTFIELDS => array(
				'email' => $email==''? $GLOBALS['TL_CONFIG']['churchtools_email']:$email,
				'password' => $password==''? \Encryption::decrypt($GLOBALS['TL_CONFIG']['churchtools_password']):$password,
				'directtool' => 'yes'
				)
			);

		//TODO: Exception Login not successful
		$this->request($options);
	}

	public function getCalendarCategories(){

		$options = array(
			CURLOPT_URL => $GLOBALS['TL_CONFIG']['churchtools_baseUrl'].'/index.php?q=churchcal/ajax',
			CURLOPT_POSTFIELDS => array(
				'func' => 'getMasterData',
				'directtool' => 'yes'
				)
			);
		$masterData = $this->request($options);
		return $masterData->category;
	}

	public function loadEvents($arrCategories,$daysFrom,$daysTo){
		$postfields = array(
			'func'			=> 'getCalendarEvents',
			'category_ids' 	=> $arrCategories,
			'directtool' 	=> 'yes',
			'from' 			=> $daysFrom,
			'to' 			=> $daysTo
		);

		$options = array(
			CURLOPT_URL => $GLOBALS['TL_CONFIG']['churchtools_baseUrl'].'/index.php?q=churchcal/ajax',
			CURLOPT_POSTFIELDS => http_build_query($postfields)
			);

		return $this->request($options);
	}


	protected function request($arrOptions){
		//TODO: Throw exception if response is null (server nicht erreichbar?)
		//TODO: Throw exception if url is not given
		$ch = curl_init();

		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_HEADER, true);
		
		curl_setopt($ch,CURLOPT_COOKIE,implode($this->auth,';'));
		
		curl_setopt_array($ch, $arrOptions);

		$result = curl_exec($ch);
		curl_close($ch);

		$this->getCookiesFromCurlHeader($result);
		
		$json = json_decode(substr($result, strrpos($result, "\r\n")));
		return $json->data;
	}

	private function getCookiesFromCurlHeader($response){

    	$header_text = substr($response, 0, strrpos($response, "\r\n"));

	    foreach (explode("\r\n", $header_text) as $i => $line){

	    	list ($key, $value) = explode(': ', $line);
	    	if($key == 'Set-Cookie'){
	    		$this->auth[] = substr($value,0, strpos($value, ";"));	
	    	}
	    }
	}
	
	

}