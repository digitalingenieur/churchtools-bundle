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
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'].';{churchtools_legend},churchtoolsEnableEvents';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'churchtoolsEnableEvents';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['churchtoolsEnableEvents'] = 'churchtoolsCalendars,churchtoolsDaysFrom,churchtoolsDaysTo';

$GLOBALS['TL_DCA']['tl_calendar']['config']['onsubmit_callback'][]=array('tl_calendar_churchtools','refreshChurchtoolsEvents');


$GLOBALS['TL_DCA']['tl_calendar']['fields']['churchtoolsEnableEvents'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['churchtoolsEnableEvents'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['churchtoolsCalendars'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['churchtoolsCalendars'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('mandatory'=>true, 'multiple'=>true),
	'options_callback'		  => array('tl_calendar_churchtools','getCalendarsFromChurchtools'),
	'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['churchtoolsDaysFrom'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['churchtoolsDaysFrom'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'maxlength'=>4, 'tl_class'=>'w50'),
	'sql'                     => "int(4) NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_calendar']['fields']['churchtoolsDaysTo'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['churchtoolsDaysTo'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'maxlength'=>4, 'tl_class'=>'w50'),
	'sql'                     => "int(4) NOT NULL default '7'"
);



class tl_calendar_churchtools extends Backend {

	/**
	 * Get all calendars and return them as array
	 *
	 * @return array
	 */
	public function getCalendarsFromChurchtools()
	{
		$arrCalendars = array();
	
		//Execute Api Call only in "Edit mode" (create or update)	
		if(\Input::get('act') == 'edit'){
			$api = new\Diging\Contao\ChurchtoolsBundle\ChurchtoolsApi();
			$categories = $api->getCalendarCategories();	
			
			foreach($categories as $category){
				$arrCalendars[$category->id] = $category->bezeichnung;
			}
		}
		
		return $arrCalendars;
	}

	/**
	 * Trigger Reload of churchtools events
	 *
	 */
	public function refreshChurchtoolsEvents(DataContainer $dc){
		\Diging\Contao\ChurchtoolsBundle\ChurchtoolsEvents::loadAndParseEvents($dc);
	}
}
