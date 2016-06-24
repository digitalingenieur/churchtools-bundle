<?php

/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2016 Samuel Heer
 *
 * @license LGPL-3.0+
 */

$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][] = array('tl_calendar_events_churchtools', 'disableCalendarFunctions');
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations']['load'] = array
(
	'label'               => &$GLOBALS['TL_LANG']['tl_calendar_events']['load'],
	'href'                => 'key=loadEvents',
	'class'               => 'header_sync',
	'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="l"'
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Samuel Heer <https://github.com/digitalingenieur>
 */
class tl_calendar_events_churchtools extends tl_calendar_events
{

	/**
	 * Make DCA as read only if calendar is set as churchtools calendar.
	 * 
	 */
	public function disableCalendarFunctions(){

		$id = strlen(Input::get('id')) ? Input::get('id') : CURRENT_ID;

		$calendar = \CalendarModel::findByPk($id);	
		
		if($calendar->churchtoolsEnableEvents){
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations']['all']);
			$GLOBALS['TL_DCA']['tl_calendar_events']['config']['closed'] = true;
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['edit']);
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['editheader']);
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['copy']);
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['delete']);
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['toggle']);
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['cut']);
		};

	}

}