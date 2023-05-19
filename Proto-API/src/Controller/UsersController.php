<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function getAllUsers(UsersRepository $usersRepository, SerializerInterface $serializer): JsonResponse
    {
        $usersList = $usersRepository->findAll();
        $jsonUsersList = $serializer->serialize($usersList, 'json');
        
        return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);
    }

    #[Route('/users/{id}', name: 'detail_user', methods: ['GET'])]
    public function getDetailUser(Users $user, SerializerInterface $serializer)
    {
        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}
