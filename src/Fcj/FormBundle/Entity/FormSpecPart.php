<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * FormSpecPart : Association class for aggregated relation FormSpec->FormSpec.
 * Permits having a hierarchy of FormSpecs.
 *
 * « Resisting the urge to use some sort of EAV impl. for dealing with attributes...,
 *   at this moment... »
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FormSpecPart implements \ArrayAccess
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var FormSpec
     *
     * @ORM\ManyToOne(targetEntity="FormSpec")
     * @ ORM\Id
     */
    protected $from;

    /**
     * @var FormSpec
     *
     * @ORM\ManyToOne(targetEntity="FormSpec")
     * @ ORM\Id
     */
    protected $to;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     * @ ORM\Id
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $label = NULL;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $required = false;

    // todo: empty_data, empty_value ?
    // todo: by_reference ?

    /// COLLECTION related stuff
    /// todo: have things like at_most 3, at_least 2...

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $collection = false;

    /// Custom thing for embedding stuff.

    /** todo: Impl.; true if part data shall be embedded in the same storage "unit"
     *  todo.. as the parent form spec., e.g. the same db table.
     *  todo.. Possible for "identifying" relations, but not for one-to-many.
     *
     *  NOTE: This might really be likewise to the Symfony's 'virtual' form option (?)
     *        See http://symfony.com/doc/current/cookbook/form/use_virtuals_forms.html
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $embedded = false;
    //protected $virtual = false;

    // todo/idea:
    //   protected $separator = '_';
    //   Or: Camel-cased embedding ?
    //   E.g. contactPerson_name versus contactPersonName

    /**
     * @var ArrayCollection<ExtraData>
     * Note: Composited relation; ExtraData instance *belong* to their FormSpecPart.
     * @ ORM\OneToMany(targetEntity="ExtraData", mappedBy="part", indexBy="name",
     *    cascade="all", orphanRemoval=true
     * )
     *
     * FIXME : Clean up ^
     *
     * @ORM\Column(type="array", nullable=false)
     */
    protected $extra;

    /**
     * @ORM\Column(type="array", nullable=false)
     */
    protected $options;

    /**
     * @ORM\Column(type="array", nullable=false)
     */
    protected $pass;

    /** ctor
     *
     * @param FormSpec $from
     * @param FormSpec $to
     * @param string $name
     */
    public function __construct(FormSpec $from, FormSpec $to=NULL, $name='')
    {
        $this->from = $from;
        $this->to   = $to;
        $this->name = $name;
        $this->extra   = array();
        $this->options = array();
        $this->pass    = array();
    }

    public function getId()   { return $this->id; }
    public function getName() { return $this->name; }
    public function getFrom() { return $this->from; }
    public function getTo()   { return $this->to; }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param boolean $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @param boolean $collection
     * @param string|bool $prototypeName
     * @param bool $allowAdd
     * @param bool $allowDelete
     * @return $this
     */
    public function setCollection($collection, $prototypeName=true,
                                  $allowAdd=true, $allowDelete=true)
    {
        $this->collection = (bool) $collection;
        if( $this->collection ) {
            if ($prototypeName===true) // todo: transl. name to be alnum
                $prototypeName = "__" . $this->name . "__";
            //$this->extra['prototype']      = false!=$prototypeName;
            //$this->extra['prototype_name'] = $prototypeName;
            //$this->extra['allow_add']      = $allowAdd;
            //$this->extra['allow_delete']   = $allowDelete;
            //$this->extra['at_least'] = 1; // todo: impl.
            //$this->extra['at_most']  = 0;
        }
        else if (FALSE) // Fixme: Yes/no (no for keeping user param) ?
            unset(
                $this->extra['prototype'], $this->extra['prototype_name'],
                $this->extra['allow_add'], $this->extra['allow_delete']
            );
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return boolean
     */
    public function isCollection()
    {
        return $this->collection;
    }

    /**
     * @return boolean
     */
    public function isEmbedded()
    {
        return $this->embedded || $this->to->isEmbedded();
    }

    /**
     * @param $embedded
     * @return boolean
     */
    public function setEmbedded($embedded)
    {
        $this->embedded = (bool) $embedded;
        return $this;
    }

    public function setExtra(Array $extra, $merge=true)
    {
        $this->extra = array_merge($extra, $merge ? $this->extra : array());
        return $this;
    }

    public function setOptions(Array $options, $merge=true)
    {
        $this->options = array_merge($options, $merge ? $this->options : array());
        return $this;
    }

    public function setPass($pass, $merge=true)
    {
        $this->pass = array_merge($pass, $merge ? $this->pass : array());
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


    public function getPass()
    {
        return $this->pass;
    }



    // todo: getExtraData() : ArrayCollection<ExtraData> ? AND/OR:
    // todo: get($key) : ExtraData / Mixed ?

    /// \ArrayAccess impl. on $this->extra.

    /**
     */
    public function offsetExists($offset)
    {
        return $this->extra[$offset];
    }

    /**
     * @param mixed $offset
     * @return ExtraData
     */
    public function offsetGet($offset)
    {
        return $this->extra[$offset];
    }

    /**
     * @param mixed $offset
     * @param ExtraData $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->extra[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset( $this->extra[$offset] );
    }

    /// End of \ArrayAccess impl.
}
