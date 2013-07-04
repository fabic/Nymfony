<?php

namespace Fcj\MouvsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fcj\MouvsBundle\Entity\FileSource;
use Fcj\MouvsBundle\Form\FileSourceType;

/**
 * FileSource controller.
 *
 * @Route("/source")
 */
class FileSourceController extends Controller
{
    /**
     * Lists all FileSource entities.
     *
     * @Route("/", name="source")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        //$em = $this->getDoctrine()->getManager();
        //$entities = $em->getRepository('FcjMouvsBundle:FileSource')->findAll();
        $mouvs = $this->get('mouvs');
        $entities = $mouvs->fileSources();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new FileSource entity.
     *
     * @Route("/", name="source_create")
     * @Method("POST")
     * @Template("FcjMouvsBundle:FileSource:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new FileSource();
        $form = $this->createForm(new FileSourceType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('source_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new FileSource entity.
     *
     * @Route("/new", name="source_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new FileSource();
        $form   = $this->createForm(new FileSourceType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a FileSource entity.
     *
     * @Route("/{id}", name="source_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FcjMouvsBundle:FileSource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FileSource entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing FileSource entity.
     *
     * @Route("/{id}/edit", name="source_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FcjMouvsBundle:FileSource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FileSource entity.');
        }

        $editForm = $this->createForm(new FileSourceType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing FileSource entity.
     *
     * @Route("/{id}", name="source_update")
     * @Method("PUT")
     * @Template("FcjMouvsBundle:FileSource:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FcjMouvsBundle:FileSource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FileSource entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new FileSourceType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('source_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a FileSource entity.
     *
     * @Route("/{id}", name="source_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FcjMouvsBundle:FileSource')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FileSource entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('source'));
    }

    /**
     * Creates a form to delete a FileSource entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
