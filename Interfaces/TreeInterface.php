<?php

namespace Wd\TreeBundle\Interfaces;

/**
 * Interface for trees
 */
interface TreeInterface
{
    /**
     * returns a in iteratable object of children
     *
     * @abstract
     * @return mixed
     */
    function getChildren();
}
