<?php
/**
 * IrfanTOOR\Container
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/container/blob/master/LICENSE (MIT License)
 * @link      https://github.com/irfantoor/container/tests/Service.php
 */
class Service
{
    public $id;

    function __construct($value)
    {
        $this->id = $value;
    }

    function __invoke()
    {
        return $this->id;
    }
}
