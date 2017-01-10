<?php

/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2016 Samuel Heer
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

    /**
     * Load Churchtool Events on Request for specified Churchtools Calendars.
     *
     * @param $arrEvents
     * @param $arrCalendars
     * @param $intStart
     * @param $intEnd
     * @param $objModule
     * @return mixed
     */

    public function getChurchtoolsEvents($arrEvents, $arrCalendars, $intStart, $intEnd, $objModule){

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
 }