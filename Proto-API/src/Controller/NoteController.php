<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class NoteController extends AbstractController
{
    #[Route('/note', name: 'app_note')]
    public function getAllNote(NoteRepository $noteRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $noteList = $noteRepository->findAll();
            $jsonNoteList = $serializer->serialize($noteList, 'json');
            
            return new JsonResponse($jsonNoteList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la récupération des notes.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/note/{id}', name: 'detailNote', methods: ['GET'])]
    public function getDetailGroup(Note $note, SerializerInterface $serializer)
    {
        try {
            $jsonNote = $serializer->serialize($note, 'json');
            return new JsonResponse($jsonNote, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la récupération des détails de la note.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/note/{id}', name: 'deleteNote', methods: ['DELETE'])]
    public function deleteNote(Note $note, EntityManagerInterface $em): JsonResponse 
    {
        if (!$note) {
            return new JsonResponse(['message' => ' Note non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $em->remove($note);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression de la note.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
