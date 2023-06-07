<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\Disc;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DiscFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

//        for ($i = 0; $i < 10; ++$i) {
//
//            $disc = new Disc();
//            $disc->setTitle("Songs for the Deaf");
//            $disc->setPicture("https://en.wikipedia.org/wiki/Songs_for_the_Deaf#/media/File:Queens_of_the_Stone_Age_-_Songs_for_the_Deaf.png");
//            $disc->setLabel("Interscope Records");
//            $disc->setArtist($this->getReference('artist_' . rand(0, 9)));
//
//            $manager->persist($disc);
//
//            $manager->flush();
//        }


    }

//    public function getDependencies(): array
//    {
//        return [
//            ArtistFixtures::class,
//        ];
//    }
}
