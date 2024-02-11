<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Form\RentFormType;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class LibraryController extends AbstractController
{

    private EntityManagerInterface $em;
    private BookRepository $bookRepository;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em, BookRepository $bookRepository, UserRepository $userRepository) {
        $this->em = $em;
        $this->bookRepository = $bookRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/', name: 'app_library', methods: ['GET', 'HEAD'])]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'books' => $this->bookRepository->findAll()
        ]);
    }

    #[Route('/add', name: 'create_book')]
    public function createBook(Request $request) : Response
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
    public function rentBook($id, Request $request) : Response
    {
        $book = $this->bookRepository->find($id);
        $user = $this->getUser();

        if (!$book || !$user) {
            return $this->redirectToRoute('app_library');
        }
        $user = $this->userRepository->find($user->getId());

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
    public function profile() : Response
    {
        return $this->render('profile.html.twig');
    }

    #[Route('/return/{id}', name: 'return_book')]
    public function returnBook($id) : Response
    {
        $book = $this->bookRepository->find($id);
        $user = $this->getUser();
        if (!$book || !$user) {
            return $this->redirectToRoute('app_library');
        }
        $user = $this->userRepository->find($user->getId());
        $book->setQuantity($book->getQuantity() + 1);
        $user->removeBook($book);
        $this->em->flush();
        return $this->redirectToRoute('app_library');
    }
}
