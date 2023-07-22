<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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

    
    #[Route('/message/{id}', name: 'createMessage', methods: ['POST'])]
    public function createMessage(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
    
        if (!isset($data['from_user']) || !isset($data['to_user']) || !isset($data['content'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }
    
        $fromUserReference = $entityManager->getReference(Users::class, $data['from_user']);
        $toUserReference = $entityManager->getReference(Users::class, $data['to_user']);
    
        if (!$entityManager->contains($fromUserReference) || !$entityManager->contains($toUserReference)) {
            return new JsonResponse(['message' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }
    
        $message = new Message();
        $message->setFromUser($fromUserReference);
        $message->setToUser($toUserReference);
        $message->setSentAt(new \DateTime());
        $message->setContent($data['content']);
    
        try {
            $entityManager->persist($message);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la création du message.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return new JsonResponse(['message' => 'Le message a été créé avec succès.'], Response::HTTP_CREATED);
    }

    #[Route('/message/{id}', name: 'updateMessage', methods: ['PUT'])]
    public function updateMessage(Message $message, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$message) {
            return new JsonResponse(['message' => 'Message non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['content'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $message->setContent($data['content']);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la mise à jour du message.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Le message a été mis à jour avec succès.'], Response::HTTP_OK);
    }
}
