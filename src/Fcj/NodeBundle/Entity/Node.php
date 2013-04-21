<?php

namespace Fcj\NodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity(repositoryClass="Fcj\NodeBundle\Repository\NodeRepository")
 * @ORM\InheritanceType("JOINED")
 *
 *
 * todo: repository + act as a factory & service layer ?
 */
class Node
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** Outbound arcs.
     *
     * @ORM\OneToMany(targetEntity="NodeSelf", mappedBy="from")
     */
    protected $to;

    // todo: $from ?

    /**
     * @ORM\OneToMany(targetEntity="NodeData", indexBy="symbol", mappedBy="node")
     */
    protected $data;


    /**
     */
    public function __construct()
    {
        $this->to = new ArrayCollection();
        $this->data = new ArrayCollection();
    }
}
