<?php
/*
 * Copyright (c) 2018 Yann 'Ze' Richard <yann.richard@univ-rennes2.fr>
 *
 * SPDX-License-Identifier: LGPL-3.0-or-later
 * License-Filename: LICENSE
 */

$homerInformation = array(
    'login'            => 'simpsonh',
    'surname'          => 'Simpson',
    'givenname'        => 'Homer',
    'birthdate'        => '1989-12-17',
    'birthdeptcode'    => '49',
);

$testResults = array();

$testResults[] = array(
    'testname' => 'testing space counted as special char',
    'password' => 'le testK1',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => true,
        'rules' => array(
            // Tested password
            'tested'     => 'le testK1',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 1,
            'lower'      => 1,
            'digit'      => 1,
            'symbol'     => 1,
            'passlength' => 9,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => 'use literal dept number in fr_FR',
    'password' => 'Quarante neuf the place to be',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'Quarante neuf the place to be',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 2,
            'lower'      => 2,
            'digit'      => 2,
            'symbol'     => 2,
            'passlength' => 29,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => false,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'     => false,
                'birthdeptcode' => array( 'quarante neuf' ),
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => 'use en_US literal number',
    'password' => 'I prefer seventeen near xmas',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'I prefer seventeen near xmas',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 2,
            'lower'      => 2,
            'digit'      => 2,
            'symbol'     => 2,
            'passlength' => 28,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => false,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => array( 'seventeen' ),
                'birthdeptcode' => false,
            ),
        ),
    ),
);


$testResults[] = array(
    'testname' => 'use fr_FR literal number of the day birthdate',
    'password' => 'Je préfère le dix sept',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'Je préfère le dix sept',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 2,
            'lower'      => 2,
            'digit'      => 2,
            'symbol'     => 2,
            'passlength' => 22,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => false,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => array( 'dix sept' ),
                'birthdeptcode' => false,
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => 'use fr_FR month name of a given date',
    'password' => 'Je suis né en Décembre',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'Je suis né en Décembre',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 2,
            'lower'      => 2,
            'digit'      => 2,
            'symbol'     => 2,
            'passlength' => 22,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => false,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => array( 'décembre' ),
                'birthdeptcode' => false,
            ),
        ),
    ),
);


$testResults[] = array(
    'testname' => 'use h4xx0r version of givenname field',
    'password' => 'h0m3r est vraiment trop fort',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'h0m3r est vraiment trop fort',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 2,
            'lower'      => 2,
            'digit'      => 2,
            'symbol'     => 2,
            'passlength' => 28,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => false,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => array( 'H0m3r' ),
                'birthdate'     => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => '9 missing special',
    'password' => 'azertyA1a',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'azertyA1a',
            // expected results for rules
            'result'     => false,
            'length'     => 1,
            'upper'      => 1,
            'lower'      => 1,
            'digit'      => 1,
            'symbol'     => 0,
            'passlength' => 9,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'     => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => '9 missing lower',
    'password' => 'AZERTYA1,',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'AZERTYA1,',
            // expected results for rules
            'result'     => false,
            'length'     => 1,
            'upper'      => 1,
            'lower'      => 0,
            'digit'      => 1,
            'symbol'     => 1,
            'passlength' => 9,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => '9 missing upper',
    'password' => 'azerty11,',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'azerty11,',
            // expected results for rules
            'result'     => false,
            'length'     => 1,
            'upper'      => 0,
            'lower'      => 1,
            'digit'      => 1,
            'symbol'     => 1,
            'passlength' => 9,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => '9 missing digit',
    'password' => 'azertyAA,',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'azertyAA,',
            // expected results for rules
            'result'     => false,
            'length'     => 1,
            'upper'      => 1,
            'lower'      => 1,
            'digit'      => 0,
            'symbol'     => 1,
            'passlength' => 9,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);


$testResults[] = array(
    'testname' => 'less than 9 char',
    'password' => 'a',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => 'a',
            // expected results for rules
            'result'     => false,
            'length'     => 0,
            'upper'      => 0,
            'lower'      => 1,
            'digit'      => 0,
            'symbol'     => 0,
            'passlength' => 1,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => 'empty password',
    'password' => '',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => false,
        'rules' => array(
            // Tested password
            'tested'     => '',
            // expected results for rules
            'result'     => false,
            'length'     => 0,
            'upper'      => 0,
            'lower'      => 0,
            'digit'      => 0,
            'symbol'     => 0,
            'passlength' => 0,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);


$testResults[] = array(
    'testname' => '16 with lower/upper',
    'password' => 'azertyAaazerazee',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => true,
        'rules' => array(
            // Tested password
            'tested'     => 'azertyAaazerazee',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 1,
            'lower'      => 1,
            'digit'      => 2,
            'symbol'     => 2,
            'passlength' => 16,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);


$testResults[] = array(
    'testname' => '13 with lower/upper/digit',
    'password' => 'azertyA1azert',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => true,
        'rules' => array(
            // Tested password
            'tested'     => 'azertyA1azert',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 1,
            'lower'      => 1,
            'digit'      => 1,
            'symbol'     => 2,
            'passlength' => 13,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);

$testResults[] = array(
    'testname' => '20+ only lowercases',
    'password' => 'invalid encoding parametter check',
    'personaldata' => $homerInformation,

    'expected' => array(
        'result' => true,
        'rules' => array(
            // Tested password
            'tested'     => 'invalid encoding parametter check',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 2,
            'lower'      => 2,
            'digit'      => 2,
            'symbol'     => 2,
            'passlength' => 33,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);


$testResults[] = array(
    'testname' => '9 with all needed mixed char',
    'password' => 'azertyA1,',
    'personaldata' => $homerInformation,

    'expected' =>   array(
        'result' => true,
        'rules' => array(
            // Tested password
            'tested'     => 'azertyA1,',
            // expected results for rules
            'result'     => true,
            'length'     => 1,
            'upper'      => 1,
            'lower'      => 1,
            'digit'      => 1,
            'symbol'     => 1,
            'passlength' => 9,
        ),
        'data' => array(
            // expected results for personal data rules
            'result' => true,
            // each submitted data results
            'fields' => array(
                'login'         => false,
                'surname'       => false,
                'givenname'     => false,
                'birthdate'      => false,
                'birthdeptcode' => false,
            ),
        ),
    ),
);
