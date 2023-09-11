<?php

use RinhaDeCompilerPhp\Interpreter;

require __DIR__ . '/../vendor/autoload.php';

$rawFile = file_get_contents(__DIR__ . '/../files/fib.json');

$parsedFile = json_decode($rawFile, true);

//$file = new File(
//    $parsedFile['name'],
//    $parsedFile['expression'],
//    Location::getInstanceByArray($parsedFile['location'])
//);

$environment = ['objects' => []];

$result = (new Interpreter())
    ->interpret($parsedFile['expression'], $environment);