<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermSecond extends Term
{
    public function __construct(
        public Term $value,
    ) {
        $this->kind = 'Second';
    }
}