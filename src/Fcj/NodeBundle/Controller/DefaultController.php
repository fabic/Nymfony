<?php

namespace Fcj\NodeBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use FOS\RestBundle\Controller\FOSRestController;

/**
 */
class DefaultController extends FOSRestController
{
    /**
     * @ Route("/")
     * @ Template()
     *
     * todo: have it simply output a page with a list of all nodes ?
     */
    public function indexAction()
    {
        $data = array('euh' => 'euh value');
        $view = $this->view($data, 200)
            ->setTemplate("FcjNodeBundle:Default:index.html.twig")
            //->setTemplateVar('data');
            ;
        return $this->handleView($view);
        //return array('name' => $name);
    }
}
