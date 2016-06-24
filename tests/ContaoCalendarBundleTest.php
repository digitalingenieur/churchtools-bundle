<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2016 Samuel Heer
 *
 * @license LGPL-3.0+
 */

namespace Diging\ChurchtoolsBundle\Test;

use Diging\ChurchtoolsBundle\ContaoChurchtoolsBundle;

/**
 * Tests the ContaoChurchtoolsBundle class.
 *
 * @author Samuel Heer <https://github.com/digitalingenieur>
 */
class ContaoChurchtoolsBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the object instantiation.
     */
    public function testInstantiation()
    {
        $bundle = new ContaoChurchtoolsBundle();

        $this->assertInstanceOf('Diging\ChurchtoolsBundle\ContaoChurchtoolsBundle', $bundle);
    }
}
