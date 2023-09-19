<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

use RinhaDeCompilerPhp\Nodes\Parameter;

class TermLet extends Term
{
    public function __construct(
        public Parameter $name,
        public Term $value,
        public Term $next,
    ) {
        $this->kind = 'Let';
    }
}