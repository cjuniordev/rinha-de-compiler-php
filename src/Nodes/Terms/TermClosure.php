<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

use RinhaDeCompilerPhp\Nodes\Parameter;

class TermClosure extends Term
{
    /**
     * @param array<Parameter> $parameters
     */
    public function __construct(
        public array $parameters,
        public Term $value,
        public array $scope,
    ) {
        $this->kind = 'Closure';
    }
}