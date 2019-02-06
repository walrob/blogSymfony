<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $technologyCategory = new Category();
        $technologyCategory->setName('Tecnología');
        $technologyCategory->setDescription('Todos los temas relacionados con la Tecnología');

        $sportCategory = new Category();
        $sportCategory->setName('Deporte');
        $sportCategory->setDescription('Todos los temas relacionados con el Deporte');

        $politicsCategory = new Category();
        $politicsCategory->setName('Política');
        $politicsCategory->setDescription('Todos los temas relacionados con la Política');

        $economyCategory = new Category();
        $economyCategory->setName('Economía');
        $economyCategory->setDescription('Todos los temas relacionados con la Economía');

        $manager->persist($technologyCategory);
        $manager->persist($sportCategory);
        $manager->persist($politicsCategory);
        $manager->persist($economyCategory);

        $manager->flush();

        $this->addReference('category-1', $technologyCategory);
        $this->addReference('category-2', $sportCategory);
        $this->addReference('category-3', $politicsCategory);
        $this->addReference('category-4', $economyCategory);
    }
}