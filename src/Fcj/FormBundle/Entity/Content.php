<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Content : Holds actual user-provided form data.
 *
 * Tree structure that is defined by a FormSpec hierarchy.
 *
 * @ORM\Table("fcj_content")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *      "dbal"  = "ContentDBAL",
 *      "array" = "ContentArray"
 * })
 */
class Content
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var FormSpec The FormSpec that defines this Content's structure.
     *
     * @ORM\ManyToOne(targetEntity="FormSpec")
     */
    protected $spec;

    /**
     * @var FormSpecPart The related part.
     *
     * @ORM\ManyToOne(targetEntity="FormSpecPart")
     */
    protected $part;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $name;

    /**
     * @var Content
     * @ORM\OneToOne(targetEntity="ContentPart", mappedBy="child")
     */
    protected $parent;

    /**
     * @var ArrayCollection<Content> indexed by Content::name.
     *
     * @ORM\OneToMany(targetEntity="ContentPart", mappedBy="parent", indexBy="name")
     */
    protected $children;


    /**
     * @param FormSpec $formSpec
     * @param Content $parent
     */
    public function __construct(FormSpec $spec = null, FormSpecPart $part=NULL, Content $parent=NULL)
    {
        $this->spec   = $spec;
        $this->part   = $part;
        $this->parent = $parent;
        $this->name   = $part ? $part->getName() : '';
        if( $parent )
            $this->parent->addChild( $this );
        $this->children = new ArrayCollection();
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

    /**
     * @return FormSpec
     */
    public function getFormSpec()
    {
        return $this->part->getTo();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function addChild(self $child)
    {
        $name = $child->getName();
        if ($name)
             $this->children->set($name, $child);
        else $this->children->add($child);
    }

    //
    // Magic stuff (default impl.)
    //

    /**
     * @param $name
     * @return mixed
     * @throws \OutOfBoundsException
     */
    function __get($name)
    {
        if (!property_exists($this, $name))
            throw new \OutOfBoundsException(get_class($this) . " has no property named `$name'.");
        return $this->$name;

    }

    /**
     * @param $name
     * @param $value
     * @return $this
     * @throws \OutOfBoundsException
     */
    function __set($name, $value)
    {
        if (!property_exists($this, $name))
            throw new \OutOfBoundsException(get_class($this) . " has no property named `$name'.");
        //if ($value instanceOf self)
        $this->$name = $value;
        return $this;
    }


}
