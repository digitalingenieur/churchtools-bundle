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
	'label'             => &$GLOBALS['TL_LANG']['tl_calendar_events']['load'],
	'href'              => 'key=loadEvents',
	'class'             => 'header_sync',
	'attributes'		=> 'onclick="Backend.getScrollOffset()" accesskey="l"'
);

//HOTFIX is needed if extension calendar_extended will be used.
//$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['repeatWeekday']['eval']['tl_class'] .= ' clr';


//Todo: Suche Icon zur Anzeige von Churchtools Event (Indicator)
/*array_insert($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations'],0, array('reload' => array
(	
	'label'             => &$GLOBALS['TL_LANG']['tl_calendar_events']['churchtools'],
	'icon'             	=> 'sync.svg',
	'button_callback' 	=> array('tl_calendar_events_churchtools','getLoadButton')
)));*/

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['churchtoolsID'] = array(
	'label'				=> &$GLOBALS['TL_LANG']['tl_calendar_events']['churchtoolsID'],
	'search'			=> true,
	'sql'				=> "int(32) unsigned NOT NULL default '0'"
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
	public function disableCalendarFunctions(DataContainer $dc){

		//Disable Fields in Edit View
		$model = \CalendarEventsModel::findByPk($dc->id);
		if($model->churchtoolsID != 0)
		{
			if(Input::get('act') == 'edit')
			{
				$arrDisableFields = ['startDate','endDate','startTime','endTime','addTime','title','teaser','location','source','url','target',];
				foreach($arrDisableFields as $field)
				{
					$GLOBALS['TL_DCA']['tl_calendar_events']['fields'][$field]['eval']['readonly'] = true;
				}
			}
		}


		//Disable Operations in List View
		$id = strlen(Input::get('id')) ? Input::get('id') : CURRENT_ID;

		$calendar = \CalendarModel::findByPk($id);	

		if($calendar->churchtoolsEnableEvents){
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['global_operations']['all']);
			$GLOBALS['TL_DCA']['tl_calendar_events']['config']['closed'] = true;
			//unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['edit']);
			//unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['editheader']);
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['copy']);
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['delete']);
			//unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['toggle']);
			unset($GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['cut']);
		};
	}

	public function getLoadButton($row, $href, $label, $title, $icon, $attributes)
	{
		//No direct access possible because it's not in palette visible
		$model = \CalendarEventsModel::findByPk($row['id']);
		if($model->churchtoolsID != 0)
		{
			return '<a href="#" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
		}
	}

	

}