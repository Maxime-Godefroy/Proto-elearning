<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Course;
use App\Entity\Users;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class NoteController extends AbstractController
{
    #[Route('/api/note', name: 'app_note', methods: ['GET'])]
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

    #[Route('/api/note/{id}', name: 'detailNote', methods: ['GET'])]
    public function getDetailGroup(Note $note, SerializerInterface $serializer)
    {
        try {
            $jsonNote = $serializer->serialize($note, 'json');
            return new JsonResponse($jsonNote, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la récupération des détails de la note.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/note/{id}', name: 'deleteNote', methods: ['DELETE'])]
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

    #[Route('/api/note', name: 'app_note', methods: ['POST'])]
    public function createNote(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['value']) || !isset($data['validate']) || !isset($data['nb_tentative']) || !isset($data['user_id']) || !isset($data['course_id']) || !isset($data['given_at'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $note = new Note();
        $note->setValue($data['value']);
        $note->setValidate($data['validate']);
        $note->setNbTentative($data['nb_tentative']);

        $user = $entityManager->getReference(Users::class, $data['user_id']);        
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $course = $entityManager->getReference(Course::class, $data['course_id']);        
        if (!$course) {
            return new JsonResponse(['message' => 'Cours introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $note->setUserId($user);
        $note->setCourseId($course);

        $note->setGivenAt(new \DateTime($data['given_at']));

        try {
            $entityManager->persist($note);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la création de la note.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'La note a été créée avec succès.'], Response::HTTP_CREATED);
    }

    #[Route('/api/note/{id}', name: 'updateNote', methods: ['PUT'])]
    public function updateNote(Note $note, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$note) {
            return new JsonResponse(['message' => 'Note non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['value']) || !isset($data['validate']) || !isset($data['nb_tentative']) || !isset($data['user_id']) || !isset($data['course_id'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $note->setValue($data['value']);
        $note->setValidate($data['validate']);
        $note->setNbTentative($data['nb_tentative']);

        $user = $entityManager->getReference(Users::class, $data['user_id']);        
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $course = $entityManager->getReference(Course::class, $data['course_id']);        
        if (!$course) {
            return new JsonResponse(['message' => 'Cours introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $note->setUserId($user);
        $note->setCourseId($course);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la mise à jour de la note.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'La note a été mise à jour avec succès.'], Response::HTTP_OK);
    }
}
