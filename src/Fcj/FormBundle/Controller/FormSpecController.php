<?php

namespace Fcj\FormBundle\Controller;

use Fcj\FormBundle\Form\FormSpecType;
use Fcj\FormBundle\Form\ContentType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FormSpecController
 *
 * @package Fcj\FormBundle\Controller
 *
 * @Route("/spec")
 */
class FormSpecController extends Controller
{
    // todo: list...
    // todo: show...

    /**
     * @Route("/new")
     * @Template()
     */
    public function newAction()
    {
        $form = $this->createForm(new FormSpecType());
        $formView = $form->createView();
        return array('form' => $formView);
    }

    /**
     * @Route("/test/{name}")
     * @Route("/test/{name}/{spec}", defaults={"spec" = null}, name="test")
     * @Template()
     */
    public function testAction(Request $request)
    {
        $formSpecName = $request->get('name');
        $subSpecName  = $request->get('spec');

        $fcj = $this->get('fcj_form');
//        $formSpecs = $fcj->loadFormSpecFromYamlFile('../formSpecs/' . $formSpecName . '.yml');

        $options = array();
        $drops = array(
            'nationalities' => array(),
            'countries' => array(),
            'yeah' => "YYYYYEEEEEEUUUUUUUUHHHHHHHH!!!!!"
        );

        //$form = $fcj->createForm($formSpecName, null, $options, $drops);
        $builder = $this->createFormBuilder(null, array('required'=>false));

        $builder->add('dummyFieldTop', 'text', array('label'=> "Top dummy field", 'data'=>'Top!'));

        //$builder->add('dummy_type_by_name', 'fcj_dummy_type');

        $fb = $fcj->createNamedBuilder($formSpecName, $formSpecName);
        $builder->add($fb, null, array());


//        if ($subSpecName && isset($formSpecs[$subSpecName]))
//            $formSpecs = array($subSpecName => $formSpecs[$subSpecName]);

//        foreach($formSpecs AS $fsName => $fs)
//        {
//            $fb = $fcj->createNamedBuilder($fsName, $fsName);
//            $builder->add($fb, null, array());
//        }

        $builder->add('dummyFieldBot', 'text', array('label'=> "Bottom dummy field", 'data'=>'Bottom!'));

        // TODO : We might actually want THIS actually:
        // todo..   $form = $this->createForm($formSpecName);

        $form = $builder->getForm();
        //$form = $this->createForm('contact');

        if ($request->isMethod('POST')) {
            $form->bind($request);
            return new Response(
                "<pre>"
                . print_r($form->getData(),true)
                . "\n\n"
                //. print_r($fcj, true)
                . "</pre>"
            );
        }

        $formView = $form->createView();
        return array(
            'form' => $formView,
            'formSpecName' => $formSpecName,
            'subSpecName'  => $subSpecName
        );
    }

}
