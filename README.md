# Contao Inputvar bundle

Contao is an Open Source PHP Content Management System for people who want a
professional website that is easy to maintain. Visit the [Contao website](https://contao.org)
for more information.

inputvar stellt Insert-Tags zum Lesen und Schreiben von GET-, POST- und SESSION-Variablen zur Verfügung:

```
{{get::var[::opt]}}                    // variable aus _GET
{{post::var[::opt]}}                   // variable aus _POST
{{posthtml::var[::opt]}}               // variable aus _POST (belaesst erlaubte HTML-Tags im Wert)
{{postraw::var[::opt]}}                // variable aus _POST (ohne grosse Sicherheitsueberpruefung -> UNSICHER!)
{{cookie::cookie[::opt]}}              // variable aus _COOKIE
{{session::var[::opt]}}                // variable aus _SESSION
{{setpost::var::value}                 // setzt variable in _POST 
{{setget::var::value}                  // setzt variable in _GET
{{setcookie::var::value}               // setzt variable in _COOKIE
```

Gültige Funktionsoptionen (opt):

* mysql_real_escape_string
* addslashes, stripslashes, standardize, ampersand, specialchars
* nl2br, nl2br_pre, strtolower, utf8_strtolower, strtoupper
* utf8_strtoupper, ucfirst, lcfirst, ucwords, trim, rtrim, ltrim
* utf8_romanize
* strlen, strrev
* decodeEntities, encodeEmail, number_format, number_format_2
* ANSI wandelt utf8



