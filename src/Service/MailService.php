<?php

namespace App\Service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;





class MailService
{
    //On injecte dans le constructeur les dépendances dont on a besoin, comme le MailerInterface

    private $mailer;
    private $requestStack;
    public function __construct(MailerInterface $mailer, RequestStack $requestStack){
        $this->mailer = $mailer;
        $this->requestStack = $requestStack;
    }


    //on écrit notre fonction sendMail()
    public function sendMail($expediteur, $destinataire, $sujet, $message){

        //le code qui construit le mail
        $email = (new TemplatedEmail())
            ->from($expediteur)
            ->to($destinataire)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($sujet)
            ->htmlTemplate('emails/contact_email.html.twig')
            ->context([
                'message' => $message,

            ]);
        //...

        $this->mailer->send($email);
    }
}