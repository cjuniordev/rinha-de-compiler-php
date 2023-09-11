<?php

namespace RinhaDeCompilerPhp\Terms;

class RinhaPrint extends Term
{
    public function __construct(
        private Term $value,
        Location $location,
    ) {
        $this->kind = 'Print';
        $this->location = $location;
    }
}