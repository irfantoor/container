<?php

class Provider
{
    /**
     * Note: Container::init requires an associative array [string $k => $value, ...]
     * @return array Array of bad arguments to initialize a Container
     */
    static function badInitArgs()
    {
        return [
            'hello',
            null,
            0,
            new stdClass(),
            true,
            false,
            function ($c) {},
        ];
    }

    /**
     * Note: Container::init requires an associative array [string $k => $value, ...]
     * @return array A sane associative array to initialize a Container
     */
    static function goodInitArgs()
    {
        return [
            'hello'   => 'world',
            'class'   => new stdClass(),
            'bool'    => true,
            'false'   => false,
            'int'     => 1,
            'float'   => 2.0,
            'array'   => [],
            'array'   => [1,2,3],
            'closure' =>
                function ()
                {
                    return 'closure';
                },
        ];
    }

    /**
     * Note: Container::init requires an associative array [string $k => $value, ...]
     * @return array Array of bad key values
     */
    static function badKeys()
    {
        return [
            null,
            0,
            new stdClass(),
            true,
            false,
            [],
            ['hello' => 'world'],
            ['hello'],
            '',
        ];
    }

    static function goodKeys()
    {
        return [
            'null',
            '0',
            'new StdClass()',
            'true',
            'false',
            '{}',
            '[]',
            '()',
            '.',
            ';',
            ':',
            '\\',
            'hello',
            'hello ',
            'hello.test',
            'Hello',
            'world',
        ];
    }
}
