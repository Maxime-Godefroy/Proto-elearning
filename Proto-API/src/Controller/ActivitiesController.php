<?php

namespace App\Controller;

use App\Entity\Activities;
use App\Repository\ActivitiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ActivitiesController extends AbstractController
{
    #[Route('/activities', name: 'app_activities')]
    public function getAllUsers(ActivitiesRepository $usersRepository, SerializerInterface $serializer): JsonResponse
    {
        $usersList = $usersRepository->findAll();
        $jsonUsersList = $serializer->serialize($usersList, 'json');
        
        return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);
    }

    #[Route('/activities/{id}', name: 'detail_activity', methods: ['GET'])]
    public function getDetailUser(Activities $user, SerializerInterface $serializer)
    {
        $jsonUser = $serializer->serialize($user, 'json');
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }
}
