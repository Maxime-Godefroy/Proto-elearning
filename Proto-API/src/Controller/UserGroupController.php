<?php

namespace App\Controller;

use App\Entity\UserGroup;
use App\Entity\Users;
use App\Entity\Group;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserGroupController extends AbstractController
{
    #[Route('/api/user_group', name: 'app_user_group', methods: ['GET'])]
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

    #[Route('/api/user_group/{id}', name: 'detailUserGroup', methods: ['GET'])]
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

    #[Route('/api/user_group/{id}', name: 'deleteUserGroup', methods: ['DELETE'])]
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

    #[Route('/api/user_group', name: 'createUserGroup', methods: ['POST'])]
    public function createUserGroup(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['id_user']) || !isset($data['id_group'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getReference(Users::class, $data['id_user']);
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $group = $entityManager->getReference(Group::class, $data['id_group']);
        if (!$group) {
            return new JsonResponse(['message' => 'Groupe introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $userGroup = new UserGroup();
        $userGroup->setIdUser($user);
        $userGroup->setIdGroup($group);

        try {
            $entityManager->persist($userGroup);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de l\'ajout de l\'utilisateur au groupe.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'L\'utilisateur a été ajouté au groupe avec succès.'], Response::HTTP_CREATED);
    }

    #[Route('/api/user_group/{id}', name: 'updateUserGroup', methods: ['PUT'])]
    public function updateUserGroup(UserGroup $userGroup, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$userGroup) {
            return new JsonResponse(['message' => 'Relation utilisateur-groupe non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['id_user']) || !isset($data['id_group'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $entityManager->getReference(Users::class, $data['id_user']);
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $group = $entityManager->getReference(Group::class, $data['id_group']);
        if (!$group) {
            return new JsonResponse(['message' => 'Groupe introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $userGroup->setIdUser($user);
        $userGroup->setIdGroup($group);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la mise à jour de la relation utilisateur-groupe.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'La relation utilisateur-groupe a été mise à jour avec succès.'], Response::HTTP_OK);
    }
}
