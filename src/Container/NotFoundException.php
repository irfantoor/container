<?php
/**
 * IrfanTOOR\Container
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/container/blob/master/LICENSE (MIT License)
 * @link      https://github.com/irfantoor/container/src/NotFoundException.php
 */

namespace IrfanTOOR\Container;

use IrfanTOOR\Container\ContainerException;
use Psr\Container\NotFoundExceptionInterface;

/**
 * No entry was found in the container.
 */
class NotFoundException extends ContainerException
    implements NotFoundExceptionInterface
{
}
