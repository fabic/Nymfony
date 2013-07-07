<?php

namespace Fcj\MouvsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToMany(targetEntity="File", mappedBy="source",
     *    orphanRemoval=true, cascade={"all"},
     *    indexBy="inode"
     * )
     * todo: indexBy = "hash" ? or "inode" ?
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
     *
     * @param File $file
     * @throws \InvalidArgumentException
     * @return FileSource
     */
    public function addFile(File $file)
    {
        $inode = $file->getInode();
        if ($inode && $this->files->containsKey($inode)) {
            error_log("INFO: " .__METHOD__. ": Inode $inode is already baked.");
            /** @var File $baked */
            $baked = $this->files->get($inode);
            //$baked->setLastUpdate();
            if ($baked->getCTime() != $file->getCTime())
            {
                $baked->setName($file->getName());
                $baked->setPath($file->getPath());
                $baked->setCTime($file->getCTime());
                $baked->setLastUpdate();
            }
            if ($baked->getMTime() != $file->getMTime())
            {
                $baked->setSize($file->getSize());
                $baked->setHash(null);
                $baked->setMTime($file->getMTime());
                $baked->setLastUpdate();
            }
        }
        else if ($inode) { // fixme !
            error_log("INFO: " .__METHOD__. ": Inode $inode NEW!!! ({$file->getName()}).");
            $file->setSource($this);
            $this->files->set($inode, $file);
        }
        else // fixme: yes? no?
            throw new \InvalidArgumentException(__METHOD__ . ": ERROR: File has *NO* inode!!");
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

}
