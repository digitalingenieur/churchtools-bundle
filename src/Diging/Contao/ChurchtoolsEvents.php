<?php

/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2016-2017 Samuel Heer
 *
 * @license LGPL-3.0+
 */

namespace Diging\Contao\ChurchtoolsBundle;

/**
 * Provide methods consuming events from churchtools api.
 *
 * @author Samuel Heer <https://github.com/digitalingenieur>
 */
class ChurchtoolsEvents extends \Events{

    //dummy function to get it work
    function __construct(){}

    //Dummy function because of abstract parent class
    function compile(){}

    protected $arrChanges = array('created'=>[], 'deleted'=>[], 'modified'=>[]);

    protected $calendar = null;

    function setCalendar($calendar){
        $this->calendar = $calendar;
    }

    /**
     * Load Churchtool Events on Request for specified Churchtools Calendars.
     *
     * @param $arrEvents
     * @param $arrCalendars
     * @param $intStart
     * @param $intEnd
     
     * @return mixed
     */

    public function getChurchtoolsEvents($arrEvents, $arrCalendars, $intStart, $intEnd){

        $this->arrEvents = $arrEvents;

        foreach($arrCalendars as $intCalendar) {
            $calendar = \CalendarModel::findByPk($intCalendar);
            if ($calendar->churchtoolsEnableEvents) {
                $arrCategoryIds = deserialize($calendar->churchtoolsCalendars);

                $daysFrom = round(($intStart - time()) / 3600 / 24);

                //Ausführungsdifferenz von 1ms in Testumgebung, Toleranz von 5ms
                $intEndDifference = ($intEnd - time());
                $daysTo = round((abs($intEndDifference) < 5) ? 0 : $intEndDifference / 3600 / 24);

                $api = new ChurchtoolsApi();
                $events = $api->loadEvents($arrCategoryIds, $daysFrom, $daysTo);

                foreach($events as $event){

                    $startDate = new \DateTime($event->startdate);
                    $endDate = new \DateTime($event->enddate);

                    $fullDayEvent = ($startDate->format('His') == 000000) && ($endDate->format('His') == 000000)? true : false;

                    $model = new \CalendarEventsModel();
                    $model->tstamp = time();
                    $model->title = $event->bezeichnung;
                    $model->location = $event->ort;
                    $model->teaser = $event->notizen;

                    if($event->link){
                        $model->source = 'external';
                        $model->url = $event->link;
                        $model->target = 1;
                    }
                    $model->startTime = $startDate->getTimestamp();
                    $model->endTime = $endDate->getTimestamp();

                    if(!$fullDayEvent) {
                        $model->addTime = 1;
                    }

                    $model->startDate = $startDate->getTimestamp();
                    if($startDate!=$endDate){
                        $model->endDate =  $endDate->getTimestamp();
                    }

                    $model->published = 1;

                    $this->addEvent($model,$model->startTime,$model->endTime,$this->strUrl,$intStart,$intEnd,$intCalendar);
                }
            }
        }

        return $this->arrEvents;
    }

    public function loadEvents(){

        if($this->Input->get('key') != 'loadEvents')
        {
            return '';
        }

        $this->calendar = \CalendarModel::findByPk($this->Input->get('id'));
        
        $this->loadAndParseEvents();

        // Zurück zur Übersicht leiten
        \Contao\Controller::redirect(str_replace('&key=loadEvents','',\Environment::get('requestUri')));
    }

    public function reloadChurchtoolsEventsHook()
    {
        $calendars = \CalendarModel::findBy(array('churchtoolsEnableEvents=? AND churchtoolsCalendars != ?'),array(1,'null'));
        foreach($calendars as $calendar){
            $this->calendar = $calendar;
            $this->loadAndParseEvents();
        }

        //TODO: USE LOGGER CLASS TO GIVE FEEDBACK
    }

    public function loadAndParseEvents()
    {
        $churchtoolCalendar = deserialize($this->calendar->churchtoolsCalendars);

        $events = \Diging\ChurchtoolsAPI\Models\Event::getByCategories($churchtoolCalendar);
    
        $this->deleteModels($events->modelKeys());

        //Create and Update Models
        foreach($events as $event){

            $model = $this->createModelIfItNotExists($event->id);
            
            //Update Model 
            $this->updateModel($event, $model);
        }

        // Eine Meldung im Backend erzeugen
        \Contao\Message::addInfo('Reload erfolgreich. '.count($this->arrChanges['modified']).' angepasst. '.count($this->arrChanges['deleted']).' Gelöscht. '.count($this->arrChanges['created']).' neu erstellt.');
    }

    public function deleteModels(array $arrChurchtoolsId){
        
        //Build Query
        $query = $arrChurchtoolsId;
        foreach($query as &$value){
            $value = 'AND churchtoolsID!=\''.$value.'\'';
        }
        unset($value);
        
        //Delete models, which are not in Churchtools anymore
        $localEvents = \CalendarEventsModel::findByPid($this->calendar->id,array('column'=> array('(pid=? AND churchtoolsID!=0 '.implode(' ',$query).')')));

        if(!empty($localEvents)){
            foreach($localEvents as $model){
                $this->arrChanges['deleted'][] = $model->id;
                $model->delete();
            }    
        }
    }

    public function createModelIfItNotExists($churchtoolsId){
        $model = \CalendarEventsModel::findByChurchtoolsID($churchtoolsId)[0];
            //Create Model if it's not exist
            if($model == null)
            {
                $model =  new \CalendarEventsModel();
                $model->pid = $this->calendar->id;
                $model->churchtoolsID = $churchtoolsId;
                $model->published = 1;
                $this->arrChanges['created'][] = $model->id;
            }
        return $model;
    }

    public function updateModel($event,$model)
    {
        if($model->title != $event->name) $model->title = $event->name;
        if($model->teaser != $event->description) $model->teaser = $event->description;

        if($event->place){
            if($model->location != $event->place) $model->location = $event->place;    
        }           

        if($event->link){
            if($model->source != 'external') $model->source = 'external';
            if($model->url != $event->link) $model->url = $event->link;
            if($model->target != 1) $model->target = 1;
        }
        else{
            if($model->source != 'default') $model->source = 'default';
        }

                
        if($model->startTime != $event->startdate->getTimestamp()) 
            $model->startTime = $event->startdate->getTimestamp();
                
        if($model->endTime != $event->enddate->getTimestamp()) 
            $model->endTime = $event->enddate->getTimestamp();

        if($model->startDate != $event->startdate->getTimestamp()) 
            $model->startDate = $event->startdate->getTimestamp();

        //Mehrtägiges Event
        $MultipleDayEvent = $event->startdate->diff($event->enddate);
        if($MultipleDayEvent->format('%a') > 0){
            if($model->endDate != $event->enddate->getTimestamp()) 
            $model->endDate = $event->enddate->getTimestamp();    
        }
        else{
            if($model->endDate != null) 
            $model->endDate = null;
        }
    
        //Ganztägiges Event
        $fullDayEvent = ($event->startdate->format('His') == 000000) && ($event->enddate->format('His') == 000000)? true : false;
        if($fullDayEvent)
        {
            if($model->addTime != 0) $model->addTime = 0;
        }
        else 
        {
            if($model->addTime != 1) $model->addTime = 1;
        }

        //TODO: Recurring EVENTS!!

        if($model->isModified()){
            $model->tstamp = time();
            $model->save();  

            if(!in_array($model->id, $this->arrChanges['created'])){
                $this->arrChanges['modified'][] = $model->id;    
            }
        }
        
        return $model;
    }
 }