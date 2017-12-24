<?php
/**
 * IrfanTOOR\Container
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/container/blob/master/LICENSE (MIT License)
 * @link      https://github.com/irfantoor/container/src/ContainerException.php
 */

namespace IrfanTOOR\Container;

use Exception as BaseException;
use Psr\Container\ContainerExceptionInterface;

/**
 * A generic exception in a container.
 */
class ContainerException extends BaseException
    implements ContainerExceptionInterface
{
}
