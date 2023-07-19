<?php

namespace App\Controller;

use App\Entity\Note;
use App\Repository\NoteRepository;
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
        $noteList = $noteRepository->findAll();
        $jsonNoteList = $serializer->serialize($noteList, 'json');
        
        return new JsonResponse($jsonNoteList, Response::HTTP_OK, [], true);
    }

    #[Route('/note/{id}', name: 'detail_note', methods: ['GET'])]
    public function getDetailGroup(Note $note, SerializerInterface $serializer)
    {
        $jsonNote = $serializer->serialize($note, 'json');
        return new JsonResponse($jsonNote, Response::HTTP_OK, [], true);
    }
}
