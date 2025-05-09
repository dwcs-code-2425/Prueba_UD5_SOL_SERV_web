<?php

namespace App\Controller;

use App\Entity\Todo;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\OptionsResolver\TodoOptionsResolver;
use App\Repository\TodoRepository;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api", "api_", format: "json")]
final class TodoController extends AbstractController
{

    //https://symfony.com/doc/current/serializer.html
    // #[Route('/todos', name: 'todos', methods: ["GET"])]
    // public function index(TodoRepository $todoRepository): JsonResponse
    // {
    //     //$todos = $todoRepository->findAll();
    //     $todos = $todoRepository->findAllWithPagination(1);

    //     return $this->json($todos);
    // }

    #[Route('/todos', name: 'get_todos', methods: ["GET"])]
    public function getTodos(TodoRepository $todoRepository, Request $request, PaginatorOptionsResolver $paginatorOptionsResolver): JsonResponse
    {
        try {
            $queryParams = $paginatorOptionsResolver
                ->configurePage()
                //query->all() devuelve un array con los parametros de la URL del querystring los que siguen a la interrogación: 
                //https://localhost:8000/api/todos?page=1
                ->resolve($request->query->all());

            $todos = $todoRepository->findAllWithPagination($queryParams["page"]);

            return $this->json($todos);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }


    #[Route("/todos/{id}", "get_todo", methods: ["GET"])]
    public function getTodo(Todo $todo): JsonResponse
    {
        return $this->json($todo);
    }


    #[Route("/todos", "create_todo", methods: ["POST"])]
    public function createTodo(
        Request $request,
        TodoRepository $todoRepository,
        ValidatorInterface $validator,
        TodoOptionsResolver $todoOptionsResolver
    ): JsonResponse {
        try {
            $requestBody = json_decode($request->getContent(), true);
            $fields = $todoOptionsResolver->configureTitle(true)->resolve($requestBody);

            $todo = new Todo();
            $todo->setTitle($fields["title"]);


            // To validate the entity
            $errors = $validator->validate($todo);
            if (count($errors) > 0) {
                throw new InvalidArgumentException((string) $errors);
            }

            $todoRepository->save($todo, true);
            return $this->json($todo, status: Response::HTTP_CREATED);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route("/todos/{id}", "delete_todo", methods: ["DELETE"])]
    public function deleteTodo(Todo $todo, TodoRepository $todoRepository)
    {
        $todoRepository->remove($todo, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }


    #[Route("/todos/{id}", "update_todo", methods: ["PATCH", "PUT"])]
    public function updateTodo(Todo $todo, Request $request, TodoOptionsResolver $todoOptionsResolver, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        try {
            // $isPatchMethod = $request->getMethod() === "PUT";
            $isPutMethod = $request->getMethod() === "PUT";
            $requestBody = json_decode($request->getContent(), true);

            $fields = $todoOptionsResolver
                ->configureTitle($isPutMethod)
                ->configureCompleted($isPutMethod)
                ->resolve($requestBody);

            foreach ($fields as $field => $value) {
                switch ($field) {
                    case "title":
                        $todo->setTitle($value);
                        break;
                    case "completed":
                        $todo->setCompleted($value);
                        break;
                }
            }

            $errors = $validator->validate($todo);
            if (count($errors) > 0) {
                throw new InvalidArgumentException((string) $errors);
            }

            $em->flush();

            return $this->json($todo);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }


}
