<?php

namespace App\Controller;

use App\Entity\Activities;
use App\Repository\ActivitiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ActivitiesController extends AbstractController
{
    #[Route('/activities', name: 'app_activities')]
    public function getAllActivities(ActivitiesRepository $activitiesRepository, SerializerInterface $serializer): JsonResponse
    {
        $activitiesList = $activitiesRepository->findAll();
        $jsonActivitiesList = $serializer->serialize($activitiesList, 'json');
        
        return new JsonResponse($jsonActivitiesList, Response::HTTP_OK, [], true);
    }

    #[Route('/activities/{id}', name: 'detail_activity', methods: ['GET'])]
    public function getDetailActivity(Activities $activity, SerializerInterface $serializer)
    {
        $jsonActivity = $serializer->serialize($activity, 'json');
        return new JsonResponse($jsonActivity, Response::HTTP_OK, [], true);
    }
}
