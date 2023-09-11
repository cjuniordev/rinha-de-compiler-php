<?php

namespace RinhaDeCompilerPhp\Terms;

class Let extends Term
{
    public function __construct(
        private Parameter $name,
        private Term $value,
        private Term $next,
        Location $location,
    ) {
        $this->kind = 'Let';
        $this->location = $location;
    }
}