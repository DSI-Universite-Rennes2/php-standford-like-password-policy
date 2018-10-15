<?php
/*
 * Copyright (c) 2018 Yann 'Ze' Richard <yann.richard@univ-rennes2.fr>
 *
 * SPDX-License-Identifier: LGPL-3.0-or-later
 * License-Filename: LICENSE
 */

/**
 * StandfordLikePasswordPolicy - Standford's like Password policy implementation
 *
 * @see       https://github.com/DSI-Universite-Rennes2/php-standford-like-password-policy
 *
 * @author    Yann 'Ze' Richard <yann.richard@univ-rennes2.fr>
 * @copyright Université Rennes 2 - https://www.univ-rennes2.fr/
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPL-3.0-or-later
 */
namespace UniversiteRennes2\StandfordLikePasswordPolicy;

use \Datetime;
use \NumberFormatter;

/**
 * StandfordLikePasswordPolicy - Standford's like Password policy implementation
 *
 */
class StandfordLikePasswordPolicy
{
    const TEST_FAILED  = 0;
    const TEST_PASSED  = 1;
    const TEST_USELESS = 2;

    /**
     * The character set used in multibytes PHP functions.
     *
     * @var string
     */
    protected $encoding = '';

    /**
     * Constructor.
     *
     * @param string $encoding character set used in multibytes PHP functions. Default: UTF-8
     */
    public function __construct(string $encoding = 'UTF-8')
    {
        $res = $this->setEncoding($encoding);
        if (! $res) {
            throw new Exception('Invalid encoding argument, check Oniguruma RegEx library encoding list');
        }
    }

    /**
     * Return current character set
     *
     * @return string current used charset
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set a new charset
     *
     * @param string $encoding new charset to used in multibytes PHP functions
     *
     * @return bool true on success, false if it's not a known charset
     */
    public function setEncoding(string $encoding)
    {
        if (strcmp($this->encoding, $encoding) === 0) {
            return true;
        }
        $res1 = mb_internal_encoding($encoding);
        $res2 = @mb_regex_encoding($encoding);
        if ($res1 && $res2) {
            $this->encoding = $encoding;
            return true;
        } elseif ($this->encoding != '') {
            // failback
            mb_internal_encoding($this->encoding);
            @mb_regex_encoding($this->encoding);
            return false;
        } else {
            return false;
        }
    }

    /**
     * Check if a password is compliant with the password policy
     *
     * @param string $password Password to test
     * @param array personal data about the user such as surname, givenname etc.
     *              see StandfordLikePasswordPolicy::checkPasswordContent
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function isCompliant(string $password, array $userInformation = array())
    {
        $res  = $this->getChecks($password, $userInformation);
        return $res['result'];
    }

    /**
     * Check password content compared to personal data
     *
     * @param string $password
     *   Password to check
     *
     * @param array $userInformation
     *   Personnal user information with a key foreach submitted data.
     *   exemple of keys :
     *     login
     *     firstname
     *     lastname
     *     birthdate
     *
     *     Sumbmitted date MUST have been formated : YYYY-MM-DD
     *
     * @return array
     *   Check results array details with keys :
     *
     *     result    TRUE|FALSE    Global test result: TRUE all OK. FALSE = Something wrong,
     *                             to find what checks fields array to find non-FALSE values.
     *
     *     foreach submitted data key :
     *
     *         key       array|FALSE   FALSE if not found
     *
     *                                 if found, the array contains all strings
     *                                 we considers matching with password
     *                                 content.
     */
    private function checkPasswordContent(string $password, array $userInformation)
    {
        $firstname = '';
        $lastname  = '';
        $birthdate = '';

        $finalTestResult = true;
        $finalResult     = array();

        foreach ($userInformation as $infoName => $info) {
            $format = 'Y-m-d';
            $date = DateTime::createFromFormat($format, $info);
            if ($date === false) {
                // not a date
                $res = $this->contains($password, $info);
                if ($res['result'] == true) {
                    $finalResult[$infoName] = $res['founds'];
                    $finalTestResult = false;
                } else {
                    $finalResult[$infoName] = false;
                }
            } else {
                // It's a date !
                $tests = array();

                // Year
                $tests[] = $date->format('Y'); // Year : YYYY
                $tests[] = $date->format('y'); // Year : YY

                // Month
                $tests[] = $date->format('n'); // Without leading 0

                // Day
                $tests[] = $date->format('j'); // day without leading 0

                $founds = array();
                // Now we need to search for all of theirs
                foreach ($tests as $val) {
                    $res = $this->contains($password, $val);
                    if ($res['result'] == true) {
                        $tmp = array_merge($founds, $res['founds']);
                        $finalTestResult = false;
                        $founds = $tmp;
                    }
                }
                if (! empty($founds)) {
                    $finalResult[$infoName] = $founds;
                    $finalTestResult = false;
                } else {
                    $finalResult[$infoName] = false;
                }
            }
        }
        $res = array(
            'result' => $finalTestResult,
            'fields' => $finalResult,
        );
        return $res;
    }

    /** Search the given value or similar into string :
     *
     *     - Search literal version of numeric value in 5 langs and their H@x0r transformation version
     *     - Search for H@x0r transformation version for non-numeric value
     *
     * @param $subject subject where searching
     *
     * @param $value value to search
     *
     * @return array Results of tests with keys :
     *     result   TRUE|FALSE  Global result
     *     founds   array       Contains all values ($value or transformations) founds
     */
    private function contains(string $subject, $value)
    {
        $tests = array();
        $res['result'] = false;

        $tests[] = $value;
        if (is_numeric($value)) {
            $locales = array( 'fr_FR', 'en_US', 'de_DE', 'it', 'es_ES' );
            $oldlocale = setlocale(LC_TIME, 0);
            foreach ($locales as $lang) {
                $number = (int) $value;
                if ($number <= 12) {
                    $resloc = setlocale(LC_TIME, $lang.'.UTF-8');
                    if ($resloc !== false) {
                        $date    = DateTime::createFromFormat('!m', $number);
                        $tests[] = strftime('%B', $date->getTimestamp()); // Complete name
                        $tests[] = strftime('%b', $date->getTimestamp()); // Abbreviated name
                    }
                }

                $fmt[$lang] = numfmt_create($lang, NumberFormatter::SPELLOUT);
                $newspelled = numfmt_format($fmt[$lang], $value);
                //echo "$lang : $value => $newspelled\n";
                $tests[] = $newspelled;
                $tests[] = $this->mbH4xx0r($newspelled);
            }
            setlocale(LC_TIME, $oldlocale);
        } else {
            $tests[] = $this->mbH4xx0r($value);
        }

        // Add ASCII version and version replaced - and ' by space
        $tmp = $tests;
        foreach ($tmp as $val) {
            $nodiacritic = iconv($this->encoding, 'ASCII//TRANSLIT', $val);
            $nosep       = str_replace(array('-', "'"), ' ', $nodiacritic);
            //echo "Rajoute dans les tests : $nosep\n";

            $tests[] = $nosep;
            $tests[] = $nodiacritic;
        }
        unset($tmp);

        // make tests unique
        $t = array_unique($tests);
        $tests = $t;
        unset($t);

        // running  all tests
        $founds = array();
        // Now we need to search for all of theirs
        foreach ($tests as $val) {
            if (mb_stripos($subject, $val) !== false) {
                $founds[]      = $val;
                $res['result'] = true;
            }
        }
        $res['founds'] = $founds;

        return $res;
    }

    /** Change the case of a chararacter
     *
     * @param $c Character to change case
     *
     * @return string changed character
     *
    private function _mb_changeCase(string $c) {
        $regUpper = '[\p{Upper}]';
        if ( mb_ereg($regUpper, $c) ) {
            return mb_strtolower($c);
        } else {
            return mb_strtoupper($c);
        }
    }
    */

    /** Transforms a text into h4xx0r-5tyl3 (simple way)
     *
     * @param $s String to transform
     *
     * @return Tranformed string
     */
    private function mbH4xx0r(string $s)
    {
        $replacements = array(
            'A' => '4',
            'B' => '8',
            //'l' => '|',
            //'H' => '|-|',
            //'C' => '(',
            'e' => '3',
            'f' => 'ph',
            //'G' => '6',
            //'g' => '6',
            'i' => '1',
            'l' => '1',
            // 'N' => '/\/',
            'o' => '0',
            'O' => '0',
            'S' => '5',
            's' => '5',
            't' => '7',
            'T' => '7',
            //'V' => '\/',
            'w' => 'vv',
        );

        $s = strtr($s, $replacements);
        return $s;
    }

    /**
     * Check password against policy and return each rules compliantcy
     *
     * @param string $password
     *   Password to check
     *
     * @param array $userInformation
     *   array of user's informations you don't want to be used in password. all information MUST have a key
     *   @see StandfordLikePasswordPolicy::checkPasswordContent()
     *
     * @return array
     *   Check results array details with keys :
     *
     *     result    TRUE|FALSE    Global test result
     *
     *     for the password constraints rules :
     *       'rules' => array(
     *           // constraints verification
     *           //  0     -> needed and failed
     *           //  1     -> needed and ok
     *           //  2     -> not needed
     *           'length' => 0,
     *           'upper'  => 0,
     *           'lower'  => 0,
     *           'digit'  => 0,
     *           'symbol' => 0,
     *       )
     *
     *     foreach submitted data key :
     *      'data' => array (
     *         key       array|FALSE   FALSE if not found
     *
     *                                 if found, the array contains all strings
     *                                 we considers matching with password
     *                                 content.
     *      )
     */
    public function getChecks(string $password, array $userInformation)
    {
        $resPwdCheck  = $this->checkStandfordPolicy($password);
        $resContent   = $this->checkPasswordContent($password, $userInformation);

        $globalResult = $resPwdCheck['result'] && $resContent['result'];

        $res = array(
            'result' => $globalResult,
            'rules'  => $resPwdCheck,
            'data'   => $resContent,
        );
        return $res;
    }

    /**
     * Check password against policy rules
     *
     * @param string $password Password to check
     *
     * @return array of detailled results (@see StandfordLikePasswordPolicy::getChecks())
     */
    private function checkStandfordPolicy(string $password)
    {

        // Manual regexp
        // $regSymbol = '[\x{0020}-\x{002F}\x{003A}-\x{0040}\x{005B}-\x{0060}\x{007B}-\x{007E}\x{00A0}-\x{00BF}]';
        // $regDigit  = '[0-9]';
        // $regUpper  = '[\x{0041}-\x{005A}\x{00C0}-\x{00D6}\x{00D8}-\x{00DD}]';
        // $regLower  = '[\x{0061}-\x{007A}\x{00E0}-\x{00F6}\x{00F8}-\x{00FF}]';

        // From   : http://php.net/manual/en/function.mb-ereg.php#120789
        //
        //          According to "https://github.com/php/php-src/tree/PHP-5.6/ext/mbstring/oniguruma",
        //          the bundled Oniguruma regex library version seems ...
        //          4.7.1 between PHP 5.3 - 5.4.45,
        //          5.9.2 between PHP 5.5 - 7.1.16,
        //          6.3.0 since PHP 7.2 - .
        //
        //          => https://github.com/geoffgarside/oniguruma/blob/master/Syntax.txt
        //
        //             Alnum, Digit, Upper, Lower works with all encodings.
        //
        $regSymbol = '[\p{^Alnum}]'; // Not alphanumeric = symbols
        $regDigit  = '[\p{Digit}]';
        $regUpper  = '[\p{Upper}]';
        $regLower  = '[\p{Lower}]';

        $res = array(
            'tested' => $password,
            'result' => false,

            // constraints verification
            //  self::TEST_FAILED  (0)     -> needed and failed
            //  self::TEST_PASSED  (1)     -> needed and ok
            //  self::TEST_USELESS (2)     -> not needed
            'length' => self::TEST_FAILED,
            'upper'  => self::TEST_FAILED,
            'lower'  => self::TEST_FAILED,
            'digit'  => self::TEST_FAILED,
            'symbol' => self::TEST_FAILED,
        );

        $password_len      = mb_strlen($password);
        $res['passlength'] = $password_len;

        if ($password_len > 0) {
            // check symbol
            if (mb_ereg($regSymbol, $password)) {
                $res['symbol'] = self::TEST_PASSED;
            }
            // check digit
            if (mb_ereg($regDigit, $password)) {
                $res['digit'] = self::TEST_PASSED;
            }
            // check uppercase
            if (mb_ereg($regUpper, $password)) {
                $res['upper'] = self::TEST_PASSED;
            }
            // check lowercase
            if (mb_ereg($regLower, $password)) {
                $res['lower'] = self::TEST_PASSED;
            }
        } else {
            return $res;
        }

        if ($password_len > 19) {
            // 20+ passwords
            $res['upper'] = self::TEST_USELESS;
            $res['lower'] = self::TEST_USELESS;
        }
        if ($password_len > 15) {
            // 16+
            $res['digit'] = self::TEST_USELESS;
        }
        if ($password_len > 11) {
            // 12+ passwords
            $res['symbol'] = self::TEST_USELESS;
        }

        if ($password_len > 8) {
            // 8+ passwords
            $res['length'] = self::TEST_PASSED;
        }

        if ($res['length'] && $res['symbol'] && $res['digit'] && $res['upper'] && $res['lower']) {
            $res['result'] = true;
        }

        return $res;
    }
}
