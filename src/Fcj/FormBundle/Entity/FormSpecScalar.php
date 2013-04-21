<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormSpecScalar
 *
 * @ORM\Entity
 *
 * todo: s/FormSpecScalar/FormSpecFormTypeRef ??
 */
class FormSpecScalar extends FormSpec
{
    /**
     * @var string Actually a form.type.thing
     *
     * @ORM\Column(type="string", length=64)
     */
    protected $typeName;

    /**
     * @param $name string Typically a Symfony Form component type, e.g.
     *     text, integer, email, ...
     * @param $typeName fully qualified symfony form.type.xxxx ?? (why?)
     *
     * @see http://symfony.com/doc/current/reference/forms/types.html
     */
    public function __construct($name, $typeName)
    {
        parent::__construct($name);
        $this->typeName = $typeName;
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
     * Set type
     *
     * @param string $type
     * @return FormSpecScalar
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
