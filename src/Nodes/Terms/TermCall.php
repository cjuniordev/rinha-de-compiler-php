<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermCall extends Term
{
    /**
     * @param array<Term> $arguments
     */
    public function __construct(
        public Term $callee,
        public array $arguments,
    ) {
        $this->kind = 'Call';
    }
}