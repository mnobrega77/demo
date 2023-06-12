<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\DemoFormType;
use App\Form\ContactFormType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, MailService $ms): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on crée une instance de Contact
            $message = new Contact();
            // Traitement des données du formulaire
//            $data = $form->getData();
//
//            //on stocke les données récupérées dans la variable $message
//            $message = $data;

            $entityManager->persist($message);
            $entityManager->flush();

            //use MailService
            $email = $ms->sendMail('hello@example.com', $message->getEmail(), $message->getObjet(), $message->getMessage() );
//            dd($message->getEmail());

            // Envoi de l'e-mail, sauvegarde en base de données, etc.
            // Envoi de l'e-mail, sauvegarde en base de données, etc.
//            $email = (new TemplatedEmail())
//                ->from('hello@example.com')
//                ->to($message->getEmail())
//                //->cc('cc@example.com')
//                //->bcc('bcc@example.com')
//                //->replyTo('fabien@example.com')
//                //->priority(Email::PRIORITY_HIGH)
//                ->subject($message->getObjet())
//                ->htmlTemplate('emails/contact_email.html.twig')
//
//                // un tableau de variable à passer à la vue;
//                //  on choisit le nom d'une variable pour la vue et on lui attribue une valeur (comme dans la fonction `render`) :
//                ->context([
//                    'message' => $message->getMessage(),
//
//                ]);
//
//            $mailer->send($email);

            // Redirection vers accueil
            return $this->redirectToRoute('app_accueil');
        }
//A partir de la version 6.2 de Symfony, on n'est plus obligé d'écrire $form->createView(), il suffit de passer l'instance de FormInterface à la méthode render

        return $this->render('contact/index.html.twig', [
//            'form' => $form->createView(),
              'form' => $form
        ]);
    }


    #[Route('/contactdemo', name: 'app_contactdemo')]
    public function index_demo(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Lors de la création du formulaire, nous pouvons spécifier les valeurs initiales des champs
        $form = $this->createForm(DemoFormType::class, [
            "objet" => "Entrez un texte !!!",
            "email" => "",
            "message" => "",
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On peut récupérer toutes données du formulaire sous la forme d'un tableau associatif
            $data = $form->getData();
            dump($data);

            // Ou récupérer les champs un par un
            $nom = $form->get("objet")->getData();
//            dd($nom);

            // Envoi de l'e-mail, sauvegarde en base de données, etc.

            // Redirection vers accueil
            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('contact/index.html.twig', [
              'form' => $form
        ]);
    }
}
