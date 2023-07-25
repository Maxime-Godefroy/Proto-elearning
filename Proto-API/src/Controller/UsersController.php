<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/api/users', name: 'app_users', methods: ['GET'])]
    public function getAllUsers(UsersRepository $usersRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $usersList = $usersRepository->findAll();
            $jsonUsersList = $serializer->serialize($usersList, 'json');

            return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['message' => 'Une erreur est survenue lors de la récupération des utilisateurs.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailUser(Users $user = null, SerializerInterface $serializer)
    {
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur introuvable'], Response::HTTP_NOT_FOUND);
        }

        try {
            $jsonUser = $serializer->serialize($user, 'json');
            return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue lors de la sérialisation'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(Users $user, EntityManagerInterface $em): JsonResponse 
    {
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $em->remove($user);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression de l\'utilisateur.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    
    
    #[Route('/api/users', name: 'createUser', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jsonData = $request->getContent();

        $data = json_decode($jsonData, true);

        if (!isset($data['username']) || !isset($data['firstname']) || !isset($data['lastname']) || !isset($data['email']) || !isset($data['color']) || !isset($data['roles']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $user = new Users();
        $user->setUsername($data['username']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setColor($data['color']);
        $user->setRoles($data['roles']);
        $user->setPassword($data['password']);
        $user->setDateCreation(new \DateTime());

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la création de l\'utilisateur.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'L\'utilisateur a été créé avec succès.'], Response::HTTP_CREATED);
    }

    #[Route('/api/users/{id}', name: 'updateUser', methods: ['PUT'])]
    public function updateUser(Users $user, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonData = $request->getContent();

        $data = json_decode($jsonData, true);

        if (!isset($data['username']) || !isset($data['firstname']) || !isset($data['lastname']) || !isset($data['email']) || !isset($data['color']) || !isset($data['roles']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $user->setUsername($data['username']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setColor($data['color']);
        $user->setRoles($data['roles']);
        $user->setPassword($data['password']);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'L\'utilisateur a été mis à jour avec succès.'], Response::HTTP_OK);
    }

}