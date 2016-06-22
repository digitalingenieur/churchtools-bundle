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
	'label'               => &$GLOBALS['TL_LANG']['MSC']['load'],
	'href'                => 'key=loadEvents',
	'class'               => 'header_loadEvents',
	'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="l"'
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property Contao\Calendar $Calendar
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class tl_calendar_events_churchtools extends tl_calendar_events
{

	public function disableCalendarFunctions(){

		$id = strlen(Input::get('id')) ? Input::get('id') : CURRENT_ID;

		$objCalendar = $this->Database->prepare("SELECT consumeCTEvents FROM tl_calendar WHERE id=?")
			->limit(1)
			->execute($id);
		
		if($objCalendar->consumeCTEvents){
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations']['all']);
			//TODO: Remove "New Event";
		};
	}

}