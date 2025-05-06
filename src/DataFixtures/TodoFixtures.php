<?php

namespace App\DataFixtures;

use App\Factory\TodoFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TodoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        TodoFactory::createMany(5);

        $manager->flush();
    }
}
