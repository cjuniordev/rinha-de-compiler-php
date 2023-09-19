<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermInt extends Term
{
    public function __construct(
        public int $value,
    ) {
        $this->kind = 'Int';
    }
}