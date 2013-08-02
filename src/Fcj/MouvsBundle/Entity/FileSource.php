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
 *    Better, have a url scheme based impl for all of these, e.g.:
 *          ssh://dude@host.example.com/home/dude/music
 *          dav://host.example.com/dav/dude
 *          git://...  ;  svn://...
 *          youtube://.../playlist/xyz
 *
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ ORM\InheritanceType("SINGLE_TABLE")
 * @ ORM\DiscriminatorMap({
 *     "local" = "FileSource",
 *     "ssh"   = "SshSource"
 * })
 *
 * todo/?: Have it impl. IteratorAggregate & Countable ?
 * todo/?: Symfony's Finder component : Extend it? and/or write adapters for each source?
 *    e.g. getFinder().
 * todo/?: Have it be-a Directory ? and abstract?
 */
class FileSource extends Directory
{
    /**
     * @var integer
     *
     * @ ORM\Column(type="integer")
     * @ ORM\Id
     * @ ORM\GeneratedValue(strategy="AUTO")
     */
    //protected $id;

    /**
     * @var string
     *
     * @ ORM\Column(type="text")
     *
     * todo/?: Have it be an array of "remote" paths that shall be indexed.
     */
    //protected $path;

    /**
     * @var boolean True if this source is *actually* a remote repository of files,
     *    such as for locally mounted filesystems (e.g. sshfs, NFS).
     *
     * @ ORM\Column(type="boolean")
     *
     * todo: $latency? $bandwidth? e.g. for keeping track of typical data transfer rates,
     *    and eventually having a means to determine if a given file might be cached locally
     *    when requested...
     */
    //protected $remote;

    /**
     * @var ArrayCollection
     *
     * @ ORM\OneToMany(targetEntity="File", mappedBy="source",
     *    orphanRemoval=true, cascade={"all"},
     *    indexBy="inode"
     * )
     * todo: indexBy = "hash" ? or "inode" ?
     */
    //protected $files;

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
    //protected $directories;

    /**
     *
     * @param File $file
     * @throws \InvalidArgumentException
     * @return File The actual indexed File instance for $file.
     */
    public function addFile(File $file)
    {
        $inode = $file->getInode();
        if (!$inode)
            throw new \InvalidArgumentException(__METHOD__ . ": ERROR: File has *NO* inode!!");

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
            $file->setIndexedOn();
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
