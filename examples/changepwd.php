<?php

require_once __DIR__ . "/../vendor/autoload.php";

use UniversiteRennes2\StandfordLikePasswordPolicy\StandfordLikePasswordPolicy;

$passwordPolicy = new StandfordLikePasswordPolicy();

// get prepared test passwords and personnal data to test with.
require_once __DIR__ . '/../tests/units/passwords.php';

// Just for echo in colors :)
require_once __DIR__ . '/console.php';

function echoResult($result)
{
    $str = '';
    if ($result == 1) {
        $str = Console::green('PASSED');
    } elseif ($result == 2) {
        $str = Console::blue('Not needed');
    } else {
        $str = Console::red('FAILED');
    }
    return $str;
}


foreach ($testResults as $testResult) {
    $testName            = $testResult['testname'];
    $testPass            = $testResult['password'];
    $testData            = $testResult['personaldata'];
    $testExpectedResult  = $testResult['expected'];

    $result = $passwordPolicy->getChecks($testPass, $testData);

    echo "Test results for '$testPass' : ". echoResult($result['result'])  ."\n";
    echo "\n";
    echo "    Password Rules : ". echoResult($result['rules']['result']) ."\n";
    echo "        Length : " . echoResult($result['rules']['length']) ." (". $result['rules']['passlength']  .")\n";
    echo "        Upper  : " . echoResult($result['rules']['upper'])  ."\n";
    echo "        Lower  : " . echoResult($result['rules']['lower'])  ."\n";
    echo "        Digit  : " . echoResult($result['rules']['digit'])  ."\n";
    echo "        Symbol : " . echoResult($result['rules']['symbol']) ."\n";
    echo "\n";
    echo "    Content rules : ". echoResult($result['data']['result']) ."\n";
    if (!$result['data']['result']) {
        foreach ($result['data']['fields'] as $fieldname => $what) {
            if ($what !== false) {
                $founds = implode(',', $what);
                echo "        -> $fieldname (". Console::red($founds) . ") as been found in given password !\n";
            }
        }
    }
    echo "\n";
    echo "\n";
}
