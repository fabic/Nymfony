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
}