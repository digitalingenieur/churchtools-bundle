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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_settings']['palettes']['default'].';{churchtools_legend},churchtools_baseUrl';

$GLOBALS['TL_DCA']['tl_settings']['fields']['churchtools_baseUrl'] = array
	(
		'label'                   => &$GLOBALS['TL_LANG']['MSC']['churchtools_baseUrl'],
		'inputType'               => 'text',
		'eval'                    => array('rgxp'=>'url', 'trailingSlash'=>false, 'tl_class'=>'w50'),
		/*'save_callback' => array
		(
			array('tl_settings', 'checkStaticUrl')
		)*/
	);
