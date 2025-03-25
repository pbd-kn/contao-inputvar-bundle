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

namespace PBDKN\ContaoInputVarBundle\Resources\contao\Modules;

use Contao\System;

class InputVar extends \contao\Frontend
{
  /**
     * Replace hexadecimal umlauts with their corresponding UTF-8 representations.
     *
     * @param string $value The input string
     * @return string The string with replaced umlauts
     */
    private function replaceHexUmlauts($value)
    {
        $replacements = [
            // First replace longer sequences
            "\xC2\xB4" => '´', // ´
            "\xC2\xA7" => '§', // §
            "\xC2\xB0" => '°',  // °
            
            // Then replace single-byte sequences
            "\xE4" => 'ä', // ä
            "\xC4" => 'Ä', // Ä
            "\xF6" => 'ö', // ö
            "\xD6" => 'Ö', // Ö
            "\xFC" => 'ü', // ü
            "\xDC" => 'Ü', // Ü
            "\xDF" => 'ß', // ß
            "\xE9" => 'é', // é
            "\xE8" => 'è', // è
            "\xEA" => 'ê', // ê
            "\xC9" => 'É', // É
            "\xC8" => 'È', // È
            "\xCA" => 'Ê', // Ê
            "\xE1" => 'á', // á
            "\xE0" => 'à', // à
            "\xE2" => 'â', // â
            "\xC1" => 'Á', // Á
            "\xC0" => 'À', // À
            "\xC2" => 'Â', // Â
        ];
        return strtr($value, $replacements);
    }
    
    /**
     * Convert a numeric string with comma as decimal separator to a float.
     *
     * @param string $value The numeric string
     * @return float The converted float value
     */
    private function convertStringToFloat($value)
    {
        $value = str_replace(',', '.', $value);
        return (float)$value;
    }

    public function replaceInputVars($strTag)
    {
        $arrTag = explode('::', $strTag);

        if (!isset( $arrTag[1])) {
            return false;
        }

        switch ($arrTag[0]) {
            case 'get':
                $this->import('Contao\Input');
                $varValue = $this->Input->get($arrTag[1]);  
                  // Log the value to the error log                          
                break;
            case 'post':
                $this->import('Contao\Input');
                $varValue = $this->Input->post($arrTag[1]);
                break;
            case 'setpost':
                //$_POST Variable setzen
                if (!isset( $arrTag[2])) return false;
                $this->import('Contao\Input');
                $this->Input->setPost($arrTag[1],$arrTag[2]);
                $arrTag[2]="";          // damit nicht aus versehen mit opt ausgewertet
                $varValue = "";
                break;
            case 'setget':
                //$_GET Variable setzen
                if (!isset( $arrTag[2])) return false;
                $this->import('Contao\Input');
                $this->Input->setGet($arrTag[1],$arrTag[2]);
                $varValue = "Tag1 ".$arrTag[1]." tag2 ".$arrTag[2];
                $varValue = "";
                $arrTag[2]="";          // damit nicht aus versehen mit opt ausgewertet
                break;

            case 'setcookie':
                //$_COOKIE Variable setzen
                if (!isset( $arrTag[2])) return false;
                $this->import('Contao\Input');
                $this->Input->cookie($arrTag[1],$arrTag[2]);
                $arrTag[2]="";          // damit nicht aus versehen mit opt ausgewertet
                $varValue = "";
                break;
            case 'postHtml':
                $this->import('Contao\Input');
                $varValue = $this->Input->postHtml($arrTag[1]);
                break;

            case 'postRaw':
                $this->import('Contao\Input');
                $varValue = $this->Input->postRaw($arrTag[1]);
                break;

            case 'cookie':
                $this->import('Contao\Input');
                $varValue = $this->Input->cookie($arrTag[1]);
                break;

            case 'session':
                $this->import('Session');
                $varValue = $this->Session->get($arrTag[1]);
                break;

            default:
                // Log unknown flags
                System::log('Unknown insert tag flag: ' . $arrTag[2], __METHOD__, TL_ERROR);
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
                    $varValue = number_format((float)$varValue, 0, $GLOBALS['TL_LANG']['MSC']['decimalSeparator'], $GLOBALS['TL_LANG']['MSC']['thousandsSeparator']);
                } else {
                    $varValue = number_format($this->convertStringToFloat($varValue), 0, $GLOBALS['TL_LANG']['MSC']['decimalSeparator'], $GLOBALS['TL_LANG']['MSC']['thousandsSeparator']);
                }
                break;

            case 'number_format_2':
                if (is_numeric($varValue)) {
                    $varValue = number_format((float)$varValue, 2, $GLOBALS['TL_LANG']['MSC']['decimalSeparator'], $GLOBALS['TL_LANG']['MSC']['thousandsSeparator']);
                } else {
                    $varValue = number_format($this->convertStringToFloat($varValue), 2, $GLOBALS['TL_LANG']['MSC']['decimalSeparator'], $GLOBALS['TL_LANG']['MSC']['thousandsSeparator']);
                }
                break;

            case 'ANSI':                     
                  if (!empty($varValue)) {
                      $varValue = $this->replaceHexUmlauts($varValue);
                      // System::log('InputVar GET (Coded): ' . $varValue, __METHOD__, TL_ERROR);      
                  }
                break;
          }
        }
                
        return \is_array($varValue) ? implode(', ', $varValue) : $varValue;
    }
}
