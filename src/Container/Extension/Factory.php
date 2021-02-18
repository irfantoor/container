<?php

/**
 * IrfanTOOR\Container\Extension\Factory
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR\Container\Extension;

use Exception;

class Factory
{
    /** @var array -- Schema of name|interface to class mappings */
    protected $schema = [];
    protected $classes = [];

    /**
     * Factory constructor
     */
    public function __construct(array $init = [])
    {
        foreach ($init as $i => $c)
            $this->factory($i, $c);
    }

    # Strips the namespace from an interface name
    protected function stripNamespace(string $full_classname): string
    {
        $array = explode('\\', $full_classname);
        return array_pop($array);
    }

    /**
     * Registers a class against a name
     *
     * @param string $name                 Name of Interface, or Class
     * @param string|closure|object $class Fullname of class, Closure or Object
     */
    public function factory(string $name, $class)
    {
        $name = $this->stripNamespace($name);
        $this->schema[$name] = $class;

        if (is_string($class)) {
            $c = $this->stripNamespace($class);
            $this->classes[$c] = $name;
        }
    }

    /**
     * Attempts to create the class, trying to auto feed the dependencies
     *
     * @param string $name Name of Interface, or Class
     * @param array $args  Arguments required for creation
     *
     * @return object
     */
    public function create(string $name, array $args = [])
    {
        if (isset($this->schema[$name])) {
            $class = $this->schema[$name];
        } elseif(isset($this->classes[$name])) {
            $class = $this->schema[$this->classes[$name]];
        } else {
            $short = $this->stripNamespace($name);
            
            if (isset($this->schema[$short])) {
                $class = $this->schema[$short];
            } elseif(isset($this->classes[$short])) {
                $class = $this->schema[$this->classes[$short]];
            }
        }

        if (!$class)
            throw new Exception("Definition of the class not found");
        
        if (is_string($class))
            return new $class($args);

        if (is_object($class)) {
            if (method_exists($class, 'init'))
                $class->init($args);

            return $class;
        }
        
        throw new Exception("Class was not created ...");
    }
}
