<?php

namespace Fcj\NodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 *
 */
class NodeData
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Node")
     */
    protected $node;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=64)
     */
    protected $symbol;
}
