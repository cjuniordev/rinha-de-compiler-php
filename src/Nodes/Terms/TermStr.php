<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermStr extends Term
{
    public function __construct(
        public string $value,
    ) {
        $this->kind = 'Str';
    }
}