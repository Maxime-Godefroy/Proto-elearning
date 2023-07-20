<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function getAllMessage(MessageRepository $messageRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $messageList = $messageRepository->findAll();
            $jsonMessageList = $serializer->serialize($messageList, 'json');
            
            return new JsonResponse($jsonMessageList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue lors de la récupération des messages.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/message/{id}', name: 'detailMessage', methods: ['GET'])]
    public function getDetailMessage(Message $message, SerializerInterface $serializer)
    {
        try {
            $jsonMessage = $serializer->serialize($message, 'json');
            return new JsonResponse($jsonMessage, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue lors de la récupération du message.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    #[Route('/message/{id}', name: 'deleteMessage', methods: ['DELETE'])]
    public function deleteMessage(Message $message, EntityManagerInterface $em): JsonResponse 
    {
        if (!$message) {
            return new JsonResponse(['message' => ' Message non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $em->remove($message);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression du message.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
