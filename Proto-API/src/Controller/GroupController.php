<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    #[Route('/groupes', name: 'app_groupes')]
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

    #[Route('/groupes/{id}', name: 'detailGroup', methods: ['GET'])]
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

    #[Route('/groupes/{id}', name: 'deleteGroup', methods: ['DELETE'])]
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
}
