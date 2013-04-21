<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormView
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FormView
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
