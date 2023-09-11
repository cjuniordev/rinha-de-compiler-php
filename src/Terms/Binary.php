<?php

namespace RinhaDeCompilerPhp\Terms;

use RinhaDeCompilerPhp\Terms\Enums\BinaryOperator;

class Binary extends Term
{
    private BinaryOperator $operator;

    public function __construct(
        BinaryOperator|string $operator,
        private array $lhs,
        private array $rhs,
        ?Location $location,
    ) {
        $this->kind = 'Binary';
        $this->location = $location;

        if (is_string($operator)) {
            $operator = BinaryOperator::from($operator);
        }

        $this->operator = $operator;
    }

    private function add(): array
    {
        if ($this->lhs['kind'] == 'number' && $this->rhs['kind'] == 'number') {
            return [
                'kind' => 'number',
                'value' => $this->lhs['value'] + $this->rhs['value'],
            ];
        }

        $lhs = $this->lhs['value']; // To String
        $rhs = $this->rhs['value']; // To String

        return [
            'kind' => 'string',
            'value' => $lhs . $rhs,
        ];
    }

    private function sub(): array
    {
        return [
            'kind' => 'number',
            'value' => $this->lhs['value'] - $this->rhs['value'], // TODO: assert if is number
        ];
    }

    private function mul(): array
    {
        return [
            'kind' => 'number',
            'value' => $this->lhs['value'] * $this->rhs['value'], // TODO: assert if is number
        ];
    }

    private function div(): array
    {
        return [
            'kind' => 'number',
            'value' => floor($this->lhs['value'] / $this->rhs['value']), // TODO: assert if is number
        ];
    }

    private function rem(): array
    {
        return [
            'kind' => 'number',
            'value' => floor($this->lhs['value'] % $this->rhs['value']), // TODO: assert if is number
        ];
    }

    private function and(): array
    {
        return [
            'kind' => 'boolean',
            'value' => $this->lhs['value'] && $this->rhs['value'],
        ];
    }

    private function or(): array
    {
        return [
            'kind' => 'boolean',
            'value' => $this->lhs['value'] || $this->rhs['value'],
        ];
    }

    private function eq(): array
    {
        $validTypes = [ 'number', 'string', 'boolean' ];

        $leftIsNotValid = ! in_array($this->lhs['kind'], $validTypes);
        $rightIsNotValid = ! in_array($this->rhs['kind'], $validTypes);

        if ($leftIsNotValid || $rightIsNotValid) {
            throw new \Exception('Not valid types');
        }

        if ($this->lhs['kind'] !== $this->rhs['kind']) {
            return [
                'kind' => 'boolean',
                'value' => false,
            ];
        }

        return [
            'kind' => 'boolean',
            'value' => $this->lhs['value'] === $this->rhs['value'],
        ];
    }

    private function neq(): array
    {
        $validTypes = [ 'number', 'string', 'boolean' ];

        $leftIsNotValid = ! in_array($this->lhs['kind'], $validTypes);
        $rightIsNotValid = ! in_array($this->rhs['kind'], $validTypes);

        if ($leftIsNotValid || $rightIsNotValid) {
            throw new \Exception('Not valid types');
        }

        if ($this->lhs['kind'] !== $this->rhs['kind']) {
            return [
                'kind' => 'boolean',
                'value' => true,
            ];
        }

        return [
            'kind' => 'boolean',
            'value' => $this->lhs['value'] !== $this->rhs['value'],
        ];
    }

    private function lt(): array
    {
        if (($this->lhs['kind'] !== 'number') || ($this->rhs['kind'] !== 'number')) {
            throw new \Exception('Not valid types');
        }

        return [
            'kind' => 'boolean',
            'value' => $this->lhs['value'] < $this->rhs['value'],
        ];
    }

    private function gt(): array
    {
        if (($this->lhs['kind'] !== 'number') || ($this->rhs['kind'] !== 'number')) {
            throw new \Exception('Not valid types');
        }

        return [
            'kind' => 'boolean',
            'value' => $this->lhs['value'] > $this->rhs['value'],
        ];
    }

    private function lte(): array
    {
        if (($this->lhs['kind'] !== 'number') || ($this->rhs['kind'] !== 'number')) {
            throw new \Exception('Not valid types');
        }

        return [
            'kind' => 'boolean',
            'value' => $this->lhs['value'] <= $this->rhs['value'],
        ];
    }

    private function gte(): array
    {
        if (($this->lhs['kind'] !== 'number') || ($this->rhs['kind'] !== 'number')) {
            throw new \Exception('Not valid types');
        }

        return [
            'kind' => 'boolean',
            'value' => $this->lhs['value'] >= $this->rhs['value'],
        ];
    }

    public function interpret(): array
    {
        return match ($this->operator)
        {
            BinaryOperator::ADD => $this->add(),
            BinaryOperator::SUB => $this->sub(),
            BinaryOperator::MUL => $this->mul(),
            BinaryOperator::DIV => $this->div(),
            BinaryOperator::REM => $this->rem(),

            BinaryOperator::AND => $this->and(),
            BinaryOperator::OR => $this->or(),

            BinaryOperator::EQ => $this->eq(),
            BinaryOperator::NEQ => $this->neq(),

            BinaryOperator::LT => $this->lt(),
            BinaryOperator::GT => $this->gt(),
            BinaryOperator::LTE => $this->lte(),
            BinaryOperator::GTE => $this->gte(),
        };
    }
}