<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use App\Repository\DiscRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{

    //On va avoir souvent besoin d'injecter les respositories de nos entités dans les contrôleurs
    //Pour ne pas les injecter dans chaque fonction, on va les instancier une seule fois dans le constructeur de notre contrôleur;
    //On fait de même avec les autres services dont nous aurons besoin (EntityManager, Mailer etc.)
    //N'oubliez pas d'importer vos respositories (les lignes "use..." en haut de la page

    private $artistRepo;
    private $discRepo;
    private $em;
    public function __construct(ArtistRepository $artistRepo, DiscRepository $discRepo, EntityManagerInterface $em){
        $this->artistRepo = $artistRepo;
        $this->discRepo = $discRepo;
        $this->em = $em;

    }
    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {
        $ats = $this->artistRepo->getSomeArtists("Neil");
//        $ats = $this->artistRepo->getSomeArtists("Neil");
        dd($ats);
        //afficher tous les artistes et tous leurs disques:
        //on appelle la fonction `findAll()` du repository de la classe Artist afin de récupérer tous les artists de la base de données;
        $artistes = $this->artistRepo->findAll();

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            //on va envoyer à la vue notre variable - $artistes
            'artistes' => $artistes
        ]);
    }

    #[Route('/artist/{id}/update', name: 'artist_update')]
public function updateArtist(){

    }


}
