<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{

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
    public function givenUser_whenFlush_thenShouldBeSaved(): void
    {
         // given
         $userRepository = $this->entityManager->getRepository(User::class);

         $user = new User();
         $user->setEmail('test@t.com');
         $user->setPassword('123456');
 
         $this->entityManager->persist($user);
         // when
         $this->entityManager->flush();
         // then
         $expected = $userRepository->findOneBy(['email' => 'test@t.com']);
 
         $this->assertEquals('test@t.com', $expected->getEmail());
         $this->assertEquals('123456', $expected->getPassword());
    }
}
