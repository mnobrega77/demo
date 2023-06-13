<br>

## Les Evenemets Doctrine

### Objectifs pédagogiques
À la fin de ce cours, vous devriez être en mesure de :
* comprendre le concept d'EventSubscribers dans Symfony
* utiliser les Events de Doctrine pour détecter les changements dans la base de données
* créer un EventSubscriber p

### Prérequis
Avoir une connaissance de base de Symfony et de Doctrine.

### Partie 1 : Comprendre les EventSubscribers
Qu'est-ce qu'un EventSubscriber ?
Un EventSubscriber est une classe qui écoute les événements (Events) et exécute des actions en réponse à ces événements. 
Il s'agit d'une fonctionnalité puissante de Symfony pour gérer les événements et effectuer des actions spécifiques.

#### Différences entre les événements de Symfony et ceux de Doctrine :

Bien qu'ils soient souvent utilisés ensemble dans une application Symfony, les événements de Symfony et de Doctrine sont deux systèmes d'événements distincts.

#### Événements de Symfony :
Les événements de Symfony sont le mécanisme de base pour gérer les événements au sein du framework Symfony. Ils sont utilisés pour déclencher des actions spécifiques en réponse à des événements survenant dans l'application.
Les événements de Symfony couvrent une large gamme de fonctionnalités, allant des événements liés à la gestion des requêtes HTTP (par exemple, l'événement "kernel.request") aux événements liés aux formulaires, à la sécurité, aux notifications, etc.

Les événements de Symfony sont généralement déclenchés et écoutés à des moments précis du cycle de vie de l'application Symfony. Ils sont gérés par le composant "EventDispatcher" de Symfony, qui est responsable de la gestion des abonnements aux événements et de l'exécution des écouteurs associés.

#### Événements de Doctrine :
Les événements de Doctrine, quant à eux, sont spécifiques à l'ORM (Object-Relational Mapping) Doctrine. Ils sont utilisés pour écouter et réagir aux opérations de la base de données effectuées par Doctrine, telles que la création, la mise à jour, la suppression ou le chargement d'entités.
Doctrine émet des événements lors de différentes étapes du cycle de vie d'une entité, par exemple, "postPersist" après l'insertion d'une entité dans la base de données, "preUpdate" avant la mise à jour d'une entité, etc.

Les événements de Doctrine permettent aux développeurs d'intercepter et de réagir à ces opérations de base de données en exécutant des actions supplémentaires. Par exemple, vous pouvez envoyer un e-mail, mettre à jour une autre entité ou effectuer toute autre logique métier nécessaire.

Même si les deux type d'événéments sont distincts, Symfony fournit des moyens d'intégrer les événements de Doctrine dans son système d'événements.
Ainsi, vous pouvez créer des EventSubscribers dans Symfony pour écouter à la fois les événements de Symfony et les événements de Doctrine, et agir en conséquence.


#### Différence entre EventSubscriber et EventListener
Un EventSubscriber peut écouter plusieurs événements, tandis qu'un EventListener écoute un événement spécifique.
Un subscriber est plus flexible car il peut écouter et réagir à plusieurs événements avec une seule classe. Cela permet de regrouper la logique liée à plusieurs événements au sein d'une même classe, ce qui facilite la gestion et la maintenance du code.

Dans Symfony, vous pouvez utiliser à la fois des listeners et des subscribers en fonction des besoins de votre application. 

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
Ouvrez le fichier `config/services.yaml` et ajoutez les services suivants pour enregistrer notre `EventSubscriber` :

```

services:
    App\EventSubscriber\NewRecordSubscriber:
        arguments:
            $mailer: '@Symfony\Component\Mailer\MailerInterface'
        tags:
            - { name: doctrine.event_subscriber }
``            

Pour que les listeners et les subscribers soient utilisés et fonctionnent correctement dans Symfony, il est nécessaire de les déclarer dans le fichier services.yaml (ou dans un fichier de configuration séparé, inclus dans services.yaml).

Voici comment vous pouvez déclarer un listener et un subscriber dans le fichier services.yaml :

Déclaration d'un Listener :
Supposons que vous avez un listener nommé App\EventListener\MyListener qui écoute l'événement kernel.request. Voici comment vous pouvez le déclarer dans services.yaml :

```

Déclaration d'un Subscriber Doctrine :
Supposons que vous avez un subscriber Doctrine nommé App\EventSubscriber\DoctrineSubscriber qui écoute les événements preUpdate et postFlush. Voici comment vous pouvez le déclarer dans services.yaml :

```
services:
    App\EventSubscriber\DoctrineSubscriber: 
        tags:
            - { name: doctrine.event_subscriber }
```
Cette déclaration utilise le tag `doctrine.event_subscriber` pour indiquer à Doctrine que la classe DoctrineSubscriber est un subscriber pour les événements spécifiés dans la méthode `getSubscribedEvents()`. Cela permet à Doctrine de détecter ce subscriber et d'activer les écoutes pour les événements de la base de données correspondants.


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

Voici quelques-uns des événements de Doctrine les plus couramment utilisés :

prePersist : Cet événement est déclenché juste avant qu'une entité soit persistée pour la première fois dans la base de données. Il peut être utilisé pour effectuer des actions avant l'insertion d'une nouvelle entité, comme la génération de valeurs par défaut, la validation supplémentaire, etc.

postPersist : Cet événement est déclenché après l'insertion d'une entité dans la base de données. Il est utile pour effectuer des actions supplémentaires une fois que l'entité a été persistée, telles que l'envoi d'e-mails, la mise à jour de caches, etc.

preUpdate : Cet événement est déclenché avant la mise à jour d'une entité dans la base de données. Il permet de prendre des mesures avant que les modifications ne soient persistées, comme la validation supplémentaire, la modification d'autres entités liées, etc.

postUpdate : Cet événement est déclenché après la mise à jour d'une entité dans la base de données. Il peut être utilisé pour effectuer des actions supplémentaires après la mise à jour de l'entité, comme l'enregistrement d'audits, la notification des utilisateurs, etc.

preRemove : Cet événement est déclenché avant la suppression d'une entité de la base de données. Il peut être utilisé pour effectuer des actions avant la suppression, telles que la validation, la vérification des dépendances, etc.

postRemove : Cet événement est déclenché après la suppression d'une entité de la base de données. Il est utile pour effectuer des actions supplémentaires après la suppression de l'entité, comme la suppression des fichiers associés, la mise à jour d'autres entités, etc.

onFlush : Cet événement est déclenché avant la validation et la persistance des objets dans la base de données. Il permet de travailler avec l'ensemble des modifications en cours avant qu'elles ne soient exécutées.

Ces événements de Doctrine offrent une flexibilité pour interagir avec les opérations de persistance et effectuer des actions supplémentaires à des moments clés du cycle de vie des entités. Ils permettent de gérer des tâches spécifiques, telles que l'envoi de notifications, la gestion des relations, la gestion des dépendances, etc., en réaction aux changements dans la base de données.

Vous pouvez utiliser ces événements en tant que listeners ou subscribers de Doctrine pour exécuter des actions supplémentaires selon vos besoins métier.


### les événements de Doctrine sont spécifiques à l'ORM (Object-Relational Mapping) de Doctrine et ne réagissent pas aux scripts SQL exécutés directement sur la base de données.

Lorsque vous utilisez Doctrine, vous travaillez avec des entités et le concept de mapping objet-relationnel, où les opérations de persistance sont gérées par le biais du Unit of Work de Doctrine. Les événements de Doctrine sont conçus pour intercepter et réagir aux opérations effectuées sur les entités avant ou après leur persistance dans la base de données.

Les événements de Doctrine ne sont pas déclenchés lorsque vous exécutez des scripts SQL bruts (par exemple, en utilisant la méthode executeQuery() de Doctrine DBAL). Ces scripts SQL ne passent pas par le processus de persistance de Doctrine, et par conséquent, les événements de Doctrine ne sont pas impliqués.

Si vous souhaitez exécuter des actions spécifiques en réaction à des scripts SQL exécutés directement sur la base de données, vous devrez le faire en dehors du contexte des événements de Doctrine. Vous pouvez simplement exécuter les scripts SQL nécessaires et ensuite effectuer les actions supplémentaires requises dans votre code après l'exécution du script.

Il est important de comprendre que les événements de Doctrine sont spécifiques à l'interaction entre les entités et la base de données via l'ORM de Doctrine. Ils ne s'appliquent pas directement aux opérations SQL brutes exécutées en dehors de ce contexte.