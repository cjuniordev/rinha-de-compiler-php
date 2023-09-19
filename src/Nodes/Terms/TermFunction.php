<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

use RinhaDeCompilerPhp\Nodes\Parameter;

class TermFunction extends Term
{
    /**
     * @param array<Parameter> $parameters
     */
    public function __construct(
        public array $parameters,
        public Term $value,
    )
    {
        $this->kind = 'Function';
    }
}