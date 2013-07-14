<?php

namespace Fcj\MouvsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package Fcj\MouvsBundle\Controller
 */
class DefaultController extends BaseController
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $mouvs = $this->mouvs();

        $sources = $mouvs->fileSources();

        return array(
            'sources' => $sources
        );
    }

    /**
     * @Route("/path/{path}", name="files_by_path",
     *    requirements={"path" = "/?.*"})
     *
     * @Template()
     */
    public function pathAction(Request $request, $path)
    {
        $mouvs = $this->mouvs();

        $page   = $request->query->get('page', 1);
        $limit  = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);

        $page  = $page>0   ? $page  :  1;
        $limit = $limit>=5 ? $limit : 20;

        $offset += ($page-1) * $limit;

        $criteria = array();
        if ($path)
            $criteria['path'] = $path;

        $files = $mouvs->filesRep()->findBy(
            $criteria,
            array(
                'path' => 'ASC',
                'name' => 'ASC'
            ),
            $limit,
            $offset
        );

        $up_path = $path ? substr($path,0, strrpos($path,'/')) : '/';

        return array(
            'path'  => $path,
            'up_path' => $up_path,
            'limit' => $limit,
            'page'  => $page,
            'offset' => $offset,
            'files' => $files
        );
    }
}
