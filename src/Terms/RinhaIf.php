<?php

namespace RinhaDeCompilerPhp\Terms;

class RinhaIf extends Term
{
    public function __construct(
        private Term $condition,
        private Term $then,
        private Term $otherwise,
        Location $location,
    ) {
        $this->kind = 'If';
        $this->location = $location;
    }
}