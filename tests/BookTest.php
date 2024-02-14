<?php

namespace App\Tests;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class BookTest extends KernelTestCase
{
    use ResetDatabase;

    private EntityManagerInterface $entityManager;

    protected function setUp() : void
    {

        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

    }

    /** @test */
    public function givenBook_whenFlush_thenShouldBeSaved()
    {
        // given
        $bookRepository = $this->entityManager->getRepository(Book::class);

        $book = new Book();
        $book->setTitle('Title1');
        $book->setAuthor('Author1');
        $book->setReleaseYear(2020);
        $book->setRating(5.0);
        $book->setQuantity(10);

        $this->entityManager->persist($book);
        // when
        $this->entityManager->flush();
        // then
        $expected = $bookRepository->findOneBy(['title' => 'Title1']);

        $this->assertEquals('Title1', $expected->getTitle());
        $this->assertEquals('Author1', $expected->getAuthor());
        $this->assertEquals(2020, $expected->getReleaseYear());
        $this->assertEquals(5.0, $expected->getRating());
        $this->assertEquals(10, $expected->getQuantity());
        
    }
}
