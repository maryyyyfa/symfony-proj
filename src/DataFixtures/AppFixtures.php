<?php

namespace App\DataFixtures;

use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $course1 = new Course();
        $course1->setTitle('Introduction à PHP');
        $course1->setDescription('Apprenez les bases de PHP, un langage de programmation populaire.');
        $course1->setCategory('Programmation');
        $course1->setContent('<h2>Introduction</h2><p>PHP est un langage de programmation...</p>');
        $manager->persist($course1);

        $course2 = new Course();
        $course2->setTitle('Symfony pour les débutants');
        $course2->setDescription('Découvrez le framework Symfony et ses fonctionnalités.');
        $course2->setCategory('Framework');
        $course2->setContent('<h2>Qu\'est-ce que Symfony ?</h2><p>Symfony est un framework PHP...</p>');
        $manager->persist($course2);

        $manager->flush();
    }
} 