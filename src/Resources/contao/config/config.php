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

$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = ['PBDKN\ContaoInputVarBundle\InputVar', 'replaceInputVars'];
