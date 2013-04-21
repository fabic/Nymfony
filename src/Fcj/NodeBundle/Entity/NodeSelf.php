<?php

namespace Fcj\NodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 *
 */
class NodeSelf // extends NodeData
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Node")
     */
    protected $from;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Node")
     */
    protected $to;
}
