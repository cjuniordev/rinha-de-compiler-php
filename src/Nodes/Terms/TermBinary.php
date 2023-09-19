<?php

namespace RinhaDeCompilerPhp\Nodes\Terms;

use Exception;
use RinhaDeCompilerPhp\Nodes\Enums\BinaryOperator;

class TermBinary extends Term
{
    public BinaryOperator $operator;

    public function __construct(
        BinaryOperator|string $operator,
        public Term $lhs,
        public Term $rhs,
    ) {
        $this->kind = 'Binary';

        if (is_string($operator)) {
            $operator = BinaryOperator::from($operator);
        }

        $this->operator = $operator;
    }

    private function convert(string $kind, bool|string|int $value): TermInt|TermStr|TermBool
    {
        return match ($kind) {
            'Int' => new TermInt($value),
            'Str' => new TermStr($value),
            'Bool' => new TermBool($value),
        };
    }

    private function bothAreInt(): bool
    {
        return $this->lhs->kind == 'Int' && $this->rhs->kind == 'Int';
    }

    private function bothAreBool(): bool
    {
        return $this->lhs->kind == 'Bool' && $this->rhs->kind == 'Bool';
    }

    private function add(): TermInt|TermStr
    {
        if ($this->bothAreInt()) {
            return $this->convert('Int', $this->lhs->value + $this->rhs->value);
        }

        $lhs = $this->lhs->value; // To String
        $rhs = $this->rhs->value; // To String

        return $this->convert('Str', $lhs . $rhs);
    }

    /**
     * @throws Exception
     */
    private function sub(): TermInt
    {
        if (! $this->bothAreInt()) {
            throw new Exception('Cannot sub NaN');
        }

        return $this->convert('Int', $this->lhs->value - $this->rhs->value);
    }

    /**
     * @throws Exception
     */
    private function mul(): TermInt
    {
        if (! $this->bothAreInt()) {
            throw new Exception('Cannot mul NaN');
        }

        return $this->convert('Int', $this->lhs->value * $this->rhs->value);
    }

    /**
     * @throws Exception
     */
    private function div(): TermInt
    {
        if (! $this->bothAreInt()) {
            throw new Exception('Cannot div NaN');
        }

        return $this->convert('Int', intdiv($this->lhs->value, $this->rhs->value));
    }

    /**
     * @throws Exception
     */
    private function rem(): TermInt
    {
        if (! $this->bothAreInt()) {
            throw new Exception('Cannot rem NaN');
        }

        return $this->convert('Int', $this->lhs->value % $this->rhs->value);
    }

    /**
     * @throws Exception
     */
    private function and(): TermBool
    {
        if (! $this->bothAreBool()) {
            throw new Exception('Cannot \'AND\' not booleans');
        }

        return $this->convert('Bool', $this->lhs->value && $this->rhs->value);
    }

    /**
     * @throws Exception
     */
    private function or(): TermBool
    {
        if (! $this->bothAreBool()) {
            throw new Exception('Cannot \'AND\' not booleans');
        }

        return $this->convert('Bool', $this->lhs->value || $this->rhs->value);
    }

    private function eq(): TermBool
    {
        $haveTheSameTypes = $this->lhs->kind === $this->rhs->kind;
        $haveEqualValues = $this->lhs->value === $this->rhs->value;

        return $this->convert('Bool', $haveTheSameTypes && $haveEqualValues);
    }

    private function neq(): TermBool
    {
        $haveTheSameTypes = $this->lhs->kind === $this->rhs->kind; // false
        $haveEqualValues = $this->lhs->value === $this->rhs->value; // true

        if (! $haveTheSameTypes) {
            return $this->convert('Bool', true);
        }

        return $this->convert('Bool', ! $haveEqualValues);
    }

    /**
     * @throws Exception
     */
    private function lt(): TermBool
    {
        if (! $this->bothAreInt()) {
            throw new Exception('Not valid types');
        }

        return $this->convert('Bool', $this->lhs->value < $this->rhs->value);
    }

    /**
     * @throws Exception
     */
    private function gt(): TermBool
    {
        if (! $this->bothAreInt()) {
            throw new Exception('Not valid types');
        }

        return $this->convert('Bool', $this->lhs->value > $this->rhs->value);
    }

    /**
     * @throws Exception
     */
    private function lte(): TermBool
    {
        if (! $this->bothAreInt()) {
            throw new Exception('Not valid types');
        }

        return $this->convert('Bool', $this->lhs->value <= $this->rhs->value);
    }

    /**
     * @throws Exception
     */
    private function gte(): TermBool
    {
        if (! $this->bothAreInt()) {
            throw new Exception('Not valid types');
        }

        return $this->convert('Bool', $this->lhs->value >= $this->rhs->value);
    }

    /**
     * @throws Exception
     */
    public function interpret(): TermInt|TermStr|TermBool
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