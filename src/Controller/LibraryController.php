<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use App\Form\BookFormType;
use App\Form\RentFormType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LibraryController extends AbstractController
{

    private EntityManagerInterface $em;
    private BookRepository $bookRepository;

    public function __construct(EntityManagerInterface $em, BookRepository $bookRepository)
    {
        $this->em = $em;
        $this->bookRepository = $bookRepository;
    }

    #[Route('/', name: 'app_library', methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'books' => $this->bookRepository->findAll()
        ]);
    }

    #[Route('/add', name: 'create_book')]
    public function createBook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookFormType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newBook = $form->getData();
            $this->em->persist($newBook);
            $this->em->flush();

            return $this->redirectToRoute('app_library');
        }

        return $this->render('add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/rent/{id}', name: 'rent_book')]
    public function rentBook(Book $book, #[CurrentUser] User $user = null, Request $request): Response
    {
        if (!$book || !$user) {
            return $this->redirectToRoute('app_library');
        }

        $form = $this->createForm(RentFormType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $book->setQuantity($book->getQuantity() - 1);
            $user->addBook($book);
            $this->em->flush();

            return $this->redirectToRoute('app_library');
        }

        return $this->render('rent.html.twig', [
            'form' => $form->createView(),
            'book' => $book
        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('profile.html.twig');
    }

    #[Route('/return/{id}', name: 'return_book')]
    public function returnBook(Book $book, #[CurrentUser] User $user = null): Response
    {
        if (!$book || !$user) {
            return $this->redirectToRoute('app_library');
        }
        $book->setQuantity($book->getQuantity() + 1);
        $user->removeBook($book);
        $this->em->flush();
        return $this->redirectToRoute('app_library');
    }
}
