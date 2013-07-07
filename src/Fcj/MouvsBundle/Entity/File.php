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
     * @ORM\ManyToOne(targetEntity="FileSource",
     *     inversedBy="files")
     */
    protected $source;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lastUpdate;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", unique=true)
     */
    protected $inode;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $ctime;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $mtime;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $size;

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
    public function copyFrom (SplFileInfo $file)
    {
        //$this->setLastUpdate();
        $this->name = $file->getFilename();
        $this->path = $file->getRelativePath();
        // Fixme?
        try {
            $this->size = $file->getSize();
            $this->inode = $file->getInode();
            $this->ctime = $file->getCTime();
            $this->mtime = $file->getMTime();
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
     * @param int $lastUpdate
     */
    public function setLastUpdate($lastUpdate=null)
    {
        $this->lastUpdate = $lastUpdate===null ? time() : $lastUpdate;
    }

    /**
     * @return int
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
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
     * @param int $ctime
     */
    public function setCTime($ctime)
    {
        $this->ctime = $ctime;
    }

    /**
     * @return int
     */
    public function getCTime()
    {
        return $this->ctime;
    }

    /**
     * @param int $mtime
     */
    public function setMTime($mtime)
    {
        $this->mtime = $mtime;
    }

    /**
     * @return int
     */
    public function getMTime()
    {
        return $this->mtime;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string|null $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
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

    public function __toString()
    {
        return "{$this->name} ({$this->inode})";
    }
}
