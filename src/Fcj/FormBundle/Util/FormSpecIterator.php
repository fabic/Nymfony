<?php
/**
 */

namespace Fcj\FormBundle\Util;

use Fcj\FormBundle\Entity\FormSpec;

/**
 * Class FormSpecIterator
 * @package Fcj\FormBundle\Util
 *
 * todo: That thing exists so as to someday have a means by which
 *       we can iterate over form spec. parts "in order" (currently
 *       form spec. children are not ordered).
 *
 * FIXME: unused...
 */
class FormSpecIterator implements \Iterator //, ArrayAccess?
{
    /**
     * @var ArrayCollection<FormSpecPart>
     */
    protected $parts;

    /**
     * @var \Iterator
     */
    protected $iter;

    /**
     * @param FormSpec $formSpec
     * todo: param $recursively ?
     */
    public function __construct(FormSpec $formSpec)
    {
        $this->parts = $formSpec->getParts();
        $this->iter = $this->parts->getIterator();
    }

    /**
     * @return FormSpecPart
     */
    public function current()
    {
        return $this->iter->current();
    }

    /**
     * @return FormSpecPart
     */
    public function next()
    {
        return $this->iter->next();
    }

    /**
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->iter->key();
    }

    /**
     */
    public function valid()
    {
        return $this->iter->valid();
    }

    /**
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->iter->rewind();
    }

}