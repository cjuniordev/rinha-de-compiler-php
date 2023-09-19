<?php

use RinhaDeCompilerPhp\Interpreter;
use RinhaDeCompilerPhp\Parser;

require __DIR__ . '/../vendor/autoload.php';

$filename = $argv[1] ?? null;

if (is_null($filename)) {
    echo "ERROR: filename is required.\nTry: 'composer rinha [FILENAME]'\n";
    die();
}

$parser = new Parser();

try {
    $file = $parser
        ->parse($filename)
        ->interpret();
} catch (Exception $e) {
    echo $e->getMessage();
}