<?php

namespace RinhaDeCompilerPhp\Terms;

class Location
{
    public function __construct(
        private int $start,
        private int $end,
        private string $filename,
    ) {}

    public static function getInstanceByArray(array $location): Location
    {
        return (new Location(
            $location['start'],
            $location['end'],
            $location['filename']
        ));
    }
}