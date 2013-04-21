<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormSpecRelationSelf
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FormSpecRelationSelf
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
     * @var string A short "semantic" name for that relation, such as, e.g. :
     *    - « derives_from / copied_from / duplicated_from » : $to "derives" from $from ;
     *    - « version » : @see FormSpecVersionRel ;
     *    - « relates_to » : $to "is related to" $from, with a $comment ? todo ;
     *    - « extends » : $to extends $from ;
     *
     * @ORM\Column(type="string", length=32)
     * @ ORM\Id
     */
    private $kind;

    /**
     * @param FormSpec $from
     * @param FormSpec $to
     * @param $relationKind
     */
    public function __construct(FormSpec $from, FormSpec $to, $relationKind)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->kind = $relationKind;
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
     * Set kind
     *
     * @param string $kind
     * @return FormSpecRelationSelf
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
    
        return $this;
    }

    /**
     * Get kind
     *
     * @return string 
     */
    public function getKind()
    {
        return $this->kind;
    }
}
