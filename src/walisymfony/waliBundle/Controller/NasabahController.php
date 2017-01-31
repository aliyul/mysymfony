<?php

namespace walisymfony\waliBundle\Controller;

use walisymfony\waliBundle\Entity\Nasabah;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use walisymfony\waliBundle\EventListener;

/**
 * Nasabah controller.
 *
 * @Route("nasabah")
 */
class NasabahController extends Controller
{
    /**
     * Lists all nasabah entities.
     *
     * @Route("/", name="nasabah_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        /**
        $locale = $this->get('request')->getLocale();
        $result = $this->getDoctrine()->getRepository('AcmeMyBundle:MyEntity')->findNtecByIdAndLocaleOrSomething($id, $locale);
*/
        $em = $this->getDoctrine()->getManager();

        $nasabahs = $em->getRepository('walisymfonywaliBundle:Nasabah')->findAll();

        return $this->render('nasabah/index.html.twig', array(
            'nasabahs' => $nasabahs,
        ));
    }

    /**
     * Creates a new nasabah entity.
     *
     * @Route("/new", name="nasabah_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {

        $nasabah = new Nasabah();
        $form = $this->createForm('walisymfony\waliBundle\Form\NasabahType', $nasabah);
        $form->handleRequest($request);
        $locale = $request->getLocale();

        if ($form->isSubmitted() && $form->isValid()) {
           # $Validasi=$nasabah->validateRegisterData(array($nasabah->getNama(),$nasabah->getNocredit()));
           # if ($Validasi == true) {
                $cardnumber = $nasabah->CreditCardType($nasabah->getNocredit());
                if ($cardnumber == "Visa") {
                    #echo "CreditCardType";
                    echo $cardnumber;
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($nasabah);
                    $em->flush($nasabah);

                    return $this->redirectToRoute('nasabah_show', array('id' => $nasabah->getId()));
                } else {
                    echo $cardnumber;

                }
            #}else{

            #}
        }

        return $this->render('nasabah/new.html.twig', array(
            'nasabah' => $nasabah,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a nasabah entity.
     *
     * @Route("/{id}", name="nasabah_show")
     * @Method("GET")
     */
    public function showAction(Nasabah $nasabah)
    {
        $deleteForm = $this->createDeleteForm($nasabah);

        return $this->render('nasabah/show.html.twig', array(
            'nasabah' => $nasabah,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing nasabah entity.
     *
     * @Route("/{id}/edit", name="nasabah_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Nasabah $nasabah)
    {
        $deleteForm = $this->createDeleteForm($nasabah);
        $editForm = $this->createForm('walisymfony\waliBundle\Form\NasabahType', $nasabah);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('nasabah_edit', array('id' => $nasabah->getId()));
        }

        return $this->render('nasabah/edit.html.twig', array(
            'nasabah' => $nasabah,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a nasabah entity.
     *
     * @Route("/{id}", name="nasabah_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Nasabah $nasabah)
    {
        $form = $this->createDeleteForm($nasabah);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($nasabah);
            $em->flush($nasabah);
        }

        return $this->redirectToRoute('nasabah_index');
    }

    /**
     * Creates a form to delete a nasabah entity.
     *
     * @param Nasabah $nasabah The nasabah entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Nasabah $nasabah)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('nasabah_delete', array('id' => $nasabah->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
