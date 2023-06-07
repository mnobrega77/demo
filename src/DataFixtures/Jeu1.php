<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\Disc;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Jeu1 extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $artist1 = new Artist();

        $artist1->setName("Queens Of The Stone Age");
        $artist1->setUrl("https://qotsa.com/");
        $manager->persist($artist1);

        $artist2 = new Artist();

        $artist2->setName("Rolling Stones");
        $artist2->setUrl("https://rollingstones.com/");
        $manager->persist($artist2);


        $artist3 = new Artist();

        $artist3->setName("Neil Young");
        $artist3->setUrl("https://neilyoung.warnerartists.net/gb/");
        $manager->persist($artist3);


        $disc1 = new Disc();
        $disc1->setTitle("Songs for the Deaf");
        $disc1->setPicture("https://en.wikipedia.org/wiki/Songs_for_the_Deaf#/media/File:Queens_of_the_Stone_Age_-_Songs_for_the_Deaf.png");
        $disc1->setLabel("Interscope Records");
        $manager->persist($disc1);

        $disc2 = new Disc();
        $disc2->setTitle("Harvest Moon");
        $disc2->setPicture("Harvest Moon.jpeg");
        $disc2->setLabel("Reprise Records");
        $manager->persist($disc2);

        $disc3 = new Disc();
        $disc3->setTitle("Broken Arrow");
        $disc3->setPicture("Broken Arrow.jpeg");
        $disc3->setLabel("Reprise Records");
        $manager->persist($disc3);

        $disc4 = new Disc();
        $disc4->setTitle("Year of the Horse");
        $disc4->setPicture("Year of the Horse.jpeg");
        $disc4->setLabel("Reprise Records");
        $manager->persist($disc4);

// Pour associer vos entités
        $disc1->setArtist($artist1);
        $disc2->setArtist($artist3);
        $disc3->setArtist($artist3);
        $disc4->setArtist($artist3);
        $manager->flush();
    }
}
