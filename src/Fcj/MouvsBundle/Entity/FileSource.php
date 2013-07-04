<?php

namespace Fcj\MouvsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * FileSource
 *
 * todo: abstract? w/ impl. such as:
 *    FileSystemSource
 *        UserDirSource (i.e. ~dude/)
 *    RemoteSource (with e.g. caching/latency mecanisms?)
 *       SshSource, FtpSource, WebDavSource
 *
 *
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FileSource
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
     * @ORM\Column(type="text")
     */
    protected $path;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="File", mappedBy="source"),
     *    orphanRemoval="true", cascade={"all"}
     * )
     * todo: indexBy = "hash"
     */
    protected $files;

    // todo: $owner? here?
    // todo: $visibility := IN private (to user), public (anyone) ?

    /**
     *
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
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
     * Set path
     *
     * @param string $path
     * @return FileSource
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

    /**
     * Set files
     *
     * @param integer $files
     * @return FileSource
     */
    public function setFiles($files)
    {
        $this->files = $files;
    
        return $this;
    }

    /**
     * Get files
     *
     * @return integer 
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function sync()
    {
        $finder = Finder::create()
            ->in($this->path)
            ->followLinks()
            ->files()
            ->sortByName();

        /** @var SplFileInfo $file */
        foreach($finder AS $file)
        {
            error_log("{$file->getFilename()} ({$file->getInode()})");
        }
    }
}
