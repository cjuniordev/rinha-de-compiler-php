<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermTuple extends Term
{
    public function __construct(
        public Term $first,
        public Term $second,
    ) {
        $this->kind = 'Tuple';
    }
}