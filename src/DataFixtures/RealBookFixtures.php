<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RealBookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Récupérer les utilisateurs
        $userRepo = $manager->getRepository(User::class);
        $users = $userRepo->findAll();

        if (empty($users)) {
            throw new \Exception('Aucun utilisateur trouvé. Assurez-vous que AppFixtures a été chargé en premier.');
        }

        // Utiliser le premier utilisateur (test@test.fr) pour la collection
        $user = $users[0];
        // Création des auteurs français célèbres
        $authors = [
            'Victor Hugo' => $this->createAuthor($manager, 'Victor', 'Hugo'),
            'Albert Camus' => $this->createAuthor($manager, 'Albert', 'Camus'),
            'Antoine de Saint-Exupéry' => $this->createAuthor($manager, 'Antoine', 'de Saint-Exupéry'),
            'Alexandre Dumas' => $this->createAuthor($manager, 'Alexandre', 'Dumas'),
            'Jules Verne' => $this->createAuthor($manager, 'Jules', 'Verne'),
            'Émile Zola' => $this->createAuthor($manager, 'Émile', 'Zola'),
            'Gustave Flaubert' => $this->createAuthor($manager, 'Gustave', 'Flaubert'),
            'Marcel Pagnol' => $this->createAuthor($manager, 'Marcel', 'Pagnol'),
            'George Sand' => $this->createAuthor($manager, 'George', 'Sand'),
            'Honoré de Balzac' => $this->createAuthor($manager, 'Honoré', 'de Balzac'),
            'Molière' => $this->createAuthor($manager, 'Jean-Baptiste', 'Poquelin'),
            'Jean-Paul Sartre' => $this->createAuthor($manager, 'Jean-Paul', 'Sartre'),
            'Simone de Beauvoir' => $this->createAuthor($manager, 'Simone', 'de Beauvoir'),
            'André Gide' => $this->createAuthor($manager, 'André', 'Gide'),
            'Guy de Maupassant' => $this->createAuthor($manager, 'Guy', 'de Maupassant'),
            'Stendhal' => $this->createAuthor($manager, 'Henri', 'Beyle'),
            'Marcel Proust' => $this->createAuthor($manager, 'Marcel', 'Proust'),
            'François Rabelais' => $this->createAuthor($manager, 'François', 'Rabelais'),
            'Voltaire' => $this->createAuthor($manager, 'François-Marie', 'Arouet'),
            'Jean de La Fontaine' => $this->createAuthor($manager, 'Jean', 'de La Fontaine'),
            'Charles Baudelaire' => $this->createAuthor($manager, 'Charles', 'Baudelaire'),
        ];

        // Création des éditeurs français
        $editors = [
            'Gallimard' => $this->createEditor($manager, 'Gallimard'),
            'Hachette' => $this->createEditor($manager, 'Hachette'),
            'Le Livre de Poche' => $this->createEditor($manager, 'Le Livre de Poche'),
            'Folio' => $this->createEditor($manager, 'Folio'),
            'Pocket' => $this->createEditor($manager, 'Pocket'),
        ];

        // Liste de vrais livres français célèbres
        $books = [
            [
                'title' => 'Les Misérables',
                'author' => $authors['Victor Hugo'],
                'editor' => $editors['Le Livre de Poche'],
                'pages' => 1664,
                'description' => 'L\'histoire de Jean Valjean, ancien forçat devenu honnête homme, poursuivi par l\'inspecteur Javert. Un chef-d\'œuvre de la littérature française qui explore les thèmes de la justice, de la rédemption et de l\'amour.',
                'image' => 'https://covers.openlibrary.org/b/id/8235470-L.jpg'
            ],
            [
                'title' => 'L\'Étranger',
                'author' => $authors['Albert Camus'],
                'editor' => $editors['Gallimard'],
                'pages' => 186,
                'description' => 'L\'histoire de Meursault, un homme indifférent qui tue un Arabe sur une plage algéroise. Un roman existentialiste sur l\'absurde et l\'aliénation.',
                'image' => 'https://covers.openlibrary.org/b/id/8339070-L.jpg'
            ],
            [
                'title' => 'Le Petit Prince',
                'author' => $authors['Antoine de Saint-Exupéry'],
                'editor' => $editors['Gallimard'],
                'pages' => 96,
                'description' => 'Un conte philosophique et poétique qui raconte la rencontre d\'un aviateur avec un jeune prince venu d\'un astéroïde. Une œuvre universelle sur l\'enfance, l\'amitié et l\'amour.',
                'image' => 'https://covers.openlibrary.org/b/id/8509266-L.jpg'
            ],
            [
                'title' => 'Le Comte de Monte-Cristo',
                'author' => $authors['Alexandre Dumas'],
                'editor' => $editors['Le Livre de Poche'],
                'pages' => 1344,
                'description' => 'L\'histoire d\'Edmond Dantès, injustement emprisonné, qui s\'évade et revient pour se venger de ceux qui l\'ont trahi. Un roman d\'aventures captivant sur la vengeance et la justice.',
                'image' => 'https://covers.openlibrary.org/b/id/8225181-L.jpg'
            ],
            [
                'title' => 'Vingt mille lieues sous les mers',
                'author' => $authors['Jules Verne'],
                'editor' => $editors['Hachette'],
                'pages' => 424,
                'description' => 'Les aventures du capitaine Nemo et de son sous-marin le Nautilus. Un voyage extraordinaire dans les profondeurs des océans, précurseur de la science-fiction.',
                'image' => 'https://covers.openlibrary.org/b/id/8225365-L.jpg'
            ],
            [
                'title' => 'Germinal',
                'author' => $authors['Émile Zola'],
                'editor' => $editors['Folio'],
                'pages' => 591,
                'description' => 'L\'histoire d\'Étienne Lantier, un jeune ouvrier qui participe à une grève de mineurs. Un roman naturaliste puissant sur la condition ouvrière au XIXe siècle.',
                'image' => 'https://covers.openlibrary.org/b/id/8225501-L.jpg'
            ],
            [
                'title' => 'Madame Bovary',
                'author' => $authors['Gustave Flaubert'],
                'editor' => $editors['Folio'],
                'pages' => 528,
                'description' => 'L\'histoire d\'Emma Bovary, une femme de province qui rêve d\'une vie plus passionnante et sombre dans l\'adultère. Un chef-d\'œuvre du réalisme français.',
                'image' => 'https://covers.openlibrary.org/b/id/8225602-L.jpg'
            ],
            [
                'title' => 'La Gloire de mon père',
                'author' => $authors['Marcel Pagnol'],
                'editor' => $editors['Pocket'],
                'pages' => 256,
                'description' => 'Les souvenirs d\'enfance de Marcel Pagnol en Provence. Un récit tendre et nostalgique qui évoque la beauté de la nature et les liens familiaux.',
                'image' => 'https://covers.openlibrary.org/b/id/8225703-L.jpg'
            ],
            [
                'title' => 'Notre-Dame de Paris',
                'author' => $authors['Victor Hugo'],
                'editor' => $editors['Le Livre de Poche'],
                'pages' => 752,
                'description' => 'L\'histoire de Quasimodo, le sonneur de cloches de Notre-Dame, et de son amour pour la belle Esmeralda. Un roman gothique sur la beauté, la laideur et l\'amour.',
                'image' => 'https://covers.openlibrary.org/b/id/8225804-L.jpg'
            ],
            [
                'title' => 'Le Tour du monde en quatre-vingts jours',
                'author' => $authors['Jules Verne'],
                'editor' => $editors['Hachette'],
                'pages' => 320,
                'description' => 'Les aventures de Phileas Fogg qui parie qu\'il peut faire le tour du monde en 80 jours. Un roman d\'aventures palpitant qui a fait rêver des générations de lecteurs.',
                'image' => 'https://covers.openlibrary.org/b/id/8225905-L.jpg'
            ],
            [
                'title' => 'La Peste',
                'author' => $authors['Albert Camus'],
                'editor' => $editors['Gallimard'],
                'pages' => 352,
                'description' => 'L\'histoire d\'une épidémie de peste dans la ville d\'Oran. Une allégorie de la condition humaine face à l\'absurde et à la solidarité.',
                'image' => 'https://covers.openlibrary.org/b/id/8226006-L.jpg'
            ],
            [
                'title' => 'Les Trois Mousquetaires',
                'author' => $authors['Alexandre Dumas'],
                'editor' => $editors['Le Livre de Poche'],
                'pages' => 704,
                'description' => 'Les aventures de d\'Artagnan et des trois mousquetaires Athos, Porthos et Aramis. Un roman de cape et d\'épée plein d\'action et d\'amitié.',
                'image' => 'https://covers.openlibrary.org/b/id/8226107-L.jpg'
            ],
            [
                'title' => 'Le Père Goriot',
                'author' => $authors['Honoré de Balzac'],
                'editor' => $editors['Folio'],
                'pages' => 368,
                'description' => 'L\'histoire tragique du père Goriot qui se sacrifie pour ses filles ingrates. Un roman réaliste sur l\'ambition, l\'argent et l\'amour paternel.',
                'image' => 'https://covers.openlibrary.org/b/id/8226208-L.jpg'
            ],
            [
                'title' => 'La Mare au Diable',
                'author' => $authors['George Sand'],
                'editor' => $editors['Pocket'],
                'pages' => 192,
                'description' => 'Un roman champêtre qui raconte l\'histoire d\'amour entre Germain, un laboureur veuf, et Marie, une jeune bergère. Une célébration de la vie rurale et des valeurs simples.',
                'image' => 'https://covers.openlibrary.org/b/id/8226309-L.jpg'
            ],
            [
                'title' => 'Voyage au centre de la Terre',
                'author' => $authors['Jules Verne'],
                'editor' => $editors['Hachette'],
                'pages' => 384,
                'description' => 'L\'expédition du professeur Lidenbrock et de son neveu Axel vers le centre de la Terre. Un roman d\'aventures extraordinaires mêlant science et imagination.',
                'image' => 'https://covers.openlibrary.org/b/id/8226410-L.jpg'
            ],
            [
                'title' => 'L\'Assommoir',
                'author' => $authors['Émile Zola'],
                'editor' => $editors['Folio'],
                'pages' => 512,
                'description' => 'L\'histoire de Gervaise Macquart, blanchisseuse parisienne, victime de l\'alcoolisme et de la misère. Un tableau saisissant de la vie ouvrière au XIXe siècle.',
                'image' => 'https://covers.openlibrary.org/b/id/8226511-L.jpg'
            ],
            [
                'title' => 'Le Rouge et le Noir',
                'author' => $authors['Stendhal'],
                'editor' => $editors['Folio'],
                'pages' => 576,
                'description' => 'L\'ascension sociale de Julien Sorel, jeune homme ambitieux, entre le rouge de l\'armée et le noir de l\'église. Un roman d\'analyse psychologique sur l\'ambition et l\'amour.',
                'image' => 'https://covers.openlibrary.org/b/id/8226612-L.jpg'
            ],
            [
                'title' => 'Du côté de chez Swann',
                'author' => $authors['Marcel Proust'],
                'editor' => $editors['Gallimard'],
                'pages' => 464,
                'description' => 'Premier tome de À la recherche du temps perdu. Une exploration magistrale de la mémoire et du temps à travers les souvenirs d\'enfance du narrateur.',
                'image' => 'https://covers.openlibrary.org/b/id/8226713-L.jpg'
            ],
            [
                'title' => 'Candide',
                'author' => $authors['Voltaire'],
                'editor' => $editors['Pocket'],
                'pages' => 160,
                'description' => 'Les aventures de Candide, jeune homme naïf qui découvre les horreurs du monde. Un conte philosophique satirique sur l\'optimisme et la tolérance.',
                'image' => 'https://covers.openlibrary.org/b/id/8226814-L.jpg'
            ],
            [
                'title' => 'Le Tartuffe',
                'author' => $authors['Molière'],
                'editor' => $editors['Gallimard'],
                'pages' => 192,
                'description' => 'La comédie d\'un faux dévot qui abuse de la confiance d\'Orgon. Une satire brillante de l\'hypocrisie religieuse.',
                'image' => 'https://covers.openlibrary.org/b/id/8226915-L.jpg'
            ],
            [
                'title' => 'La Nausée',
                'author' => $authors['Jean-Paul Sartre'],
                'editor' => $editors['Gallimard'],
                'pages' => 256,
                'description' => 'Le journal d\'Antoine Roquentin qui découvre l\'absurdité de l\'existence. Un roman existentialiste majeur du XXe siècle.',
                'image' => 'https://covers.openlibrary.org/b/id/8227016-L.jpg'
            ],
            [
                'title' => 'Le Deuxième Sexe',
                'author' => $authors['Simone de Beauvoir'],
                'editor' => $editors['Gallimard'],
                'pages' => 976,
                'description' => 'Un essai fondateur du féminisme moderne qui analyse la condition féminine. "On ne naît pas femme, on le devient."',
                'image' => 'https://covers.openlibrary.org/b/id/8227117-L.jpg'
            ],
            [
                'title' => 'Les Faux-Monnayeurs',
                'author' => $authors['André Gide'],
                'editor' => $editors['Gallimard'],
                'pages' => 448,
                'description' => 'Un roman dans le roman qui explore les thèmes de l\'authenticité et du mensonge. Une œuvre moderniste novatrice.',
                'image' => 'https://covers.openlibrary.org/b/id/8227218-L.jpg'
            ],
            [
                'title' => 'Bel-Ami',
                'author' => $authors['Guy de Maupassant'],
                'editor' => $editors['Folio'],
                'pages' => 416,
                'description' => 'L\'ascension sociale de Georges Duroy grâce à la séduction des femmes. Un roman réaliste sur l\'arrivisme et la corruption.',
                'image' => 'https://covers.openlibrary.org/b/id/8227319-L.jpg'
            ],
            [
                'title' => 'Gargantua',
                'author' => $authors['François Rabelais'],
                'editor' => $editors['Pocket'],
                'pages' => 352,
                'description' => 'Les aventures du géant Gargantua et de son fils Pantagruel. Une satire joyeuse et érudite de la société du XVIe siècle.',
                'image' => 'https://covers.openlibrary.org/b/id/8227420-L.jpg'
            ],
            [
                'title' => 'Fables',
                'author' => $authors['Jean de La Fontaine'],
                'editor' => $editors['Hachette'],
                'pages' => 384,
                'description' => 'Recueil de fables mettant en scène des animaux pour délivrer des leçons de morale. Un classique de la littérature française.',
                'image' => 'https://covers.openlibrary.org/b/id/8227521-L.jpg'
            ],
            [
                'title' => 'Nana',
                'author' => $authors['Émile Zola'],
                'editor' => $editors['Folio'],
                'pages' => 528,
                'description' => 'L\'histoire de Nana, courtisane qui règne sur le Paris du Second Empire. Un roman naturaliste sur le pouvoir de la séduction.',
                'image' => 'https://covers.openlibrary.org/b/id/8227622-L.jpg'
            ],
            [
                'title' => 'La Chartreuse de Parme',
                'author' => $authors['Stendhal'],
                'editor' => $editors['Folio'],
                'pages' => 608,
                'description' => 'Les aventures amoureuses et politiques de Fabrice del Dongo en Italie. Un roman romantique sur la passion et l\'intrigue.',
                'image' => 'https://covers.openlibrary.org/b/id/8227723-L.jpg'
            ],
            [
                'title' => 'L\'Éducation sentimentale',
                'author' => $authors['Gustave Flaubert'],
                'editor' => $editors['Folio'],
                'pages' => 592,
                'description' => 'L\'histoire de Frédéric Moreau et de son amour impossible pour Mme Arnoux. Un roman d\'apprentissage sur les illusions de la jeunesse.',
                'image' => 'https://covers.openlibrary.org/b/id/8227824-L.jpg'
            ],
            [
                'title' => 'La Petite Fadette',
                'author' => $authors['George Sand'],
                'editor' => $editors['Pocket'],
                'pages' => 224,
                'description' => 'L\'histoire d\'amour entre Landry et Fadette, une jeune fille rejetée. Un roman champêtre sur la différence et l\'acceptation.',
                'image' => 'https://covers.openlibrary.org/b/id/8227925-L.jpg'
            ],
            [
                'title' => 'Le Château de ma mère',
                'author' => $authors['Marcel Pagnol'],
                'editor' => $editors['Pocket'],
                'pages' => 288,
                'description' => 'Suite de La Gloire de mon père. Les souvenirs d\'enfance de Marcel Pagnol, entre tendresse familiale et découverte de la nature provençale.',
                'image' => 'https://covers.openlibrary.org/b/id/8228026-L.jpg'
            ],
            [
                'title' => 'Boule de Suif',
                'author' => $authors['Guy de Maupassant'],
                'editor' => $editors['Pocket'],
                'pages' => 128,
                'description' => 'Une prostituée se sacrifie pour des bourgeois qui la méprisent. Une nouvelle qui dénonce l\'hypocrisie de la société.',
                'image' => 'https://covers.openlibrary.org/b/id/8228127-L.jpg'
            ],
            [
                'title' => 'L\'Île mystérieuse',
                'author' => $authors['Jules Verne'],
                'editor' => $editors['Hachette'],
                'pages' => 544,
                'description' => 'Des naufragés reconstruisent une civilisation sur une île déserte. Un roman d\'aventures sur l\'ingéniosité humaine et la survie.',
                'image' => 'https://covers.openlibrary.org/b/id/8228228-L.jpg'
            ],
            [
                'title' => 'Les Fleurs du mal',
                'author' => $authors['Charles Baudelaire'],
                'editor' => $editors['Gallimard'],
                'pages' => 320,
                'description' => 'Recueil de poèmes explorant la beauté, le mal et la modernité. Une œuvre fondatrice de la poésie moderne.',
                'image' => 'https://covers.openlibrary.org/b/id/8228329-L.jpg'
            ],
            [
                'title' => 'Le Malade imaginaire',
                'author' => $authors['Molière'],
                'editor' => $editors['Gallimard'],
                'pages' => 160,
                'description' => 'Argan, hypocondriaque tyrannique, veut marier sa fille à un médecin. Une comédie satirique sur la médecine et l\'autoritarisme.',
                'image' => 'https://covers.openlibrary.org/b/id/8228430-L.jpg'
            ],
            [
                'title' => 'Contes de la bécasse',
                'author' => $authors['Guy de Maupassant'],
                'editor' => $editors['Pocket'],
                'pages' => 256,
                'description' => 'Recueil de nouvelles sur la vie normande et parisienne. Des histoires pleines d\'ironie et d\'observation sociale.',
                'image' => 'https://covers.openlibrary.org/b/id/8228531-L.jpg'
            ],
            [
                'title' => 'Eugénie Grandet',
                'author' => $authors['Honoré de Balzac'],
                'editor' => $editors['Folio'],
                'pages' => 288,
                'description' => 'L\'histoire d\'Eugénie, fille d\'un avare, qui sacrifie son bonheur pour l\'amour. Un roman réaliste sur l\'avarice et le sacrifice.',
                'image' => 'https://covers.openlibrary.org/b/id/8228632-L.jpg'
            ],
            [
                'title' => 'Le Horla',
                'author' => $authors['Guy de Maupassant'],
                'editor' => $editors['Pocket'],
                'pages' => 96,
                'description' => 'Un homme est hanté par une présence invisible et terrifiante. Une nouvelle fantastique sur la folie et l\'angoisse.',
                'image' => 'https://covers.openlibrary.org/b/id/8228733-L.jpg'
            ],
            [
                'title' => 'Thérèse Raquin',
                'author' => $authors['Émile Zola'],
                'editor' => $editors['Folio'],
                'pages' => 320,
                'description' => 'Thérèse et son amant tuent le mari de celle-ci et sont rongés par la culpabilité. Un roman noir sur le crime et le remords.',
                'image' => 'https://covers.openlibrary.org/b/id/8228834-L.jpg'
            ],
            [
                'title' => 'Zadig',
                'author' => $authors['Voltaire'],
                'editor' => $editors['Pocket'],
                'pages' => 144,
                'description' => 'Les aventures de Zadig, jeune Babylonien victime du destin. Un conte philosophique sur la providence et la liberté.',
                'image' => 'https://covers.openlibrary.org/b/id/8228935-L.jpg'
            ],
            [
                'title' => 'Indiana',
                'author' => $authors['George Sand'],
                'editor' => $editors['Pocket'],
                'pages' => 384,
                'description' => 'Une femme mariée cherche à s\'émanciper de son mari tyrannique. Un roman féministe avant l\'heure sur l\'indépendance féminine.',
                'image' => 'https://covers.openlibrary.org/b/id/8229036-L.jpg'
            ],
            [
                'title' => 'Les Contemplations',
                'author' => $authors['Victor Hugo'],
                'editor' => $editors['Gallimard'],
                'pages' => 512,
                'description' => 'Recueil de poèmes intimistes sur l\'amour, la mort et la foi. "Les Mémoires d\'une âme" selon Hugo.',
                'image' => 'https://covers.openlibrary.org/b/id/8229137-L.jpg'
            ],
            [
                'title' => 'Cinq semaines en ballon',
                'author' => $authors['Jules Verne'],
                'editor' => $editors['Hachette'],
                'pages' => 352,
                'description' => 'Trois explorateurs traversent l\'Afrique en ballon. Le premier roman de Jules Verne, rempli d\'aventures et de découvertes.',
                'image' => 'https://covers.openlibrary.org/b/id/8229238-L.jpg'
            ],
            [
                'title' => 'Le Temps retrouvé',
                'author' => $authors['Marcel Proust'],
                'editor' => $editors['Gallimard'],
                'pages' => 528,
                'description' => 'Dernier tome de À la recherche du temps perdu. Le narrateur découvre sa vocation d\'écrivain et la vraie nature du temps.',
                'image' => 'https://covers.openlibrary.org/b/id/8229339-L.jpg'
            ],
        ];

        foreach ($books as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title'])
                ->setAuthor($bookData['author'])
                ->setEditor($bookData['editor'])
                ->setPages($bookData['pages'])
                ->setDescription($bookData['description'])
                ->setImage($bookData['image']);

            // Associer l'utilisateur au livre (relation ManyToMany)
            $book->addUser($user);

            $manager->persist($book);
        }

        $manager->flush();
    }

    private function createAuthor(ObjectManager $manager, string $firstName, string $lastName): Author
    {
        $author = new Author();
        $author->setFirstName($firstName);
        $author->setLastName($lastName);
        $manager->persist($author);
        return $author;
    }

    private function createEditor(ObjectManager $manager, string $name): Editor
    {
        $editor = new Editor();
        $editor->setName($name);
        $manager->persist($editor);
        return $editor;
    }

    public function getDependencies(): array
    {
        return [
            AppFixtures::class,
        ];
    }
}
