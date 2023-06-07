<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\Disc;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArtistFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

//        for ($i = 0; $i < 10; ++$i) {
//            $artist = new Artist();
//
//            $artist->setName("Queens Of The Stone Age");
//            $artist->setUrl("https://qotsa.com/");
//            /**
//             * this is where we set the refence
//             */
//            $this->addReference('artist_'.$i, $artist);
//
//            $manager->persist($artist);
//            $manager->flush();
//        }
    }
}
