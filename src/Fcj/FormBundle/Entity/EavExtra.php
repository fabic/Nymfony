<?php

namespace Fcj\FormBundle\Entity;

use Fcj\FormBundle\Entity\ExtraData;

use Doctrine\ORM\Mapping as ORM;

/**
 * HashmapExtra : For your ORM-baked key-value mapping needs.
 *
 * @ORM\Entity
 */
class EavExtra extends ExtraData
{
    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $integer;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $string;

    /// todo: Etc...
    /// todo: Have it be handled at the Doctrine/DBAL/ORM level ?
    /// todo.. hence having some kind of automatic eav backend table(s)
    /// todo.. be generated; likewise for this entity.

    /**
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
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
     * Set parts
     *
     * @param array $parts
     * @return ArrayPart
     */
    public function setParts($parts)
    {
        $this->parts = $parts;
    
        return $this;
    }

    /**
     * Get parts
     *
     * @return array 
     */
    public function getParts()
    {
        return $this->parts;
    }
}
