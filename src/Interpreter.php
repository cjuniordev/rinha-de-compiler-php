<?php

namespace RinhaDeCompilerPhp;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use RinhaDeCompilerPhp\Nodes\Terms\Term;
use RinhaDeCompilerPhp\Nodes\Terms\TermBinary;
use RinhaDeCompilerPhp\Nodes\Terms\TermBool;
use RinhaDeCompilerPhp\Nodes\Terms\TermCall;
use RinhaDeCompilerPhp\Nodes\Terms\TermClosure;
use RinhaDeCompilerPhp\Nodes\Terms\TermFunction;
use RinhaDeCompilerPhp\Nodes\Terms\TermIf;
use RinhaDeCompilerPhp\Nodes\Terms\TermInt;
use RinhaDeCompilerPhp\Nodes\Terms\TermLet;
use RinhaDeCompilerPhp\Nodes\Terms\TermStr;
use RinhaDeCompilerPhp\Nodes\Terms\TermVar;

class Interpreter
{
    private array $cache = [];

    /**
     * @throws Exception
     */
    private function handleIf(TermIf $term, array &$scope): Term
    {
        $condition = $term->condition;

        if (! $condition instanceof TermBool) {
            $condition = $this->interpret($condition, $scope);
        }

        $branch = $condition->value ? $term->then : $term->otherwise;

        return $this->interpret($branch, $scope);
    }

    /**
     * @throws Exception
     */
    private function handleBinary(TermBinary $term, array &$scope): TermInt|TermStr|TermBool
    {
        $lhs = $this->interpret($term->lhs, $scope);
        $rhs = $this->interpret($term->rhs, $scope);

        return (new TermBinary($term->operator, $lhs, $rhs))
            ->interpret();
    }

    /**
     * @throws Exception
     */
    private function handleVar(TermVar $term, array &$scope): mixed
    {
        $value = $scope[$term->text] ?? null;

        if (! $value) {
            throw new Exception("Variable '{$term->text}' is not set");
        }

        return $value;
    }

    private function handleInt(TermInt $term): TermInt
    {
        return $term;
    }

    private function handleStr(TermStr $term): TermStr
    {
        return $term;
    }

    private function handleBool(TermBool $term): TermBool
    {
        return $term;
    }

    /**
     * @throws Exception
     */
    private function handleCall(TermCall $term, array &$scope): Term
    {
        $closure = $this->interpret($term->callee, $scope);

        if (! $closure instanceof TermClosure) {
            throw new Exception('Invalid closure call');
        }

        $countParameters = count($closure->parameters);
        $countArguments = count($term->arguments);

        if ($countParameters !== $countArguments) {
            throw new Exception("The function expected {$countParameters} arguments, but received {$countArguments}.");
        }

        $scoped = [
            ...$scope,
            ...$closure->scope,
        ];

        // TODO: improve this memoize
        $signature = '';

        for ($i = 0; $i < $countParameters; $i++) {
            $parameter = $closure->parameters[$i];
            $scoped[$parameter->text] = $this->interpret($term->arguments[$i], $scope);
            $signature .= $scoped[$parameter->text]->value ?? '';
        }

        $signature = $term->callee->text . $signature;

        if (isset($this->cache[$signature])) {
            return $this->cache[$signature];
        }

        $return = $this->interpret($closure->value, $scoped);

        $this->cache[$signature] = $return;

        return $return;
    }

    private function handleFunction(TermFunction $term, array $scope): TermClosure
    {
        return new TermClosure($term->parameters, $term->value, $scope);
    }

    /**
     * @throws Exception
     */
    private function handleLet(TermLet $term, array &$scope): Term
    {
        $scoped = [ ...$scope ];

        $value = $this->interpret($term->value, $scope);
        $scoped[$term->name->text] = $value;

        return $this->interpret($term->next, $scoped);
    }

    /**
     * @throws Exception
     */
    private function handlePrint(Term $term, array &$scope): Term
    {
        if (
            (! $term instanceof TermStr) ||
            (! $term instanceof TermInt) ||
            (! $term instanceof TermBool) ||
            (! $term instanceof TermClosure)
        ) {
            if (!empty($term->value)) {
                $term = $this->interpret($term->value, $scope);
            }
        }

        $result = match ($term->kind) {
            'Str' => $term->value,
            'Int' => (string) $term->value,
            'Bool' => $term->value ? 'true' : 'false',
            'Closure' => '<#closure>',
            'Tuple' => '(term, term)',
            default => '',
        };

        echo $result . PHP_EOL;

        return $term;
    }

    #[NoReturn] private function handleDefault(array $term, array &$scope): void
    {
        var_dump([
            'who' => debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'],
            'term' => $term,
            'env' => $scope,
        ]);

        die();
    }


    /**
     * @throws Exception
     */
    public function interpret(Term $term, array &$scope): Term
    {
        return match ($term->kind) {
            'If' => $this->handleIf($term, $scope),
            'Binary' => $this->handleBinary($term, $scope),
            'Var' => $this->handleVar($term, $scope),
            'Str' => $this->handleStr($term),
            'Int' => $this->handleInt($term),
            'Bool' => $this->handleBool($term),
            'Call' => $this->handleCall($term, $scope),
            'Function' => $this->handleFunction($term, $scope),
            'Let' => $this->handleLet($term, $scope),
            'Print' => $this->handlePrint($term, $scope),
            default => $this->handleDefault($term, $scope),
            // Tuple
            // First
            // Second
        };
    }
}