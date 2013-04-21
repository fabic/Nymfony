<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentPart : Association class for composited (OneToMany) relation Content(1)->Content(*).
 * Defines a tree of Contents.
 *
 * todo: Unique constraint on tuple {parent, child, name} ?
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ContentPart
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Content
     *
     * @ ORM\Id
     * @ORM\ManyToOne(targetEntity="Content")
     */
    protected $parent;

    /**
     * @var Content
     *
     * @ ORM\Id
     * @ORM\OneToOne(targetEntity="Content")
     */
    protected $child;

    /**
     * @var Content
     *
     * @ ORM\Id
     * @ORM\Column(type="string", length=64)
     */
    protected $name;

    /**
     * @param Content $parent
     * @param Content $child
     * @param string  $name The name of the child content within the context
     *                      defined by the parent's specification.
     */
    public function __construct(Content $parent, Content $child, $name)
    {
        $this->parent = $parent;
        $this->child  = $child;
        $this->name   = $name;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
