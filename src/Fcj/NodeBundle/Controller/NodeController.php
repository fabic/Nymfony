<?php

namespace Fcj\NodeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * @RouteResource("Node")
 */
class NodeController extends FOSRestController
{
    /**
     *
     */
    public function cgetAction()
    {
        $data = array('euh' => 'euh value');
        $view = $this->view($data, 200)
            ->setTemplate("FcjNodeBundle:Default:index.html.twig")
            ;
        return $this->handleView($view);
    }
}
