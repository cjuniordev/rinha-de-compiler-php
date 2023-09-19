<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

class TermFirst extends Term
{
    public function __construct(
        public Term $value,
    ) {
        $this->kind = 'First';
    }
}