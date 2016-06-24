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

 	static public function loadAndParseEvents($dc){

		$calendar = \CalendarModel::findByPk($dc->id);
		$arrCategoryIds = deserialize($calendar->churchtoolsCalendars);

 		$api = new ChurchtoolsApi();
 		$events = $api->loadEvents($arrCategoryIds,$calendar->churchtoolsDaysFrom,$calendar->churchtoolsDaysTo);

 		//Clear Database
 		$collection = \CalendarEventsModel::findByPid($dc->id);
 		if(isset($collection)){
 			while($collection->next()){
 				$collection->delete();
 			}
 		}
 		
 		foreach($events as $event){
 			$startdate = new \DateTime($event->startdate);
 			$enddate = new \DateTime($event->enddate);

 			$fullDayEvent = ($startdate->format('His') == 000000) && ($enddate->format('His') == 000000)? true : false;

 			$model = new \CalendarEventsModel();
 			$model->pid = $dc->id;
 			$model->tstamp = time();
 			$model->title = $event->bezeichnung;
 			//$model->alias = 
 			//$model->author = 

 			if(!$fullDayEvent){
 				$model->addTime = 1;
 				$model->startTime = $startdate->getTimestamp();
 				$model->endTime = $enddate->getTimestamp();	
 			}
 			else{
 				$model->startTime = $startdate->getTimestamp();
 				$model->endTime = $enddate->getTimestamp();	
 			}
 			
 			$model->startDate = $startdate->getTimestamp();
 			if($startdate!=$enddate){
 				$model->endDate =  $enddate->getTimestamp();	
 			}

 			//$model->location = 
 			//$model->teaser = 
 			$model->published = 1;
 			$model->save();
 		}


 		if(\Input::get('key')){
 			\Controller::redirect(preg_replace('/(&(amp;)?|\?)key=[^& ]*/i', '', \Environment::get('request')));	
 		}
 	}
 }