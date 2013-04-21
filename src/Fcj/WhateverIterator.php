<?php
/** File:
 */
namespace Cines\PeerReviewBundle;

/**
 */
class WhateverIterator implements \Iterator
    //, \ArrayAccess ?
{
    //protected $whatever;

    protected $vertices;
    protected $labels;

    static protected $verticeSkeleton = Array(
        'label'   => '',
        'visited' => 0,
        'reflClass' => null
    );

    /**
     * todo: Traversal options (limit depth, getterOnly, isserOnly) ?
     * todo: property path like traversal definition, emailAddress » getEmailAddress() ?
     * todo: Traveral/filter/callback closure ?
     */
    public function __construct ($whatever)
    {
        $this->vertices = new \SplObjectStorage();
        $whatever = is_array($whatever) ? $whatever : array($whatever);
        $iterator = new \RecursiveArrayIterator($whatever);
        foreach($iterator AS $label => $object) {
            assert(is_object($object));
            $this->addVertice ($object, $label);
        }
    }

    public function addVertice($object, $label='')
    {
        $vdata = self::$verticeSkeleton;
        //$vdata['label'] = $label; // todo: valid label are alphanum.
        $label = is_numeric($label) ? strtr(get_class($object), '\\', '_') : $label;
        $vdata['label'] = $label;
        $this->vertices->attach($object, $vdata);
        $this->labels[$label] = $object;
    }

    /*
     * === Iterator implementation ===
     */

    /**
     * @return void
     */
    public function rewind()
    {
        $this->vertices->rewind();
    }

    /**
     * @return
     */
    public function current()
    {
        return $this->vertices->current();
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->vertices->key();
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->vertices->next();
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->vertices->valid();
    }


}
