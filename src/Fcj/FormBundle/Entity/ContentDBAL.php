<?php

namespace Fcj\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentDBAL
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ContentDBAL extends Content
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=96)
     */
    protected $tableName;


    /** ctor
     * @param $tableName
     */
    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Set tableName
     *
     * @param string $tableName
     * @return ContentDBAL
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    
        return $this;
    }

    /**
     * Get tableName
     *
     * @return string 
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}
