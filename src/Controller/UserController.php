<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;


class UserController extends AbstractController
{
    public $res = [];

    #[Route('/users/', methods: ['GET'])]
    public function showAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            $this->res[$user->getId()] = $user->getRoles();
        }

        return new JsonResponse($this->res);
    }

    // #[Route('/task/{taskID}', methods: ['GET'])]
    // public function show(TaskRepository $taskRepository, int $taskID): JsonResponse
    // {
    //     $task = $taskRepository->find($taskID);
    //     $this->res['task_name'] = $task->getName();

    //     return new JsonResponse($this->res);
    // }

    #[Route('/user/register/', methods: ['POST'])]
    public function register(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $email = $request->query->get('email');
        // $name = $request->query->get('name');
        $password = $request->query->get('password');
        $repeatedPassword = $request->query->get('repeat_password');

        // $this->res['email'] = $email;
        // $this->res['name'] = $name;
        // $this->res['password'] = $password;
        // $this->res['repeatedPassword'] = $repeatedPassword;


        if ($email && $password && $repeatedPassword) {

            /* To add function to check if is password strong */
            if ($password == $repeatedPassword) {
                /* check if user exists */
                if (!$userRepository->findOneBy(array('email' => $email))) {
                    $user = new User();
                    $user->setEmail($email);
                    // $user->setName($name);
                    $user->setPassword($password);
                    $user->setRoles(['ROLE_USER']);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->res['info'] = 'Registered new user with id: ' . $user->getId();
                }
            }
            
        } else {
            $this->res['error'] = 'too few data';
        }

        return new JsonResponse($this->res);
    }

    // #[Route('/task/{taskID}', methods: ['PATCH'])]
    // public function edit(EntityManagerInterface $entityManager, Request $request, TaskRepository $taskRepository, int $taskID): Response
    // {
    //     $newName = $request->query->get('new_name');

    //     if ($taskID && $newName) {
    //         $task = $taskRepository->find($taskID);

    //         $task->setName($newName);
    //         $entityManager->persist($task);
    //         $entityManager->flush();

    //         $this->res['info'] = "Task with id {$taskID} has new name: {$newName}";

    //     } else {
    //         $this->res['error'] = 'too few data';
    //     }

    //     return new JsonResponse($this->res);
    // }

    // #[Route('/task/{taskID}', methods: ['DELETE'])]
    // public function delete(EntityManagerInterface $entityManager, TaskRepository $taskRepository, int $taskID): Response
    // {
    //     if ($taskID) {
    //         $task = $taskRepository->find($taskID);

    //         $entityManager->remove($task);
    //         $entityManager->flush();

    //         $this->res['info'] = "task with id {$taskID} deleted!";
        
    //     } else {
    //         $this->res['error'] = 'too few data';
    //     }

    //     return new JsonResponse($this->res);
    // }
}