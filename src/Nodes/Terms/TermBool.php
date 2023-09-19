<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermBool extends Term
{
    public function __construct(
        public bool $value,
    ) {
        $this->kind = 'Int';
    }
}