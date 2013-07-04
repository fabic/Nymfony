<?php

namespace Fcj\MouvsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Finder\SplFileInfo;

/**
 * File
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class File extends SplFileInfo
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
     * @var FileSource
     *
     * @ORM\ManyToOne(targetEntity="FileSource", inversedBy="files")
     */
    protected $source;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $hash;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $path;


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
     * Set name
     *
     * @param string $name
     * @return File
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

    /**
     * Set path
     *
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }
}
