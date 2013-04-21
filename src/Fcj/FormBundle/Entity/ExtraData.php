<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExtraData : Utility thing for storage of extra data as part of e.g.
 * FormSpecs, FormSpecParts, ContentParts?
 *
 * FIXME: ...
 *
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *      "eav" = "Fcj\FormBundle\Entity\EavExtra"
 * })
 */
class ExtraData
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
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $name;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getId() { return $this->id; }

    /**
     * Set name
     *
     * @param string $name
     * @return ExtraData
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
}
