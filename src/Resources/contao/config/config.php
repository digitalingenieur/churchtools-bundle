<?php 

/**
 * Contao Churchtools-Bundle
 *
 * Copyright (c) 2016 Samuel Heer
 *
 * @license LGPL-3.0+
 */

$GLOBALS['BE_MOD']['content']['calendar']['loadEvents'] = array('\Diging\ChurchtoolsBundle\ChurchtoolsEvents','loadAndParseEvents');