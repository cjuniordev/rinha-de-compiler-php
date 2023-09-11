<?php

namespace RinhaDeCompilerPhp;

use RinhaDeCompilerPhp\Terms\Binary;

class Interpreter
{
    private function handleIf(array $term, array &$environment): array
    {
        $condition = $this->interpret($term['condition'], $environment); // TODO: assert bool
        $branch = $condition['value'] ? $term['then'] : $term['otherwise'];

        return $this->interpret($branch, $environment);
    }

    private function handleBinary(array $term, array &$environment): array
    {
        $lhs = $this->interpret($term['lhs'], $environment);
        $rhs = $this->interpret($term['rhs'], $environment);

        return (new Binary($term['op'], $lhs, $rhs, null))
            ->interpret();
    }

    private function handleVar(array $term, array &$environment): mixed
    {
        $value = $environment['objects'][$term['text']] ?? null;

        if (! $value) {
            throw new \Exception("Variable '{$term['text']}' is not set");
        }

        return $value;
    }

    private function handleType(string $kind, array $term): array
    {
        return [
            'kind' => $kind,
            'value' => $term['value'],
        ];
    }

    private function handleCall(array $term, array &$environment): array
    {
        $function = $this->interpret($term['callee'], $environment)['value']; // TODO: assert this

        $countParameters = count($function['parameters']);
        $countArguments = count($term['arguments']);

        if ($countParameters !== $countArguments) {
            throw new \Exception("function expected {$countParameters} but {$countArguments} received.");
        }

        $scopedEnv = [
            'objects' => [
                ...$environment['objects'],
                ...$function['environment']['objects'],
            ]
        ];

        for ($i = 0; $i < $countParameters; $i++) {
            $parameter = $function['parameters'][$i];
            $scopedEnv['objects'][$parameter] = $this->interpret($term['arguments'][$i], $environment);
        }

        return $this->interpret($function['value'], $scopedEnv);
    }

    private function handleFunction(array $term, array $environment): array
    {
        $parameters = array_map(fn ($parameter) => $parameter['text'], $term['parameters']);

        return [
            'kind' => 'closure',
            'value' => [
                'parameters' => $parameters,
                'value' => $term['value'],
                'environment' => $environment,
            ]
        ];
    }

    private function handleLet(array $term, array &$environment): array
    {
        $scopedEnv = [ ...$environment ];

        $value = $this->interpret($term['value'], $environment);

        $scopedEnv['objects'][$term['name']['text']] = $value;

        return $this->interpret($term['next'], $scopedEnv);
    }

    private function handlePrint(array $term, array &$environment): array
    {
        $value = $this->interpret($term['value'], $environment);

        $result = match ($value['kind']) {
            'string' => $value['value'],
            'number' => (string) $value['value'],
            'boolean' => $value['value'] ? 'true' : 'false',
            'closure' => '<#closure>',
            default => '',
        };

        echo $result;

        return $value;
    }

    private function handleDefault(array $term, array &$environment)
    {
        var_dump([
            'who' => debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'],
            'term' => $term,
            'env' => $environment,
        ]);

        die();

        return [];
    }


    public function interpret(array $term, array &$environment): array
    {
        return match ($term['kind']) {
            'If' => $this->handleIf($term, $environment),
            'Binary' => $this->handleBinary($term, $environment),
            'Var' => $this->handleVar($term, $environment),
            'Int' => $this->handleType('number', $term),
            'Call' => $this->handleCall($term, $environment),
            'Function' => $this->handleFunction($term, $environment),
            'Let' => $this->handleLet($term, $environment),
            'Print' => $this->handlePrint($term, $environment),
            default => $this->handleDefault($term, $environment),
        };
    }
}