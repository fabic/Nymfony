<?php

namespace Fcj\MouvsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * FileSource
 *
 * TODO: abstract? w/ impl. such as:
 *
 *    FileSystemSource === FileSource (currently)
 *        UserDirSource (i.e. ~dude/)
 *
 *    RemoteSource (with e.g. caching/latency mecanisms?)
 *       SshSource, FtpSource, WebDavSource, SmbSource,
 *
 *    Online storages:
 *       Dropbox, flickr, Google Drive, etc...
 *
 *    Special cases :
 *       GitSource!! with ability to browse specific trees!
 *       SvnSource
 *       RsyncSource?! (see librsync + php-rsync extension).
 *
 *    Other cases:
 *       Youtube playlists, Vimeo and the like.
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorMap({
 *     "local" = "FileSource",
 *     "ssh"   = "SshSource"
 * })
 *
 * todo/?: Have it impl. IteratorAggregate & Countable ?
 * todo/?: Symfony's Finder component : Extend it? and/or write adapters for each source?
 *    e.g. getFinder().
 * todo/?: Have it be-a File ? and abstract.
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
     *
     * todo/?: Have it be an array of "remote" paths that shall be indexed.
     */
    protected $path;

    /**
     * @var boolean True if this source is *actually* a remote repository of files,
     *    such as for locally mounted filesystems (e.g. sshfs, NFS).
     *
     * @ORM\Column(type="boolean")
     *
     * todo: $latency? $bandwidth? e.g. for keeping track of typical data transfer rates,
     *    and eventually having a means to determine if a given file might be cached locally
     *    when requested...
     */
    protected $remote;

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

    /** Separate structure for storing dirs, Yes/No?
     *
     * @var ArrayCollection
     *
     * @ ORM\OneToMany(targetEntity="Directory", mappedBy="source",
     *    orphanRemoval=true, cascade={"all"},
     *    indexBy="inode"
     */
    protected $directories;

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
     * @param boolean $remote
     */
    public function setRemote($remote)
    {
        $this->remote = $remote;
    }

    /**
     * @return boolean
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /** (Re-)Synchronize DB versus on-disk files.
     * Fixme: rephrase ^ : we really would like to be db oblivious when possible.
     *
     * @param \Doctrine\ORM\EntityManager $em Yes? No?
     *
     * @return ArrayCollection of File instances, some already baked,
     *    others not in case of newly discovered files.
     */
    public function sync(EntityManager $em)
    {
        // todo: Have a custom impl. where *we* do browse
        // dirs ourselves so as to determine if something
        // has changed (dir. mtime).
        $finder = Finder::create()
            ->in($this->path)
            ->followLinks()
            //->files()
            ->sortByName();

        $i = 0;
        $files = new ArrayCollection();

        /** @var SplFileInfo $file */
        foreach($finder AS $file)
        {
            $i ++;
            try {
                //error_log("{$file->getFilename()} [{$file->getInode()}] ({$file->getSize()}, {$file->getRelativePath()})");
                //error_log("$i");
                //$f = new File($file);
                $f = $this->newFile($file);
                $g = $this->addFile($f, $file->getRelativePath());
                $files[$g->getInode()] = $g;
            }
            catch(\RuntimeException $ex)
            {
                error_log(__METHOD__ . ": ERROR: Caught exception!: " . $ex->getMessage());
            }
        }

        return $files;
    }

    /**
     * @param \SplFileInfo $sfi
     * @return File
     */
    public function newFile(\SplFileInfo $sfi)
    {
        if ($sfi->isDir()) {
            $dir = new Directory($sfi, $this);
            return $dir;
        }
        // todo: else if ($sfi->isLink()) ???
        else {
            $file = new File($sfi, $this);
            return $file;
        }
    }

    /**
     *
     * @param File $file
     * @param string $path
     * @throws \InvalidArgumentException
     * @return File The actual indexed File instance for $file.
     */
    public function addFile(File $file, $path)
    {
        $inode = $file->getInode();
        if (!$inode)
            throw new \InvalidArgumentException(__METHOD__ . ": ERROR: File has *NO* inode!!");

        $parent = $this->lookupDirectoryByPath($path);
        if ($this->files->containsKey($inode)) {
            //error_log("INFO: " .__METHOD__. ": Inode $inode is already baked.");
            error_log("U\t$inode\t{$file->getName()}");
            /** @var File $baked */
            $baked = $this->files->get($inode);
            // CTime : Update if Inode has changed.
            if ($baked->getCTime() != $file->getCTime()) {
                $baked->setName($file->getName());
                //$baked->setPath($file->getPath());
                $baked->setCTime($file->getCTime());
                $baked->setLastUpdate();
            }
            // MTime : Update if file content has changed.
            if ($baked->getMTime() != $file->getMTime()) {
                $baked->setSize($file->getSize());
                $baked->setHash(null); // Reset the hash.
                $baked->setMTime($file->getMTime());
                $baked->setLastUpdate();
            }
            return $baked;
        } else { // fixme !?
            //error_log("INFO: " .__METHOD__. ": Inode $inode NEW!!! ({$file->getName()}).");
            error_log("A\t$inode\t{$file->getName()}");
            $file->setSource($this);
            $file->setAddedOn();
            $this->files->set($inode, $file);
            return $file;
        }
    }

    /**
     * Get files
     *
     *
     * @return ArrayCollection
     */
    public function getFiles()
    {
        return $this->files;
    }

    // todo: getRemoteFiles()
}
