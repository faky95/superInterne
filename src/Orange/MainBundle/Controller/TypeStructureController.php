<?php

namespace Orange\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Orange\MainBundle\Entity\TypeStructure;
use Orange\MainBundle\Form\TypeStructureType;
use Orange\QuickMakingBundle\Annotation\QMLogger;
/**
 * TypeStructure controller.
 *
 * @Route("/les_types_structures")
 */
class TypeStructureController extends Controller
{

    /**
     * Lists all TypeStructure entities.
     * @Route("/", name="les_types_structures")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OrangeMainBundle:TypeStructure')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new TypeStructure entity.
     *
     * @Route("/", name="les_types_structures_create")
     * @Method("POST")
     * @Template("OrangeMainBundle:TypeStructure:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TypeStructure();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('les_types_structures_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a TypeStructure entity.
    *
    * @param TypeStructure $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TypeStructure $entity)
    {
        $form = $this->createForm(new TypeStructureType(), $entity, array(
            'action' => $this->generateUrl('les_types_structures_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TypeStructure entity.
     *
     * @Route("/new", name="les_types_structures_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TypeStructure();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a TypeStructure entity.
     *
     * @Route("/{id}", name="les_types_structures_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:TypeStructure')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TypeStructure entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TypeStructure entity.
     *
     * @Route("/{id}/edit", name="les_types_structures_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:TypeStructure')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TypeStructure entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a TypeStructure entity.
    *
    * @param TypeStructure $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TypeStructure $entity)
    {
        $form = $this->createForm(new TypeStructureType(), $entity, array(
            'action' => $this->generateUrl('les_types_structures_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TypeStructure entity.
     *
     * @Route("/{id}", name="les_types_structures_update")
     * @Method("PUT")
     * @Template("OrangeMainBundle:TypeStructure:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OrangeMainBundle:TypeStructure')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TypeStructure entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('les_types_structures_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a TypeStructure entity.
     *
     * @Route("/{id}", name="les_types_structures_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OrangeMainBundle:TypeStructure')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TypeStructure entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('les_types_structures'));
    }

    /**
     * Creates a form to delete a TypeStructure entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('les_types_structures_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
