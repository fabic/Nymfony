<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cadet
 * Date: 7/30/13
 * Time: 10:49 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Fcj\MouvsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Finder\SplFileInfo;


/**
 * @ORM\Entity
 *
 * @package Fcj\MouvsBundle\Entity
 */
class Directory extends File
{
    /**
     * @var File
     *
     * @ORM\OneToMany(targetEntity="File",
     *     mappedBy="parent",
     *     indexBy="inode"
     * )
     */
    protected $files;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $path;

    public function __construct(SplFileInfo $file = null)
    {
        parent::__construct($file);
        $this->files = new ArrayCollection();
    }

    public function copyFrom (SplFileInfo $file)
    {
        parent::copyFrom($file);
        $this->path = $file->getRelativePath();
        return $this;
    }

    /// inherited. todo: is it ok?
    public function getRelativePathname()
    {
//        return $this->path ?
//              $this->path . DIRECTORY_SEPARATOR . $this->name
//            : $this->name;
        $ppath = $this->parent ? $this->parent->getRelativePathname() : '';
        return $ppath ?
              $ppath . DIRECTORY_SEPARATOR . $this->name
            : $this->name;
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

    /**
     * @return \Fcj\MouvsBundle\Entity\File
     */
    public function getFiles()
    {
        return $this->files;
    }


}