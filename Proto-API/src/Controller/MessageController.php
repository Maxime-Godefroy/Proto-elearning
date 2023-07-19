<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
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
        $messageList = $messageRepository->findAll();
        $jsonMessageList = $serializer->serialize($messageList, 'json');
        
        return new JsonResponse($jsonMessageList, Response::HTTP_OK, [], true);
    }

    #[Route('/message/{id}', name: 'detail_message', methods: ['GET'])]
    public function getDetailMessage(Message $message, SerializerInterface $serializer)
    {
        $jsonMessage = $serializer->serialize($message, 'json');
        return new JsonResponse($jsonMessage, Response::HTTP_OK, [], true);
    }
}
