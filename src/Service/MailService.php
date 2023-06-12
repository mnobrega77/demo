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

    public function __construct(
        private MailerInterface $mailer,
        private RequestStack $requestStack
)


    //on écrit notre fonction sendMail()
    public function sendMail($expediteur, $destinataire, $sujet, $message){

        //le code qui construit le mail
        $email = (new Email())
            ->from($expediteur)
            ->to($destinataire)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($sujet)
            ->text($message);
        //...

        $this->mailer->send();
    }
}