<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Users;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GroupController extends AbstractController
{
    #[Route('/api/groupes', name: 'app_groupes')]
    public function getAllGroupes(GroupRepository $groupRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $groupes = $groupRepository->findAll();
            $jsonGroupes = $serializer->serialize($groupes, 'json');
            return new JsonResponse($jsonGroupes, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la récupération des groupes.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/groupes/{id}', name: 'detailGroup', methods: ['GET'])]
    public function getDetailGroupe(Group $groupe = null, SerializerInterface $serializer)
    {
        if (!$groupe) {
            return new JsonResponse(['message' => 'Le groupe demandé est introuvable.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $jsonGroupe = $serializer->serialize($groupe, 'json');
            return new JsonResponse($jsonGroupe, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la récupération des détails du groupe.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/groupes/{id}', name: 'deleteGroup', methods: ['DELETE'])]
    public function deleteMessage(Group $groupes, EntityManagerInterface $em): JsonResponse 
    {
        if (!$groupes) {
            return new JsonResponse(['message' => ' Groupe non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $em->remove($groupes);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression du groupe.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/groupes/{id}', name: 'createGroup', methods: ['POST'])]
    public function createGroup(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['name']) || !isset($data['professeur_id']) || !isset($data['color'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $group = new Group();
        $group->setName($data['name']);
        $group->setColor($data['color']);
        
        $professeur = $entityManager->getReference(Users::class, $data['professeur_id']);        

        if (!$professeur) {
            return new JsonResponse(['message' => 'Professeur introuvable.'], Response::HTTP_NOT_FOUND);
        }
        $group->setProfesseurId($professeur);

        try {
            $entityManager->persist($group);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la création du groupe.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Le groupe a été créé avec succès.'], Response::HTTP_CREATED);
    }

    #[Route('/api/groupes/{id}', name: 'updateGroup', methods: ['PUT'])]
    public function updateGroup(Group $group, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$group) {
            return new JsonResponse(['message' => 'Groupe non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['name']) || !isset($data['professeur_id']) || !isset($data['color'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $group->setName($data['name']);
        $group->setColor($data['color']);

        $professeur = $entityManager->getReference(Users::class, $data['professeur_id']);        
        if (!$professeur) {
            return new JsonResponse(['message' => 'Professeur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $group->setProfesseurId($professeur);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la mise à jour du groupe.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Le groupe a été mis à jour avec succès.'], Response::HTTP_OK);
    }
}
