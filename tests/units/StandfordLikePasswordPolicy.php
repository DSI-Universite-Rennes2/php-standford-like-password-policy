<?php
/*
 * Copyright (c) 2018 Yann 'Ze' Richard <yann.richard@univ-rennes2.fr>
 *
 * SPDX-License-Identifier: LGPL-3.0-or-later
 * License-Filename: LICENSE
 */

// The namespace of the tested class + "test\units"
namespace UniversiteRennes2\StandfordLikePasswordPolicy\tests\units;

// You must include the tested class (if you don't have an autoloader)
require_once __DIR__ . '/../../src/StandfordLikePasswordPolicy.php';

use atoum;

class StandfordLikePasswordPolicy extends atoum {

    public function test_construct() {
        $i = 0;
        /**/

        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                ->string($this->testedInstance->getEncoding())
                    ->isEqualTo('UTF-8');

        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance('UTF-16'))
            ->then
                ->string($this->testedInstance->getEncoding())
                    ->isEqualTo('UTF-16');

        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->exception(
				function() {
                    $this->newTestedInstance('AZERTY');
				}
        	)
	        ->error()
    	    ->withType(E_WARNING)   // pass
        	->exists()
		;
		//$this->exception->hasMessage('ValueError : invalid encoding parametter, check Oniguruma RegEx library encoding list');
    }

    public function test_setEncoding() {
		$i =0;

        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance('UTF-16'))
            ->then
                ->boolean($this->testedInstance->setEncoding('UTF-16'))->isTrue();


        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                ->boolean($this->testedInstance->setEncoding('AZERTY'))
                    ->isFalse()
                    ->error()
                    ->withType(E_WARNING)   // pass
                    ->exists()
        ;
    }

    public function test_isCompliant() {

        $i =0;

        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                // 20+ only lowercase
                ->boolean($this->testedInstance->isCompliant('invalid encoding parametter check'))->isTrue()
                // 9 with all needed mixed char
                ->boolean($this->testedInstance->isCompliant('azertyA1,'))->isTrue()
                // 13 with lower/upper/digit
                ->boolean($this->testedInstance->isCompliant('azertyA1azer'))->isTrue()
                // 16 with lower/upper
                ->boolean($this->testedInstance->isCompliant('azertyAaazerazee'))->isTrue()
                // 20+ with all mixed not needed
                ->boolean($this->testedInstance->isCompliant('invalid encoding parametter check,1A'))->isTrue()
            ;

        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                // empty passwords
                ->boolean($this->testedInstance->isCompliant(''))->isFalse()
                // less than 9 char 
                ->boolean($this->testedInstance->isCompliant('a'))->isFalse()
                // 9 missing digit
                ->boolean($this->testedInstance->isCompliant('azertyAA,'))->isFalse()
                // 9 missing upper
                ->boolean($this->testedInstance->isCompliant('azerty11,'))->isFalse()
                // 9 missing lower
                ->boolean($this->testedInstance->isCompliant('AZERTYA1,'))->isFalse()
                // 9 missing special
                ->boolean($this->testedInstance->isCompliant('azertyA1a'))->isFalse()
		;

        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                // use h4xx0r version of Homer
                ->boolean($this->testedInstance->isCompliant('h0m3r est vraiment trop fort', array('name' => 'Homer')))->isFalse()
                // use fr_FR month name of a given date
                ->boolean($this->testedInstance->isCompliant('Je suis né en Décembre', array('birth' => '1989-12-17')))->isFalse()
                // use fr_FR literal number 
                ->boolean($this->testedInstance->isCompliant('Je préfère le dix sept', array('birth' => '1989-12-17')))->isFalse()
                // use en_US literal number 
                ->boolean($this->testedInstance->isCompliant('I prefer seventeen near xmas', array('birth' => '1989-12-17')))->isFalse()
        ;

        $i++;
        $this->assert(__METHOD__.' : test #'.$i)
            ->given($this->newTestedInstance)
            ->then
                // working test
                ->boolean($this->testedInstance->isCompliant('être ou ne pas être telle est la question', array('name' => 'Homer', 'birth' => '1989-12-17')))->isTrue()
        ;
    } 

    public function test_getChecks() {
        $i = 0;
        require_once(__DIR__ . '/passwords.php');

        foreach ( $testResults as $testResult ) {
            $testName            = $testResult['testname'];
            $testPass            = $testResult['password'];
            $testData            = $testResult['personaldata'];
            $testExpectedResult  = $testResult['expected'];

            $this->assert(__METHOD__. "$testName")
                ->given($this->newTestedInstance)
                ->then
                    ->array($this->testedInstance->getChecks($testPass, $testData))
                        ->isEqualTo($testExpectedResult);
        }
    }
/**/

/**/
}