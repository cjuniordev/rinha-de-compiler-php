<?php

namespace RinhaDeCompilerPhp\Nodes;

class Location
{
    public function __construct(
        public int $start,
        public int $end,
        public string $filename,
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