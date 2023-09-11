<?php

namespace RinhaDeCompilerPhp\Terms;

class RinhaInt extends Term
{
    public function __construct(
        private int $value,
        Location $location,
    ) {
        $this->kind = 'Int';
        $this->location = $location;
    }
}