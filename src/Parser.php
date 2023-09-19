<?php

namespace RinhaDeCompilerPhp;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use RinhaDeCompilerPhp\Nodes\File;
use RinhaDeCompilerPhp\Nodes\Parameter;
use RinhaDeCompilerPhp\Nodes\Terms\Term;
use RinhaDeCompilerPhp\Nodes\Terms\TermBinary;
use RinhaDeCompilerPhp\Nodes\Terms\TermBool;
use RinhaDeCompilerPhp\Nodes\Terms\TermCall;
use RinhaDeCompilerPhp\Nodes\Terms\TermFirst;
use RinhaDeCompilerPhp\Nodes\Terms\TermFunction;
use RinhaDeCompilerPhp\Nodes\Terms\TermIf;
use RinhaDeCompilerPhp\Nodes\Terms\TermInt;
use RinhaDeCompilerPhp\Nodes\Terms\TermLet;
use RinhaDeCompilerPhp\Nodes\Terms\TermPrint;
use RinhaDeCompilerPhp\Nodes\Terms\TermSecond;
use RinhaDeCompilerPhp\Nodes\Terms\TermStr;
use RinhaDeCompilerPhp\Nodes\Terms\TermTuple;
use RinhaDeCompilerPhp\Nodes\Terms\TermVar;

class Parser
{
    /**
     * @throws Exception
     */
    public function parse(string $path): File
    {
        $fullPath = __DIR__ . '/../files/' . $path;

        if (! file_exists($fullPath)) {
            throw new Exception("File not found in '/files'\n");
        }

        $explodedPath = explode('.', $path);
        $extension = end($explodedPath);

        if (! in_array($extension, ['json', 'rinha'])) {
            throw new Exception('Cannot run this file.');
        }

        if ($extension === 'rinha') {
            $output = null;
            exec("./parser/rinha {$fullPath}", $output);
            $rawFile = $output[0];
        }
        else {
            $rawFile = file_get_contents($fullPath);
        }

        $json = json_decode($rawFile, true);
        $expression = $json['expression'] ?? null;

        if (is_null($expression)) {
            throw new Exception('Invalid expression.');
        }

        return new File($this->handleTerm($expression));
    }

    private function handleParameter(array $term): Parameter
    {
        return new Parameter($term['text']);
    }

    private function handleLet(array $term): TermLet
    {
        // TODO: use multi-thread or processes
        $name = $this->handleParameter($term['name']);
        $value = $this->handleTerm($term['value']);
        $next = $this->handleTerm($term['next']);

        return new TermLet($name, $value, $next);
    }

    private function handleIf(array $term): TermIf
    {
        $condition = $this->handleTerm($term['condition']);
        $then = $this->handleTerm($term['then']);
        $otherwise = $this->handleTerm($term['otherwise']);

        return new TermIf($condition, $then, $otherwise);
    }

    private function handleBinary(array $term): TermBinary
    {
        $lhs = $this->handleTerm($term['lhs']);
        $rhs = $this->handleTerm($term['rhs']);

        return new TermBinary($term['op'], $lhs, $rhs);
    }

    private function handleVar(array $term): TermVar
    {
        return new TermVar($term['text']);
    }

    private function handleCall(array $term): TermCall
    {
        $callee = $this->handleTerm($term['callee']);
        $arguments = [];

        foreach ($term['arguments'] as $argument) {
            $arguments[] = $this->handleTerm($argument);
        }

        return new TermCall($callee, $arguments);
    }

    private function handleFunction(array $term): TermFunction
    {
        $value = $this->handleTerm($term['value']);
        $parameters = [];

        foreach ($term['parameters'] as $parameter) {
            $parameters[] = $this->handleParameter($parameter);
        }

        return new TermFunction($parameters, $value);
    }

    private function handlePrint(array $term): TermPrint
    {
       return new TermPrint($this->handleTerm($term['value']));
    }

    private function handleStr(array $term): TermStr
    {
        return new TermStr($term['value']);
    }

    private function handleInt(array $term): TermInt
    {
        return new TermInt($term['value']);
    }

    private function handleBool(array $term): TermBool
    {
        return new TermBool($term['value']);
    }

    private function handleTuple(array $term): TermTuple
    {
        $first = $this->handleTerm($term['first']);
        $second = $this->handleTerm($term['second']);

        return new TermTuple($first, $second);
    }

    private function handleFirst(array $term): TermFirst
    {
        return new TermFirst($this->handleTerm($term['value']));
    }

    private function handleSecond(array $term): TermSecond
    {
        return new TermSecond($this->handleTerm($term['value']));
    }

    #[NoReturn] private function handleDefault($term): void
    {
        var_dump([
            'who' => debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,2)[1]['function'],
            'term' => $term,
        ]);

        die();
    }

    private function handleTerm($term): Term
    {
        return match ($term['kind']) {
            'If' => $this->handleIf($term),
            'Binary' => $this->handleBinary($term),
            'Var' => $this->handleVar($term),
            'Call' => $this->handleCall($term),
            'Function' => $this->handleFunction($term),
            'Let' => $this->handleLet($term),
            'Print' => $this->handlePrint($term),
            'Str' => $this->handleStr($term),
            'Int' => $this->handleInt($term),
            'Bool' => $this->handleBool($term),
            'Tuple' => $this->handleTuple($term),
            'First' => $this->handleFirst($term),
            'Second' => $this->handleSecond($term),
            default => $this->handleDefault($term),
        };
    }
}