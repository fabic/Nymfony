<?php
/**
 */

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * ContentArray
 *
 * @ORM\Entity()
 *
 * @package Fcj\FormBundle\Entity
 */
class ContentArray extends Content
{
    /**
     * @var array
     * @ORM\Column(type="array", nullable=false)
     */
    protected $data;

    /**
     * @param FormSpec $formSpec
     * @param Content $parent
     */
    public function __construct(FormSpec $formSpec=NULL, Content $parent=NULL)
    {
        parent::__construct($formSpec, $parent);
        $this->data = array();
    }

    //
    // Magic stuff (custom $this->data impl.)
    //

    /**
     * @param $name
     * @return mixed
     */
    function __get_OLD_IMPL($name)
    {
        if (! array_key_exists($name, $this->data))
            $this->data[$name] = NULL;
        //    throw new \OutOfBoundsException(get_class($this) . "::data has no key named `$name'.");
        return $this->data[$name];

    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        if ($this->children->containsKey($name))
            return $this->children->get($name);
        // Else :
        $part = $this->part ? $this->part->get($name) : null;
        $to   = $part       ? $part->getTo()          : null;
        if ($part && $to->isCompound()) {
            $subContent = new self($to, $this);
        }
        // Else :
        if (! array_key_exists($name, $this->data))
            $this->data[$name] = NULL;
        return $this->data[$name];
    }

    /**
     * @param $name
     * @param $value
     * @return $this (??)
     */
    function __set($name, $value)
    {
        if (! array_key_exists($name, $this->data))
            error_log(__METHOD__ . ": WARNING: Key '$name' didn't exist until now!");
        //    throw new \OutOfBoundsException(get_class($this) . " has no property named `$name'.");
        $part = $this->formSpec->get($name);
        $to   = $part ? $part->getTo() : null;
        if ($part && !$to->isLeaf()) {
            $subContent = new self($to, $this);
            $subContent->$name($value);
            $this->data[$name] = $subContent;
        }
        else
            $this->data[$name] = $value;
        return $this;
    }


}