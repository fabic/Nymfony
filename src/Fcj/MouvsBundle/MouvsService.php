<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cadet
 * Date: 7/4/13
 * Time: 4:11 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Fcj\MouvsBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
     * @param FileSource $fileSource
     * @return Collection of File instances.
     */
    public function sync(FileSource $fileSource)
    {
        error_log(__METHOD__ . ": There we are!");

        $em = $this->em;
        $files = $fileSource->sync();

        //error_log(__METHOD__ . ": FLUSH TO DATABASE !!");
        //$em->flush();

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

}