<?php

namespace Fcj\MouvsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Finder\Finder;

class DefaultController extends BaseController
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $mouvs = $this->get('mouvs');

        //$there = "/home/cadet/mouvs/";
        $there = "../src/";

        $finder = Finder::create()
            //->in('.')
            ->in($there)
            ->followLinks()
            ->files()
            ->sortByName();

        return array('finder' => $finder);
    }
}
