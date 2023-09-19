<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermVar extends Term
{
    public function __construct(
        public string $text,
    ) {
        $this->kind = 'Var';
    }
}