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

/* 
 * Umgestellt auf contao 5. läuft mit contao 4.13 und contao 5.3
 * Dank an Steffen Frey
 * Version ab 2.0.1
 * Logger wurde auf Contao 5 angepasst, 24.9.25 quapla
*/

namespace PBDKN\ContaoInputVarBundle\Resources\contao\Modules;

use Contao\System;

class InputVar extends \Contao\Frontend
{
    /**
     * Replace old Logger
     */
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
 
    /**
     * Replace hexadecimal umlauts with their corresponding UTF-8 representations.
     *
     * @param string $value The input string
     * @return string The string with replaced umlauts
     */
    private function replaceHexUmlauts($value): string
    {
        $replacements = [
            "\xC2\xB4" => '´',
            "\xC2\xA7" => '§',
            "\xC2\xB0" => '°',
            "\xE4" => 'ä',
            "\xC4" => 'Ä',
            "\xF6" => 'ö',
            "\xD6" => 'Ö',
            "\xFC" => 'ü',
            "\xDC" => 'Ü',
            "\xDF" => 'ß',
            "\xE9" => 'é',
            "\xE8" => 'è',
            "\xEA" => 'ê',
            "\xC9" => 'É',
            "\xC8" => 'È',
            "\xCA" => 'Ê',
            "\xE1" => 'á',
            "\xE0" => 'à',
            "\xE2" => 'â',
            "\xC1" => 'Á',
            "\xC0" => 'À',
            "\xC2" => 'Â',
        ];
        return strtr($value, $replacements);
    }

    /**
     * Convert a numeric string with comma as decimal separator to a float.
     *
     * @param string $value The numeric string
     * @return float The converted float value
     */
    private function convertStringToFloat($value): float
    {
        $value = str_replace(',', '.', $value);
        return (float) $value;
    }

    /**
     * Hilfsmethode: holt GET/POST/COOKIE-Werte kompatibel für Contao 4.13 und 5.3
     */
    private function getInputValue(string $type, string $key): ?string
    {
        // Contao 4: Input::getInstance() existiert
        if (class_exists(\Contao\Input::class) && method_exists(\Contao\Input::class, 'getInstance')) {
            $input = \Contao\Input::getInstance();

            return match ($type) {
                'get'      => $input->get($key),
                'post'     => $input->post($key),
                'postHtml' => $input->postHtml($key),
                'postRaw'  => $input->postRaw($key),
                'cookie'   => $input->cookie($key),
                default    => null,
            };
        }

        // Contao 5: nur noch Symfony Request
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();
        if (!$request) {
            return null;
        }

        return match ($type) {
            'get'      => $request->query->get($key),
            'post'     => $request->request->get($key),
            'postHtml' => htmlspecialchars((string) $request->request->get($key), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'postRaw'  => $request->request->get($key),
            'cookie'   => $request->cookies->get($key),
            default    => null,
        };
    }

    public function replaceInputVars($strTag)
    {
        $arrTag = explode('::', $strTag);

        if (!isset($arrTag[1])) {
            return false;
        }

        switch ($arrTag[0]) {
            case 'get':
                $varValue = $this->getInputValue('get', $arrTag[1]) ?? ($_GET[$arrTag[1]] ?? null);
                break;

            case 'post':
                $varValue = $this->getInputValue('post', $arrTag[1]) ?? ($_POST[$arrTag[1]] ?? null);
                break;

            case 'setpost':
                if (!isset($arrTag[2])) {
                    return false;
                }
                if (class_exists(\Contao\Input::class) && method_exists(\Contao\Input::class, 'getInstance')) {
                    \Contao\Input::getInstance()->setPost($arrTag[1], $arrTag[2]);
                } else {
                    $_POST[$arrTag[1]] = $arrTag[2];
                }
                $arrTag[2] = "";
                $varValue = '';
                break;

            case 'setget':
                if (!isset($arrTag[2])) {
                    return false;
                }
                if (class_exists(\Contao\Input::class) && method_exists(\Contao\Input::class, 'getInstance')) {
                    \Contao\Input::getInstance()->setGet($arrTag[1], $arrTag[2]);
                } else {
                    $_GET[$arrTag[1]] = $arrTag[2];
                }
                $arrTag[2] = "";
                $varValue = '';
                break;

            case 'setcookie':
                if (!isset($arrTag[2])) {
                    return false;
                }
                if (class_exists(\Contao\Input::class) && method_exists(\Contao\Input::class, 'getInstance')) {
                    \Contao\Input::getInstance()->cookie($arrTag[1], $arrTag[2]);
                } else {
                    setcookie($arrTag[1], $arrTag[2]);
                    $_COOKIE[$arrTag[1]] = $arrTag[2];
                }
                $arrTag[2] = "";
                $varValue = '';
                break;

            case 'postHtml':
                $varValue = $this->getInputValue('postHtml', $arrTag[1]) ?? (isset($_POST[$arrTag[1]]) ? htmlspecialchars($_POST[$arrTag[1]], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : null);
                break;

            case 'postRaw':
                $varValue = $this->getInputValue('postRaw', $arrTag[1]) ?? ($_POST[$arrTag[1]] ?? null);
                break;

            case 'cookie':
                $varValue = $this->getInputValue('cookie', $arrTag[1]) ?? ($_COOKIE[$arrTag[1]] ?? null);
                break;

            case 'session':
                $varValue = null;
                if (class_exists(\Contao\Session::class) && method_exists(\Contao\Session::class, 'getInstance')) {
                    $varValue = \Contao\Session::getInstance()->get($arrTag[1]);
                }
                if ($varValue === null && isset($_SESSION[$arrTag[1]])) {
                    $varValue = $_SESSION[$arrTag[1]];
                }
                break;

            default:
                if ($this->logger) {
                  $this->logger->error('Unknown insert tag flag: ' . ($arrTag[2] ?? ''), ['source' => __METHOD__]);
                }

                return false;
        }

        if (isset($arrTag[2])) {
            switch (($arrTag[2])) {
                case 'mysql_real_escape_string':
                case 'addslashes':
                case 'stripslashes':
                case 'standardize':
                case 'ampersand':
                case 'specialchars':
                case 'nl2br':
                case 'nl2br_pre':
                case 'strtolower':
                case 'utf8_strtolower':
                case 'strtoupper':
                case 'utf8_strtoupper':
                case 'ucfirst':
                case 'lcfirst':
                case 'ucwords':
                case 'trim':
                case 'rtrim':
                case 'ltrim':
                case 'utf8_romanize':
                case 'strlen':
                case 'strrev':
                    $varValue = $arrTag[2]($varValue);
                    break;

                case 'decodeEntities':
                case 'encodeEmail':
                    $this->import('String');
                    $varValue = $this->String->{$arrTag[2]}($varValue);
                    break;

                case 'number_format':
                    if (is_numeric($varValue)) {
                        $varValue = number_format((float) $varValue, 0, $GLOBALS['TL_LANG']['MSC']['decimalSeparator'], $GLOBALS['TL_LANG']['MSC']['thousandsSeparator']);
                    } else {
                        $varValue = number_format($this->convertStringToFloat($varValue), 0, $GLOBALS['TL_LANG']['MSC']['decimalSeparator'], $GLOBALS['TL_LANG']['MSC']['thousandsSeparator']);
                    }
                    break;

                case 'number_format_2':
                    if (is_numeric($varValue)) {
                        $varValue = number_format((float) $varValue, 2, $GLOBALS['TL_LANG']['MSC']['decimalSeparator'], $GLOBALS['TL_LANG']['MSC']['thousandsSeparator']);
                    } else {
                        $varValue = number_format($this->convertStringToFloat($varValue), 2, $GLOBALS['TL_LANG']['MSC']['decimalSeparator'], $GLOBALS['TL_LANG']['MSC']['thousandsSeparator']);
                    }
                    break;

                case 'ANSI':
                    if (!empty($varValue)) {
                        $varValue = $this->replaceHexUmlauts($varValue);
                    }
                    break;
            }
        }

        return \is_array($varValue) ? implode(', ', $varValue) : $varValue;
    }
}
