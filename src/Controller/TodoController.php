<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
#[Route("/api", "api_")]
final class TodoController extends AbstractController
{

    //https://symfony.com/doc/current/serializer.html
    #[Route('/todos', name: 'todos', methods: ["GET"])]
    public function index(TodoRepository $todoRepository): JsonResponse
    {
        $todos = $todoRepository->findAll();

        return $this->json($todos);
    }
}
