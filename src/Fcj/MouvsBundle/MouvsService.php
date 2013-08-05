<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cadet
 * Date: 7/4/13
 * Time: 4:11 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Fcj\MouvsBundle;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\Expr;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use Fcj\MouvsBundle\Entity\Directory;
use Fcj\MouvsBundle\Entity\FileSource;
use Fcj\MouvsBundle\Entity\File;

class MouvsService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /** Tha constructor, DI injection magic happens here.
     *
     * @param EntityManager $em
     */
    public function __construct (EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return Collection of FileSource.
     */
    public function fileSources()
    {
        $sources = $this->em->getRepository('FcjMouvsBundle:FileSource')
            ->findAll();
        return $sources;
    }

    /**
     * @param FileSource|null $fileSource
     * @return array of File instances.
     */
    public function directories(FileSource $fileSource = null)
    {
        $qb = $this->dirsRep()->createQueryBuilder('f');
        if ($fileSource) {
            $qb->join('f.source', 'fs')
                ->where('fs.id = :source_id')
                ->setParameter('source_id', $fileSource->getId());
        }
        $q = $qb->getQuery();
        $q->execute();
        $result = $q->getResult();
        return $result;
    }

    /** (Re-)Synchronize DB versus on-disk files.
     *
     * @param FileSource $fileSource
     * @return Collection of File instances, some already baked,
     *    others not in case of newly discovered files.
     */
    public function sync(FileSource $fileSource)
    {
        error_log(__METHOD__ . ": There we are!");

        $em = $this->em;

        //$files = $fileSource->sync();
        //$files = $this->syncLocalFileSystem($fileSource);
        // todo: Have a custom impl. where *we* do browse
        // dirs ourselves so as to determine if something
        // has changed (dir. mtime).

        // TODO: Better: Have a getIteratorForSource() thing, e.g.
        // sthg that whichever the source is (ssh, ftp, dav, ...) would
        // simply return an iterator.
        $finder = Finder::create()
            ->in($fileSource->getPath())
            ->followLinks()
            //->files()
            ->sortByName();

        $dirs = $this->directories($fileSource);
        $dirs = \Fcj\Util::reindex($dirs, 'relativePathname');
        $dirs[''] = $fileSource;
        error_log(print_r(array_keys($dirs), true));

        $i = 0;
        $files = new ArrayCollection();

        /** @var SplFileInfo $sfi */
        foreach($finder AS $sfi)
        {
            $i ++;
            try {
                error_log("#$i - {$sfi->getFilename()} [{$sfi->getInode()}] ({$sfi->getSize()}, {$sfi->getRelativePath()})");
                //error_log("$i");
                //$f = new File($file);
                $rpath = $sfi->getRelativePath();
                $parent = array_key_exists($rpath, $dirs) ? $dirs[$rpath] : null;
                if ($sfi->isDir()) {
                    $dir = new Directory($sfi);
                    $dir->setParent($parent);
                    $fileSource->addFile($dir);
                    $dirs[$dir->getRelativePathname()] = $dir;
                }
                // todo: else if ($sfi->isLink()) ???
                else {
                    $file = new File($sfi);
                    $file->setParent($parent);
                    $fileSource->addFile($file);
                    $files[$file->getInode()] = $file;
                }
            }
            catch(\RuntimeException $ex)
            {
                error_log(__METHOD__ . ": ERROR: Caught exception!: " . $ex->getMessage());
            }
        }

        //error_log(__METHOD__ . ": FLUSH TO DATABASE !!");
        $em->flush();

        return $files;
    }

    // todo: SyncAllSources() ?

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function filesRep()
    {
        return $this->em->getRepository('FcjMouvsBundle:File');
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function dirsRep()
    {
        return $this->em->getRepository('FcjMouvsBundle:Directory');
    }

    //public function syncLocalFileSystem(FileSource $fileSource) { }   ???
    // todo/?: Likewise, have syncSshSource(), syncFtpSource(), ...
}