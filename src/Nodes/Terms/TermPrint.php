<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermPrint extends Term
{
    public function __construct(
        public Term $value,
    ) {
        $this->kind = 'Print';
    }
}