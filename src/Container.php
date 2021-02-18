<?php

/**
 * IrfanTOOR\Container
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR;

use Exception;
use IrfanTOOR\Container\{AbstractContainer, NotFoundException, ContainerException};
use IrfanTOOR\Container\Adapter\{AdapterInterface, ArrayAdapter};
use Psr\Container\ContainerInterface;
use Throwable;

# =========================================================================== #
# psr11 compliance: get, has
# =========================================================================== #

/**
 * Container that exposes methods to read its entries.
 */
class Container extends AbstractContainer implements ContainerInterface
{
    const NAME        = "Irfan's Container";
    const DESCRIPTION = "Simple container to contain/create your objects and data";
    const VERSION     = "1.2";

    /**
     * Container constructor
     *
     * @param array $init Associative array [string $id => $entry, ...]
     * @throws ContainerException Identifier can only be a non empty string.
     */
    function __construct($init = [], ?AdapterInterface $adapter = null)
    {
        if (!is_array($init))
            throw new ContainerException("Container can be initialized with an associative array only");

        if (!$adapter)
            $adapter = new ArrayAdapter();

        # initialize the storage
        $this->initAdapter($adapter);

        # initialize with the data
        $this->init($init);
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $this->validate($id);
        $this->assertHas($id);

        try {
            return $this->getItem($id);
        } catch (Throwable $e) {
            throw new ContainerException("Error while retrieving the entry.");
        }
    }

    /**
     * @inheritdoc
     */
    public function has($id)
    {
        try {
            if (!$this->isValid($id))
                return false;

            return $this->hasItem($id);
        } catch (Throwable $e) {
            return false;
        }
    }
}
