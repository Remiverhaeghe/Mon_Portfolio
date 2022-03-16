<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class); 
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->addFlash('notice', 'Merci de nous avoir contacter, notre équipe va vous contacter dans les meilleurs délais');

        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/qui-somme-nous", name="history")
     */
    public function history(Request $request): Response
    {
       
        return $this->render('contact/history.html.twig');
    }
}
