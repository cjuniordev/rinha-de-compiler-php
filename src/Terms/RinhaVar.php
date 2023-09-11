<?php

namespace RinhaDeCompilerPhp\Terms;

class RinhaVar extends Term
{
    public function __construct(
        private string $text,
        Location $location,
    ) {
        $this->kind = 'Var';
        $this->location = $location;
    }
}