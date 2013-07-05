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
class File //extends SplFileInfo
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
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $inode;

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


    public function __construct (SplFileInfo $file, FileSource $source=null)
    {
        //parent::__construct($file);
        //parent::setInfoClass(get_class());
        $this->source = $source;
        $this->copyFrom($file);
    }

    /** Sort of a copy constructor.
     *
     * @param SplFileInfo $file
     * @return $this
     */
    public function copyFrom (File $file)
    {
        $this->name = $file->getFilename();
        $this->path = $file->getRelativePath();
        // Fixme?
        try {
            $this->inode = $file->getInode();
        }
        catch(\RuntimeException $ex)
        {
            error_log(__METHOD__ . ": ERROR: Caught exception!: " . $ex->getMessage());
        }
        return $this;
    }

    /// inherited. todo: is it ok?
    public function getRelativePathname()
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->name;
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
     * @param FileSource $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return FileSource
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param int $inode
     */
    public function setInode($inode)
    {
        $this->inode = $inode;
    }

    /**
     * @return int
     */
    public function getInode()
    {
        return $this->inode;
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
