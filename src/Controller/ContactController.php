<?php

namespace App\Controller;

use App\Form\ContactFormType;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement des données du formulaire
            $data = $form->getData();

            // Envoi de l'e-mail, sauvegarde en base de données, etc.

            // Redirection vers une page de succès
            return $this->redirectToRoute('success');
        }
//A partir de la version 6.2 de Symfony, on n'est plus obligé d'écrire $form->createView(), il suffit de passer l'instance de FormInterface à la méthode render

        return $this->render('contact/index.html.twig', [
//            'form' => $form->createView(),
              'form' => $form
        ]);
    }
}
