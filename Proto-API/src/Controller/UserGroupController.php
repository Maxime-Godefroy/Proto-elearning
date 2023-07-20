<?php

namespace App\Controller;

use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserGroupController extends AbstractController
{
    #[Route('/user_group', name: 'app_user_group')]
    public function getAllUserGroup(UserGroupRepository $user_groupRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $user_groupList = $user_groupRepository->findAll();
            $jsonUserGroupList = $serializer->serialize($user_groupList, 'json');

            return new JsonResponse($jsonUserGroupList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Une erreur est survenue lors de la récupération des groupes d\'utilisateurs.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/user_group/{id}', name: 'detailUserGroup', methods: ['GET'])]
    public function getDetailUserGroup(UserGroup $user_group, SerializerInterface $serializer)
    {
        try {
            $jsonUserGroup = $serializer->serialize($user_group, 'json');
            return new JsonResponse($jsonUserGroup, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'Une erreur est survenue lors de la récupération des détails du groupe d\'utilisateurs.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/user_group/{id}', name: 'deleteUserGroup', methods: ['DELETE'])]
    public function deleteUserGroup(UserGroup $user_group, EntityManagerInterface $em): JsonResponse 
    {
        if (!$user_group) {
            return new JsonResponse(['message' => ' Utilisateur non trouvé dans aucun groupe.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $em->remove($user_group);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression de l\'utilisateur d\'un groupe.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
