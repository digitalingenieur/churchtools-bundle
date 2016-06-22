<?php

/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2016 Samuel Heer
 *
 * @license LGPL-3.0+
 */

namespace Diging\ChurchtoolsBundle;

class ChurchtoolsEvents extends \Backend{

 	public function loadAndParseEvents(){

 		$id = strlen(\Input::get('id')) ? \Input::get('id') : CURRENT_ID;

		$arrCtCalendars = $this->Database->prepare("SELECT CTCalendars FROM tl_calendar WHERE id=?")
			->limit(1)
			->execute($id);

		$arrCategoryIds = deserialize($arrCtCalendars->CTCalendars);

 		$api = new ChurchtoolsApi();
 		$eventCategories = $api->loadEvents($arrCategoryIds);
 		

 		foreach($eventCategories as $id => $category){
 			dump($category);
 			foreach($category as $event){
 				//dump($event->bezeichnung);
 			}
 		}

 		//Clear Database

 		//Parse Events into Database

 		dump('Load events in ChurchtoolsEvents loaded');
 	}
 }