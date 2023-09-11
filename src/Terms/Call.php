<?php

namespace RinhaDeCompilerPhp\Terms;

class Call extends Term
{
    public function __construct(
        private Term $calle,
        private array $arguments, // Term[]
        Location $location,
    ) {
        $this->kind = 'Call';
        $this->location = $location;
    }
}