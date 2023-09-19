<?php

namespace RinhaDeCompilerPhp\Nodes;

use Exception;
use RinhaDeCompilerPhp\Interpreter;
use RinhaDeCompilerPhp\Nodes\Terms\Term;

class File
{
    public function __construct(
        public Term $expression,
    ) {}

    /**
     * @throws Exception
     */
    public function interpret(): Term
    {
        $newScope = [];

        return (new Interpreter())
            ->interpret($this->expression, $newScope);
    }
}