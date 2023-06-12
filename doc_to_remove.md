<br>

## Les Evenemets Doctrine

###Objectifs pédagogiques
À la fin de ce cours, vous devriez être en mesure de :
* comprendre le concept d'EventSubscribers dans Symfony
* utiliser les Events de Doctrine pour détecter les changements dans la base de données
* créer un EventSubscriber p

###Prérequis
Avoir une connaissance de base de Symfony et de Doctrine.

### Partie 1 : Comprendre les EventSubscribers
Qu'est-ce qu'un EventSubscriber ?
Un EventSubscriber est une classe qui écoute les événements (Events) et exécute des actions en réponse à ces événements. 
Il s'agit d'une fonctionnalité puissante de Symfony pour gérer les événements et effectuer des actions spécifiques.

#### Les Events de Doctrine
Doctrine émet des événements lors de diverses opérations de la base de données, telles que la création, la mise à jour ou la suppression d'entités. 
Ces événements nous permettent d'intercepter ces opérations et d'effectuer des actions supplémentaires.

#### Différence entre EventSubscriber et EventListener
Un EventSubscriber peut écouter plusieurs événements, tandis qu'un EventListener écoute un événement spécifique.

#### Création de l'EventSubscriber 
Créez une nouvelle classe `NewRecordSubscriber` dans le répertoire `src/EventSubscriber`.
Cette classe écoutera les événements de Doctrine pour détecter les nouveaux enregistrements.

Dans le fichier `src/EventSubscriber/NewRecordSubscriber.php`, ajoutez le contenu suivant :

``php

<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NewRecordSubscriber implements EventSubscriber
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // Vérifier si l'entité est un nouveau disque
        if ($entity instanceof \App\Entity\Disque) {
            // Envoyer un e-mail
            $email = (new Email())
                ->from('votre_adresse_email@example.com')
                ->to('destinataire@example.com')
                ->subject('Nouveau disque ajouté')
                ->text('Un nouveau disque a été ajouté à la base de données.');

            $this->mailer->send($email);
        }
    }
}
#### Configuration des services
Ouvrez le fichier `onfig/services.yaml` et ajoutez les services suivants pour enregistrer notre `EventSubscriber` :

```

services:
    App\EventSubscriber\NewRecordSubscriber:
        arguments:
            $mailer: '@Symfony\Component\Mailer\MailerInterface'
        tags:
            - { name: doctrine.event_subscriber }
``            
Il est important de comprendre les différences entre les événements de Symfony et ceux de Doctrine. Voici une explication pour clarifier ces différences :

Les événements de Symfony et de Doctrine sont deux systèmes d'événements distincts, bien qu'ils soient souvent utilisés ensemble dans une application Symfony.

#### Événements de Symfony :
Les événements de Symfony sont le mécanisme de base pour gérer les événements au sein du framework Symfony. Ils sont utilisés pour déclencher des actions spécifiques en réponse à des événements survenant dans l'application.
Les événements de Symfony couvrent une large gamme de fonctionnalités, allant des événements liés à la gestion des requêtes HTTP (par exemple, l'événement "kernel.request") aux événements liés aux formulaires, à la sécurité, aux notifications, etc.

Les événements de Symfony sont généralement déclenchés et écoutés à des moments précis du cycle de vie de l'application Symfony. Ils sont gérés par le composant "EventDispatcher" de Symfony, qui est responsable de la gestion des abonnements aux événements et de l'exécution des écouteurs associés.

#### Événements de Doctrine :
Les événements de Doctrine, quant à eux, sont spécifiques à l'ORM (Object-Relational Mapping) Doctrine. Ils sont utilisés pour écouter et réagir aux opérations de la base de données effectuées par Doctrine, telles que la création, la mise à jour, la suppression ou le chargement d'entités.
Doctrine émet des événements lors de différentes étapes du cycle de vie d'une entité, par exemple, "postPersist" après l'insertion d'une entité dans la base de données, "preUpdate" avant la mise à jour d'une entité, etc.

Les événements de Doctrine permettent aux développeurs d'intercepter et de réagir à ces opérations de base de données en exécutant des actions supplémentaires. Par exemple, vous pouvez envoyer un e-mail, mettre à jour une autre entité ou effectuer toute autre logique métier nécessaire.

Il est important de noter que bien que les événements de Symfony et de Doctrine soient distincts, Symfony fournit des moyens d'intégrer les événements de Doctrine dans le système d'événements de Symfony. Ainsi, vous pouvez créer des EventSubscribers dans Symfony pour écouter à la fois les événements de Symfony et les événements de Doctrine, et agir en conséquence.

J'espère que cela clarifie les différences entre les événements de Symfony et ceux de Doctrine. N'hésitez pas à me poser d'autres questions si vous en avez besoin !




#### Listeners vs Subscribers ?
Les listeners et les subscribers sont deux concepts liés aux événements dans Symfony, mais ils ont des différences de fonctionnement.

Listeners (Écouteurs) :
Un listener, ou un EventListener, est une classe qui écoute un événement spécifique et exécute une action en réponse à cet événement. Chaque listener est associé à un événement particulier et peut réagir uniquement à cet événement.
Pour créer un listener, vous devez implémenter l'interface EventListenerInterface de Symfony et définir la méthode onEvent() pour gérer l'événement. Ensuite, vous devez enregistrer le listener dans le conteneur de services de Symfony et spécifier l'événement auquel il doit être associé.

Un listener est spécifique à un événement et ne peut écouter qu'un seul événement à la fois. Si vous souhaitez écouter plusieurs événements, vous devez créer un listener distinct pour chaque événement.

Subscribers (Abonnés) :
Un subscriber, ou un EventSubscriber, est une classe qui écoute plusieurs événements et peut réagir à chacun de ces événements en exécutant une ou plusieurs actions. Contrairement aux listeners, un subscriber peut gérer plusieurs événements.
Pour créer un subscriber, vous devez implémenter l'interface EventSubscriberInterface de Symfony et définir la méthode getSubscribedEvents(). Cette méthode retourne un tableau associatif où vous spécifiez les événements auxquels le subscriber doit être abonné et les méthodes qui seront appelées en réponse à ces événements.

Un subscriber est plus flexible car il peut écouter et réagir à plusieurs événements avec une seule classe. Cela permet de regrouper la logique liée à plusieurs événements au sein d'une même classe, ce qui facilite la gestion et la maintenance du code.

En résumé, la différence principale entre les listeners et les subscribers réside dans leur capacité à gérer les événements. Les listeners sont spécifiques à un seul événement, tandis que les subscribers peuvent gérer plusieurs événements en regroupant la logique liée à ces événements dans une seule classe.

Dans Symfony, vous pouvez utiliser à la fois des listeners et des subscribers en fonction des besoins de votre application. Les listeners sont utiles lorsque vous avez des actions spécifiques à exécuter pour un événement donné, tandis que les subscribers sont plus adaptés lorsque vous devez regrouper la logique liée à plusieurs événements.





User
Doit-on les passer au fichier service.yaml dans symfony?
ChatGPT
Oui, pour que les listeners et les subscribers soient utilisés et fonctionnent correctement dans Symfony, il est nécessaire de les déclarer dans le fichier services.yaml (ou dans un fichier de configuration séparé, inclus dans services.yaml).

Voici comment vous pouvez déclarer un listener et un subscriber dans le fichier services.yaml :

Déclaration d'un Listener :
Supposons que vous avez un listener nommé App\EventListener\MyListener qui écoute l'événement kernel.request. Voici comment vous pouvez le déclarer dans services.yaml :

yaml
Copy code
services:
    App\EventListener\MyListener:
        tags:
            - { name: kernel.event_listener, event: kernel.request }
Cette déclaration indique à Symfony de prendre en compte la classe MyListener en tant que listener de l'événement kernel.request. Symfony détectera automatiquement ce tag et activera le listener pour l'événement correspondant.

Déclaration d'un Subscriber :
Supposons que vous avez un subscriber nommé App\EventSubscriber\MySubscriber qui écoute les événements kernel.request et kernel.response. Voici comment vous pouvez le déclarer dans services.yaml :

yaml
Copy code
services:
    App\EventSubscriber\MySubscriber:
        tags:
            - { name: kernel.event_subscriber }
Cette déclaration utilise le tag kernel.event_subscriber, qui indique à Symfony que la classe MySubscriber est un subscriber. Symfony détectera automatiquement ce tag et activera le subscriber pour les événements spécifiés dans la méthode getSubscribedEvents() de la classe MySubscriber.

En résumé, pour que les listeners et les subscribers soient utilisés dans Symfony, vous devez les déclarer dans le fichier services.yaml en utilisant les tags appropriés (kernel.event_listener pour les listeners et kernel.event_subscriber pour les subscribers). Cela permet à Symfony de les reconnaître et de les intégrer dans le système d'événements de l'application.





User
Même les listeners et evenements doctrine doivent être dans le service.yaml?
ChatGPT
Oui, même les listeners et les événements de Doctrine doivent être déclarés dans le fichier services.yaml de Symfony.

Lorsque vous utilisez des listeners ou des subscribers pour écouter les événements de Doctrine, vous devez les enregistrer en tant que services dans le conteneur de services de Symfony.

Voici comment vous pouvez déclarer un listener ou un subscriber pour les événements de Doctrine dans le fichier services.yaml :

Déclaration d'un Listener Doctrine :
Supposons que vous avez un listener Doctrine nommé App\EventListener\DoctrineListener qui écoute l'événement postPersist. Voici comment vous pouvez le déclarer dans services.yaml :

yaml
Copy code
services:
    App\EventListener\DoctrineListener:
        tags:
            - { name: doctrine.event_listener, event: postPersist }
Cette déclaration utilise le tag doctrine.event_listener pour indiquer à Doctrine que la classe DoctrineListener est un listener pour l'événement postPersist. Cela permet à Doctrine de prendre en compte ce listener lors de la gestion des événements de la base de données.

Déclaration d'un Subscriber Doctrine :
Supposons que vous avez un subscriber Doctrine nommé App\EventSubscriber\DoctrineSubscriber qui écoute les événements preUpdate et postFlush. Voici comment vous pouvez le déclarer dans services.yaml :

yaml
Copy code
services:
    App\EventSubscriber\DoctrineSubscriber:
        tags:
            - { name: doctrine.event_subscriber }
Cette déclaration utilise le tag doctrine.event_subscriber pour indiquer à Doctrine que la classe DoctrineSubscriber est un subscriber pour les événements spécifiés dans la méthode getSubscribedEvents(). Cela permet à Doctrine de détecter ce subscriber et d'activer les écoutes pour les événements de la base de données correspondants.

En résumé, pour que les listeners et les subscribers liés aux événements de Doctrine fonctionnent correctement, vous devez les déclarer dans le fichier services.yaml de Symfony en utilisant les tags doctrine.event_listener pour les listeners et doctrine.event_subscriber pour les subscribers. Cela permet à Doctrine de les reconnaître et de les utiliser lors de la gestion des événements de la base de données.





User
Et le unitOfWork, qu'en est-il dans les evenements doctrine?
ChatGPT
Le Unit of Work (Unité de travail) est un concept clé dans Doctrine et il joue également un rôle important dans les événements de Doctrine.

Le Unit of Work est responsable de la gestion des opérations de persistance des objets et des transactions au sein de Doctrine. Il suit les modifications apportées aux entités et les synchronise avec la base de données lorsqu'une transaction est validée.

Lorsque vous utilisez des événements de Doctrine tels que postPersist, preUpdate, postFlush, etc., le Unit of Work intervient pour gérer la gestion des entités et des transactions. Voici comment cela se déroule :

Lorsqu'un événement de Doctrine est déclenché (par exemple, postPersist après l'insertion d'une entité dans la base de données), le Unit of Work est informé de cet événement.

Le Unit of Work vérifie les modifications apportées aux entités et les organise pour être synchronisées avec la base de données lors de la prochaine validation de la transaction.

Le Unit of Work exécute les requêtes nécessaires pour persister les entités modifiées dans la base de données. Par exemple, lors du postPersist, il exécutera une requête d'insertion pour ajouter l'entité dans la table correspondante.

Une fois que toutes les opérations ont été exécutées avec succès, la transaction est validée et les modifications sont effectivement persistées dans la base de données.

Il est important de noter que le Unit of Work travaille en arrière-plan pour gérer ces opérations, et les événements de Doctrine vous permettent d'intercepter ces opérations et d'effectuer des actions supplémentaires en réponse à ces événements.

Vous pouvez utiliser les événements de Doctrine pour effectuer des tâches supplémentaires lors des opérations de persistance, telles que l'envoi d'e-mails, la mise à jour d'autres entités, l'enregistrement d'audit, etc. Les événements de Doctrine offrent une flexibilité pour étendre le comportement de Doctrine et ajouter des fonctionnalités personnalisées à votre application.

En résumé, le Unit of Work est étroitement lié aux événements de Doctrine. Il gère la synchronisation des entités avec la base de données et permet d'intercepter et de réagir à ces opérations grâce aux événements de Doctrine. Cela vous permet d'effectuer des actions supplémentaires lors des opérations de persistance des entités.





User
Tu peux me dire quelque chose sur les différents events de doctrine? postpersist, onflush etc
ChatGPT
Bien sûr ! Voici quelques-uns des événements de Doctrine les plus couramment utilisés :

prePersist : Cet événement est déclenché juste avant qu'une entité soit persistée pour la première fois dans la base de données. Il peut être utilisé pour effectuer des actions avant l'insertion d'une nouvelle entité, comme la génération de valeurs par défaut, la validation supplémentaire, etc.

postPersist : Cet événement est déclenché après l'insertion d'une entité dans la base de données. Il est utile pour effectuer des actions supplémentaires une fois que l'entité a été persistée, telles que l'envoi d'e-mails, la mise à jour de caches, etc.

preUpdate : Cet événement est déclenché avant la mise à jour d'une entité dans la base de données. Il permet de prendre des mesures avant que les modifications ne soient persistées, comme la validation supplémentaire, la modification d'autres entités liées, etc.

postUpdate : Cet événement est déclenché après la mise à jour d'une entité dans la base de données. Il peut être utilisé pour effectuer des actions supplémentaires après la mise à jour de l'entité, comme l'enregistrement d'audits, la notification des utilisateurs, etc.

preRemove : Cet événement est déclenché avant la suppression d'une entité de la base de données. Il peut être utilisé pour effectuer des actions avant la suppression, telles que la validation, la vérification des dépendances, etc.

postRemove : Cet événement est déclenché après la suppression d'une entité de la base de données. Il est utile pour effectuer des actions supplémentaires après la suppression de l'entité, comme la suppression des fichiers associés, la mise à jour d'autres entités, etc.

onFlush : Cet événement est déclenché avant la validation et la persistance des objets dans la base de données. Il permet de travailler avec l'ensemble des modifications en cours avant qu'elles ne soient exécutées.

Ces événements de Doctrine offrent une flexibilité pour interagir avec les opérations de persistance et effectuer des actions supplémentaires à des moments clés du cycle de vie des entités. Ils permettent de gérer des tâches spécifiques, telles que l'envoi de notifications, la gestion des relations, la gestion des dépendances, etc., en réaction aux changements dans la base de données.

Vous pouvez utiliser ces événements en tant que listeners ou subscribers de Doctrine pour exécuter des actions supplémentaires selon vos besoins métier.

N'hésitez pas à me poser d'autres questions si vous en avez !





User
est-ce que ces evenements cités réagissent meême au niveau d'un script sql?
ChatGPT
Non, les événements de Doctrine sont spécifiques à l'ORM (Object-Relational Mapping) de Doctrine et ne réagissent pas aux scripts SQL exécutés directement sur la base de données.

Lorsque vous utilisez Doctrine, vous travaillez avec des entités et le concept de mapping objet-relationnel, où les opérations de persistance sont gérées par le biais du Unit of Work de Doctrine. Les événements de Doctrine sont conçus pour intercepter et réagir aux opérations effectuées sur les entités avant ou après leur persistance dans la base de données.

Les événements de Doctrine ne sont pas déclenchés lorsque vous exécutez des scripts SQL bruts (par exemple, en utilisant la méthode executeQuery() de Doctrine DBAL). Ces scripts SQL ne passent pas par le processus de persistance de Doctrine, et par conséquent, les événements de Doctrine ne sont pas impliqués.

Si vous souhaitez exécuter des actions spécifiques en réaction à des scripts SQL exécutés directement sur la base de données, vous devrez le faire en dehors du contexte des événements de Doctrine. Vous pouvez simplement exécuter les scripts SQL nécessaires et ensuite effectuer les actions supplémentaires requises dans votre code après l'exécution du script.

Il est important de comprendre que les événements de Doctrine sont spécifiques à l'interaction entre les entités et la base de données via l'ORM de Doctrine. Ils ne s'appliquent pas directement aux opérations SQL brutes exécutées en dehors de ce contexte.