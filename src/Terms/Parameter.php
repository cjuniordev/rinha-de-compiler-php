<?php

namespace RinhaDeCompilerPhp\Terms;

class Parameter
{
    public function __construct(
        private string $text,
        private Location $location,
    ) {}
}