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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_settings']['palettes']['default'].';{churchtools_legend},churchtools_baseUrl,churchtools_email,churchtools_password';

$GLOBALS['TL_DCA']['tl_settings']['fields']['churchtools_baseUrl'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['churchtools_baseUrl'],
	'inputType'               => 'text',		
	'eval'                    => array('rgxp'=>'url', 'trailingSlash'=>false, 'tl_class'=>'w50'),
		/*'save_callback' => array
		(
			array('tl_settings', 'checkStaticUrl')
		)*/
	);

$GLOBALS['TL_DCA']['tl_settings']['fields']['churchtools_email'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['churchtools_email'],
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'email', 'tl_class'=>'w50 clr')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['churchtools_password'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['churchtools_password'],
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50', 'hideInput'=>true, 'encrypt'=>true)
);

