<?php
/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2017 Samuel Heer | diging.de
 *
 * @license LGPL-3.0+
 */




/*ClassLoader::addClasses(array(
    'Diging\\Contao\\ChurchtoolsBundle\\ChurchtoolsEvents' => 'system/modules/churchtools/src/Diging/Contao/ChurchtoolsEvents.php'
));*/

$templatesFolder = version_compare(VERSION, '4.0', '>=')
	? 'vendor/diging/churchtools-bundle/templates'
	: 'system/modules/churchtools/templates';

/*TemplateLoader::addFiles(array(
	'*' => $templatesFolder
));*/
