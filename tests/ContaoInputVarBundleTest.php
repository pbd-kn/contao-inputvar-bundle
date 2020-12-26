<?php

declare(strict_types=1);

/*
 * This file is part of [package name].
 *
 * (c) John Doe
 *
 * @license LGPL-3.0-or-later
 */

namespace PBD-KN\InputVarBundle\Tests;

use PBD-KN\InputVar\ContaoInputVarBundle;
use PHPUnit\Framework\TestCase;

class ContaoInputVarBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new ContaoInputVarBundle();

        $this->assertInstanceOf('PBD-KN\ContaoInputVarBundle\ContaoInputVarBundle', $bundle);
    }
}
