<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LibraryController extends AbstractController
{

    private EntityManagerInterface $em;
    private BookRepository $bookRepository;

    public function __construct(EntityManagerInterface $em, BookRepository $repo) {
        $this->em = $em;
        $this->bookRepository = $repo;
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
}
