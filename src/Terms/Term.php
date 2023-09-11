<?php

namespace RinhaDeCompilerPhp\Terms;

abstract class Term
{
    public string $kind;
    public ?Location $location;
}