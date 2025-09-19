<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On utilise Faker pour générer des données réalistes
        $faker = Factory::create('fr_FR');

        // Création de 5 auteurs et de références pour les utiliser après
        $authors = [];
        for ($i = 0; $i < 5; $i++) {
            $author = new Author();
            $author->setFirstName($faker->firstName());
            $author->setLastName($faker->lastName());
            $author->setCountry($faker->country());
            $manager->persist($author);
            $authors[] = $author;
        }

        // Création de 5 éditeurs
        $editors = [];
        for ($i = 0; $i < 5; $i++) {
            $editor = new Editor();
            $editor->setName($faker->company());
            $editor->setHeadquarter($faker->city());
            $editor->setCreationDate($faker->dateTimeBetween('-50 years', 'now'));
            $manager->persist($editor);
            $editors[] = $editor;
        }

        $manager->flush();

        // Création de 100 livres
        for ($i = 0; $i < 100; $i++) {
            $book = new Book();
            $bookTitle = $faker->sentence(3);

            $book->setTitle($bookTitle);
            $book->setDescription($faker->paragraph(3));
            $book->setPages($faker->numberBetween(50, 800));

            // On gère la balise alt en créant une chaine de caractère valide
            $altText = urlencode(trim($bookTitle));
            $book->setImage('https://placehold.co/300x550?text=' . $altText);

            // On sélectionne un auteur et un éditeur au hasard
            $book->setAuthor($faker->randomElement($authors));
            $book->setEditor($faker->randomElement($editors));

            $manager->persist($book);
        }

        $manager->flush();
    }
}
