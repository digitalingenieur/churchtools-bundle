<?php

/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2016 Samuel Heer
 *
 * @license LGPL-3.0+
 */

namespace Diging\ChurchtoolsBundle;

/**
 * Provide methods regarding churchtools api.
 *
 * @author Samuel Heer <https://github.com/digitalingenieur>
 */
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
	 * Return categories array from api.
	 * Called api function: getMasterData
	 *
	 * @return array categories
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

	/**
	 * Return Events for given categories
	 * Called api function: getCalenderEvents
	 *
	 * @param array $arrCategories
	 * @param int $daysFrom
	 * @param int $daysTo
	 * @return array events
	 */
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

	/**
	 * Request helper function to query the api via curl
	 * TODO: Throw exception if response is null (server nicht erreichbar?)
	 * TODO: Throw exception if url is not given
	 * 
	 * @param string $strUrl
	 * @param array $arrPostfields
	 * @return array data
	 */
	protected function request($strUrl, $arrPostfields){

		$ch = curl_init();
		
		$arrOptions = array(
			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_POST 			=> true,
			CURLOPT_HEADER 			=> true,
			CURLOPT_COOKIE			=> implode($this->auth,';'),
			CURLOPT_URL 			=> $strUrl,
			CURLOPT_POSTFIELDS 		=> http_build_query($arrPostfields)
		);
		curl_setopt_array($ch, $arrOptions);

		$result = curl_exec($ch);
		curl_close($ch);

		$this->getCookiesFromCurlHeader($result);
		
		$arrResult = json_decode(substr($result, strrpos($result, "\r\n")));
		return $arrResult->data;
	}

	/**
	 * Helper class to get Set-Cookie out of CURL header and store it in auth attribute.
	 * TODO: This could be store in SESSION also, to reduce api calls
	 *
	 * @param string $arrCategories
	 * @param string $daysFrom
	 * @param string $daysTo
	 */
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