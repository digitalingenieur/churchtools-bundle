<?php

/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2016 Samuel Heer
 *
 * @license LGPL-3.0+
 */


/**
 * System configuration
 */
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'].';{churchtools_legend},consumeCTEvents';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'consumeCTEvents';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['consumeCTEvents'] = 'CTCalendars';


$GLOBALS['TL_DCA']['tl_calendar']['fields']['consumeCTEvents'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['consumeCTEvents'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['CTCalendars'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['CTCalendars'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('multiple'=>true),
	'options_callback'		  => array('tl_calendar_churchtools','getCalendarsFromChurchtools'),
	'sql'                     => "blob NULL"
);


class tl_calendar_churchtools extends Backend {

	/**
	 * Get all calendars and return them as array
	 *
	 * @return array
	 */
	public function getCalendarsFromChurchtools()
	{
		/*if (!$this->User->isAdmin && !is_array($this->User->calendars))
		{
			return array();
		}*/

		$arrCalendars = array();
		
		$api = new \Diging\ChurchtoolsBundle\ChurchtoolsApi();
		$categories = $api->getCalendarCategories();	
		
		foreach($categories as $category){
			$arrCalendars[$category->id] = $category->bezeichnung;
		}
		
		return $arrCalendars;
	}
}
