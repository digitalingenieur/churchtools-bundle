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

	/**
	 *  Store Authentication Cookie From Login Response
	 * @var array
	 */
	protected $auth = array();

	/**
	 * On object creation API gets contacted with credentials to authenticate the user
	 *
	 * @param string $email
	 * @param string $password
	 */
	public function __construct($email='', $password=''){

		if($email=='' && $GLOBALS['TL_CONFIG']['churchtools_email']==''){
			throw new \InvalidArgumentException('No E-Mail given. Please fill Churchtools-Mail Settings.');
		}
		if($password=='' && $GLOBALS['TL_CONFIG']['churchtools_password']==''){
			throw new \InvalidArgumentException('No Password given. Please fill Contao Settings Churchtools section.');
		}

		
		$postfields = array(
				'email' => $email==''? $GLOBALS['TL_CONFIG']['churchtools_email']:$email,
				'password' => $password==''? \Encryption::decrypt($GLOBALS['TL_CONFIG']['churchtools_password']):$password,
				'directtool' => 'yes'
		);
		$url = $GLOBALS['TL_CONFIG']['churchtools_baseUrl'].'/?q=login';
		
		$this->request($url, $postfields);
		//TODO: Exception Login not successful
	}

	/**
	 * On object creation API gets contacted with credentials to authenticate the user
	 *
	 */
	public function getCalendarCategories(){

		$postfields = array(
			'func' => 'getMasterData',
			'directtool' 	=> 'yes'
		);

		$url = $GLOBALS['TL_CONFIG']['churchtools_baseUrl'].'/index.php?q=churchcal/ajax';
		$masterData = $this->request($url,$postfields);
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

		$url = $GLOBALS['TL_CONFIG']['churchtools_baseUrl'].'/index.php?q=churchcal/ajax';
		return $this->request($url,$postfields);
	}


	protected function request($url, $postfields){
		//TODO: Throw exception if response is null (server nicht erreichbar?)
		//TODO: Throw exception if url is not given
		$ch = curl_init();

		//curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch,CURLOPT_POST, true);
		//curl_setopt($ch,CURLOPT_HEADER, true);
		
		//curl_setopt($ch,CURLOPT_COOKIE,implode($this->auth,';'));
		
		$arrOptions = array(
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_POST 			=> true,
			CURLOPT_HEADER 			=> true,
			CURLOPT_COOKIE			=> implode($this->auth,';'),
			CURLOPT_URL 			=> $url,
			CURLOPT_POSTFIELDS 		=> http_build_query($postfields)
		);
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