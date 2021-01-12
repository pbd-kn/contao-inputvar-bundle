<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 * @copyright  Peter Broghammer 2020
 * @author     Peter Broghammer (PBD)
 * @package    Contao Inputvar Bundle
 * @license    LGPL-3.0-or-later
 */

namespace PBDKN\ContaoInputVarBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use PBDKN\ContaoInputVarBundle\ContaoInputVarBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoInputVarBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),        ];
    }
}
