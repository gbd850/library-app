<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LibraryController extends AbstractController
{

    private EntityManagerInterface $em;
    private BookRepository $bookRepository;

    public function __construct(EntityManagerInterface $em, BookRepository $repo) {
        $this->em = $em;
        $this->bookRepository = $repo;
    }

    #[Route('/', name: 'app_library')]
    public function index(): JsonResponse
    {
        return $this->json($this->bookRepository->findAll());
    }
}
