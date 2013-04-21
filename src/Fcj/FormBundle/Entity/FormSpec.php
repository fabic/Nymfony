<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Fcj\FormBundle\Util\FormSpecIterator;
use Fcj\FormBundle\FormSpecFactory;

/**
 * FormSpec
 *
 * @ORM\Table()
 * @ORM\InheritanceType("JOINED")
 * @ ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *      "spec"   = "FormSpec",
 *      "scalar" = "FormSpecScalar"
 * })
 * @ORM\Entity(repositoryClass="Fcj\FormBundle\Repository\FormSpecRepository")
 *
 * todo/idea: func. forEachPart(closure(FormSpecPart $part);
 * todo/idea: Having a tree-traversal (visitor pattern, depth-first/breadth-first) thing ?
 *    » With ability to provide a closure for local node operations ?
 * todo: Embedding extra data.
 */
class FormSpec implements \IteratorAggregate, \ArrayAccess
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
     * @var string === « type »
     *
     * @ORM\Column(type="string", length=64)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $extends;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $embedded = false;

    /**
     * @ORM\Column(type="array", nullable=false)
     */
    protected $extra = array();

    /**
     * @ORM\Column(type="array", nullable=false)
     */
    protected $options = array();


    /**
     * @var ArrayCollection<FormSpecPart> indexed by FormSpecPart::name.
     *
     * Note: Aggregation; FormSpecs may be shared.
     *
     * @ORM\OneToMany(targetEntity="FormSpecPart", mappedBy="from", indexBy="name",
     *                cascade={"all"}, orphanRemoval=true)
     */
    protected $parts;

    /**
     * @var ArrayCollection<Content> indexed by Content::id.
     * @ORM\OneToMany(targetEntity="Content", mappedBy="formSpec", indexBy="id")
     */
    protected $contents;


    /**
     * @var FormSpecFactory
     */
    //protected $factory;


    /** ctor
     *
     */
    public function __construct($name)
    {
        $this->name     = $name;
        $this->parts    = new ArrayCollection();
        $this->contents = new ArrayCollection();
    }

    /** For the factory to inject itself, for ContentType needs to guess
     *  if we know about a type or not.
     *
     * @param FormSpecFactory $factory
     * @return $this
     */
//    public function setFactory(FormSpecFactory $factory)
//    {
//        $this->factory = $factory;
//        return $this;
//    }

    /**
     * @return \Fcj\FormBundle\FormSpecFactory
     */
    public function getFactory()
    {
        return $this->factory;
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
     * Set name
     *
     * @param string $name
     * @return FormSpec
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $extends
     * @return $this
     */
    public function setExtends($extends)
    {
        $this->extends = (string) $extends;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtends()
    {
        return $this->extends;
    }



    /**
     * @return ArrayCollection<FormSpec>
     */
    public function getParts()
    {
        return $this->parts;
    }

    public function has($partName)
    {
        return $this->parts->containsKey($partName);
    }

    /**
     * @param $partName
     * @return FormSpecPart|null
     */
    public function get($partName)
    {
        if (! $this->has($partName))
            return null;
        return $this->parts->get($partName);
    }

    /**
     * @param FormSpecPart $part
     * @return $this
     */
    public function addPart(FormSpecPart $part)
    {
        $this->parts->set($part->getName(), $part);
        return $this;
    }

    /**
     * @return bool True is this FormSpec has parts (hence if it is made of sub-FormSpecs).
     */
    public function isCompound()
    {
        return !$this->parts->isEmpty();
    }

    /**
     * @return bool True if we're non-compound.
     */
    public function isLeaf()
    {
        return !$this->isCompound(); // || $this->embedded; // fixme: think about that.
    }

    /** A bare form spec. node would be one that we only know about the name (i.e. type).
     *
     * @return bool
     */
    public function isBare()
    {
        return $this->isLeaf() && !$this->extends;
    }

    /** fixme: unused; Why have this? can't recall...
     *
     * @return bool True if this form spec. is either not compound, *or* made
     *    of non-compound children FormSpecs; hence node is either a leaf, or
     *    an internal node one level above leafs.
     *
     * Note: O(N) where N is the number of (direct) children parts.
     */
    public function isFloor()
    {
        if ($this->isLeaf())
            return true;
        // Else:
        foreach($this->parts AS $part) {
            if (!$part->getTo()->isLeaf())
                return false;
        }
        return true;
    }

    /**
     * @param boolean $embedded
     * @return $this
     */
    public function setEmbedded($embedded)
    {
        $this->embedded = (bool) $embedded;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmbedded()
    {
        return $this->embedded;
    }

    public function setExtra(Array $extra, $merge=true)
    {
        $this->extra = $merge ?
              array_merge($extra, $this->extra)
            : $extra;
        return $this;
    }

    public function setOptions(Array $options, $merge=true)
    {
        $this->options = $merge ?
              array_merge($options, $this->options)
            : $options;
        return $this;
    }

    public function getExtra()
    {
        return $this->extra;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /** Impl. for \IteratorAggregate.
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     *    <b>Traversable</b>
     */
    public function getIterator()
    {
        return new FormSpecIterator($this);
    }

    /// \ArrayAccess impl. on $this->parts.


    /**
     */
    public function offsetExists($offset)
    {
        return $this->parts->offsetExists($offset);
    }

    /**
     * @param mixed $offset
     * @return ExtraData
     */
    public function offsetGet($offset)
    {
        return $this->parts->offsetGet($offset);
    }

    /**
     * @param mixed $offset
     * @param FormSpecPart $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        assert($value instanceOf FormSpecPart);
        $this->parts->offsetSet($offset, $value);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->parts->offsetUnset($offset);
    }

    /// End of \ArrayAccess impl.
}
