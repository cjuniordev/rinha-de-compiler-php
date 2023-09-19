<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermIf extends Term
{
    public function __construct(
        public Term $condition,
        public Term $then,
        public Term $otherwise,
    ) {
        $this->kind = 'If';
    }
}