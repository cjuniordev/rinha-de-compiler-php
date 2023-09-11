<?php

namespace RinhaDeCompilerPhp\Terms;

use RinhaDeCompilerPhp\Interpreter;

class File
{
    public array $environment;
    public Term $expression;

    public function __construct(
        public string $name,
        Term|array $expression,
        public Location $location,
    )
    {
        $this->environment = [
            'objects' => [],
        ];

        if (is_array($expression)) {

        }
    }

    public function interpret()
    {
        return (new Interpreter())
            ->interpret($this->expression, $this->environment);
    }
}