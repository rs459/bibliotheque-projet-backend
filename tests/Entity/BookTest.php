<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Editor;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testBookCreation(): void
    {
        $book = new Book();
        $book->setTitle('Test Book');
        $book->setDescription('A test book description');
        $book->setPages(350);

        $this->assertSame('Test Book', $book->getTitle());
        $this->assertSame('A test book description', $book->getDescription());
        $this->assertSame(350, $book->getPages());
    }

    public function testBookAuthorRelation(): void
    {
        $book = new Book();
        $author = new Author();
        $author->setFirstName('Victor');
        $author->setLastName('Hugo');

        $book->setAuthor($author);

        $this->assertSame($author, $book->getAuthor());
        $this->assertSame('Hugo', $book->getAuthor()->getLastName());
    }

    public function testBookEditorRelation(): void
    {
        $book = new Book();
        $editor = new Editor();
        $editor->setName('Gallimard');

        $book->setEditor($editor);

        $this->assertSame($editor, $book->getEditor());
        $this->assertSame('Gallimard', $book->getEditor()->getName());
    }

    public function testBookUserRelation(): void
    {
        $book = new Book();
        $user = new User();
        $user->setEmail('reader@test.com');

        $book->setUser($user);

        $this->assertSame($user, $book->getUser());
        $this->assertSame('reader@test.com', $book->getUser()->getEmail());
    }

    public function testBookImage(): void
    {
        $book = new Book();
        $imageUrl = 'https://books.google.com/books/content?id=abc&printsec=frontcover&img=1&zoom=1';

        $book->setImage($imageUrl);

        $this->assertSame($imageUrl, $book->getImage());
    }
}
