<?php

namespace RinhaDeCompilerPhp\Terms;

class RinhaFunction extends Term
{
    public function __construct(
        public array $parameters, // array of Parameters
        public $body,
        public array $environment,
        ?Location $location,
    )
    {
        $this->kind = 'Function';
        $this->location = $location;
    }
}