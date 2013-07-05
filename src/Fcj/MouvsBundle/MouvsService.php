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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use Fcj\MouvsBundle\Entity\FileSource;
use Fcj\MouvsBundle\Entity\File;

class MouvsService
{

    protected $em;

    public function __construct (EntityManager $em)
    {
        $this->em = $em;
    }

    public function fileSources()
    {
        $sources = $this->em->getRepository('FcjMouvsBundle:FileSource')
            ->findAll();
        return $sources;
    }

    public function sync(FileSource $fileSource)
    {
        error_log(__METHOD__ . ": There we are!");
        $em = $this->em;
        $finder = Finder::create()
            ->in($fileSource->getPath())
            ->followLinks()
            ->files()
            ->sortByName();

        /** @var SplFileInfo $file */
        foreach($finder AS $file)
        {
            try {
                error_log("{$file->getFilename()} [{$file->getInode()}] ({$file->getSize()}, {$file->getRelativePath()})");
                $f = new File($file);
                $em->persist($f);
                $fileSource->addFile($f);
            }
            catch(\RuntimeException $ex)
            {
                error_log(__METHOD__ . ": ERROR: Caught exception!: " . $ex->getMessage());
            }
        }
        $em->flush();
    }

}