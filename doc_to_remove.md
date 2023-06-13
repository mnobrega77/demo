<br>

## Les Événements Doctrine

### Objectifs pédagogiques
À la fin de ce cours, vous devriez être en mesure de :
* comprendre le concept d'Événements dans Symfony et dans Doctrine
* créer et utiliser les EventSubscribers de Doctrine pour détecter les changements dans la base de données


### Prérequis
Avoir une connaissance de base de Symfony et de Doctrine.

### Introduction
#### Comprendre les EventSubscribers
Qu'est-ce qu'un EventSubscriber ?  

Un `EventSubscriber` est une classe qui écoute les événements (`Events`) et exécute des actions en réponse à ces événements. 
Il s'agit d'une fonctionnalité puissante de Symfony pour gérer les événements et effectuer des actions spécifiques.

#### Événements de Symfony vs Événements de Doctrine :

Bien qu'ils soient souvent utilisés ensemble dans une application Symfony, les événements de Symfony et de Doctrine sont deux systèmes d'événements distincts.
* Les événements de Symfony sont utilisés pour déclencher des actions spécifiques en réponse à des événements survenant dans l'application (événements liés à la gestion 
des requêtes HTTP (comme `kernel.request), aux formulaires, à la sécurité, aux notifications, etc.

* Les événements de Doctrine sont spécifiques à cet ORM et sont utilisés pour écouter et réagir aux opérations de la base de données effectuées par Doctrine, telles que la création, la mise à jour, la suppression ou le chargement d'entités.
Doctrine émet des événements lors de différentes étapes du cycle de vie d'une entité, par exemple, `postPersist` après l'insertion d'une entité dans la base de données, `preUpdate` avant la mise à jour d'une entité, etc.

Les événements de Doctrine permettent aux développeurs d'intercepter et de réagir à ces opérations de base de données en exécutant des actions supplémentaires. 
Par exemple, vous pouvez envoyer un e-mail, mettre à jour une autre entité ou effectuer toute autre logique métier nécessaire.

Même si les deux type d'événéments sont distincts, Symfony fournit des moyens d'intégrer les événements de Doctrine dans son système d'événements.
Ainsi, vous pouvez créer des EventSubscribers pour écouter à la fois les événements de Symfony et les événements de Doctrine, et agir en conséquence.

#### EventSubscriber ou EventListener ? 
Dans Symfony, vous pouvez utiliser à la fois des listeners et des subscribers en fonction des besoins de votre application. 
Un EventSubscriber peut écouter **plusieurs événements**, tandis qu'un EventListener écoute **un événement spécifique**.
Un subscriber est plus flexible car il peut écouter et réagir à plusieurs événements avec une seule classe. 


#### Les événements de Doctrine les plus couramment utilisés :

* `prePersist` : Cet événement est déclenché juste avant qu'une entité soit persistée pour la première fois dans la base de données. Il peut être utilisé pour effectuer des actions avant l'insertion d'une nouvelle entité, comme la génération de valeurs par défaut, la validation supplémentaire, etc.

* `postPersist` : Cet événement est déclenché après l'insertion d'une entité dans la base de données. Il est utile pour effectuer des actions supplémentaires une fois que l'entité a été persistée, telles que l'envoi d'e-mails, la mise à jour de caches, etc.

* ̀`preUpdate` : Cet événement est déclenché avant la mise à jour d'une entité dans la base de données. Il permet de prendre des mesures avant que les modifications ne soient persistées, comme la validation supplémentaire, la modification d'autres entités liées, etc.

* `postUpdate` : Cet événement est déclenché après la mise à jour d'une entité dans la base de données. Il peut être utilisé pour effectuer des actions supplémentaires après la mise à jour de l'entité, comme l'enregistrement d'audits, la notification des utilisateurs, etc.

* `preRemove` : Cet événement est déclenché avant la suppression d'une entité de la base de données. Il peut être utilisé pour effectuer des actions avant la suppression, telles que la validation, la vérification des dépendances, etc.

* `postRemove` : Cet événement est déclenché après la suppression d'une entité de la base de données. Il est utile pour effectuer des actions supplémentaires après la suppression de l'entité, comme la suppression des fichiers associés, la mise à jour d'autres entités, etc.

* `onFlush` : Cet événement est déclenché avant la validation et la persistance des objets dans la base de données. Il permet de travailler avec l'ensemble des modifications en cours avant qu'elles ne soient exécutées.



**Attention** Les événements de Doctrine sont spécifiques à cet ORM et ne réagissent pas aux scripts SQL exécutés directement sur la base de données! Lorsqu'ils sont exécutés, ces scripts SQL ne passent pas 
par le processus de persistance de Doctrine, et par conséquent, les événements de Doctrine ne sont pas impliqués.

### Création d'un EventSubscriber Doctrine 
   Commençons par la création d'un répertoire `EventSubscriber` dans `src` et à l'intérieur, créez une nouvelle classe `ContactSubscriber`.
   Cette classe écoutera les événements de Doctrine pour détecter les nouveaux enregistrements(insert).
   A la soumission du formulaire de contact que nous avons créé précédemment, on va vérifier que l'objet ou le corps du message contient le mot "RGPD". Si c'est le cas, 
   un email sera envoyé à l'administrateur du site velvet.
   
   Dans le fichier `src/EventSubscriber/ContactSubscriber.php`, ajoutons le contenu suivant :
   
   ```php
   
   <?php
   
   namespace App\EventSubscriber;
   
   use App\Entity\Contact;
   use Doctrine\Common\EventSubscriber;
   use Doctrine\ORM\Events;
   use Doctrine\Persistence\Event\LifecycleEventArgs;
   use Symfony\Component\Mailer\MailerInterface;
   use Symfony\Component\Mime\Email;
   
   class ContactSubscriber implements EventSubscriber
   {
       private $mailer;
   
       public function __construct(MailerInterface $mailer)
       {
           $this->mailer = $mailer;
       }
   
       public function getSubscribedEvents()
       {
           //retourne un tableau d'événements (prePersist, postPersist, preUpdate etc...)
           return [
               //événement déclenché après l'insert dans la base de donnée
               Events::postPersist,
           ];
       }
   
       public function postPersist(LifecycleEventArgs $args)
       {
   //        $args->getObjetc() nous retourne l'entité concernée par l'événement postPersist
           $entity = $args->getObject();
   
   
   //     Vérifier si l'entité est un nouvel objet de type Contact;
   //    Si l'objet persité n'est pas de type Contact, on ne veut pas que le Subscriber se déclenche!
           if ($entity instanceof \App\Entity\Contact) {
   
               $objet = $entity->getObjet();
               $message = $entity->getMessage();
   
               //Si l'objet ou le text du message contiennent le mot "rgpd", le Subscriber enverra un email à l'adresse "admin@velvet.com"
               if (preg_match("/rgpd\b/i", $objet) || preg_match("/rgpd\b/i", $message) ) {
                   //     Envoyer un e-mail à l'admin
                   $email = (new Email())
                       ->from('votre_adresse_email@example.com')
                       ->to('admin@velvet.com')
                       ->subject('Alerte RGPD')
                       ->text("Un nouveau message en rapport avec la loi sur les RGPD vous a été envoyé! L'id du message : " .$entity->getId(). " \n Objet du message : " .$entity->getObjet(). " \n Texte du message : " .$entity->getMessage());
   
                   $this->mailer->send($email);
               }
   
           }
       }
   }
   ```
   
   **Explications : **
   * `getSubscribedEvents()` - cette méthode retourne un tableau d'événements auxquels le subscriber doit s'abonner. L'événement est associé à la méthode `postPersist()` dans le Subscriber.
   * Si un nouvel objet de type `Contact` est persisté et que l'objet ou le corps de celui-ci contiennent le mot "RGPD", la fonction déclenché l'envoi d'un email à l'administrateur du site `velvet`. 
   * La fonction PHP `preg_match()` est utilisée pour chercher le mot "rgpd" (la regex contient un modificateur `i` qui indique une correspondance insensible à la casse et un `\b`, qui représente une limite de mot ("word boundary") pour s'assurer que le mot "rgpd" est trouvé comme un mot entier.   
   
   
   ### Configuration du Subscriber
   Mais avant d'utiliser le Subscriber, il faut le déclarer dans le fichier `services.yaml`.
   
   
   ```
   services:
       App\EventSubscriber\ContactSubscriber:
           arguments:
               $mailer: '@Symfony\Component\Mailer\MailerInterface'
           tags:
               - { name: doctrine.event_subscriber }
   ```            
   
   Cette déclaration utilise le tag `doctrine.event_subscriber` pour indiquer à Doctrine que la classe ContactSubscriber est un subscriber pour les événements spécifiés dans la méthode `getSubscribedEvents()`. 
   Cela permet à Doctrine de détecter ce subscriber et d'activer les écoutes pour les événements de la base de données correspondants.
   Elle spécifie les arguments qui seront passés au constructeur de la classe lors de son instanciation (ici, le Mailer)
   
   Testez le Subscriber dans votre projet! Vous pouvez modifier, si vous le souhaitez, l'action à effectuer dans la fonction `postPersist` ou ajouter 
   un autre type d'événement (ex. `preUpdate etc.)
   
   **Liens pour approfondir ce chapitre : **
   * [Événements de Symfony](https://symfony.com/doc/current/event_dispatcher.html)
   * [Évenements de Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/events.html#the-event-system)
   
   <br>