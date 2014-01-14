<?php

namespace Wd\TreeBundle\Interfaces;

use Wd\TreeBundle\Interfaces\TreeInterface;

/**
 * TreeControllerInterface
 */
interface TreeControllerEditableInterface
{
    /**
     * Controller action to move a tree node
     *
     * @abstract
     *
     * @param int $node id of the node to move
     * @param int $ref  id of the reference node
     * @param int $move 1 to move after, 0 to move before the reference
     *
     * @return \Symfony\Component\HttpFoundation\Response must return a Response object with:
     *     "ok": everything worked
     *     "ko": there was a problem
     */
    function sortNodeAction($node, $ref, $move);

    /**
     * Controller action to move a tree node
     *
     * @abstract
     *
     * @param int $node id of the node to move
     *
     * @return \Symfony\Component\HttpFoundation\Response must return a Response object with:
     *     "ok": everything worked
     *     "ko": there was a problem
     */
    function addNodeAction($parent, $title);

    function removeNodeAction($node);

    function renameNodeAction($node, $title);
}
