<?php

namespace App\Controller;

use App\Entity\CoursePart;
use App\Repository\CoursePartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class CoursePartController extends AbstractController
{
    #[Route('/course_part', name: 'app_course_part')]
    public function getAllCoursePart(CoursePartRepository $course_partRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $course_partList = $course_partRepository->findAll();
            $jsonCoursePartList = $serializer->serialize($course_partList, 'json');
            
            return new JsonResponse($jsonCoursePartList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la récupération des parties de cours.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/course_part/{id}', name: 'detailCoursePart', methods: ['GET'])]
    public function getDetailCoursePart(CoursePart $course_part, SerializerInterface $serializer)
    {
        try {
            $jsonCoursePart = $serializer->serialize($course_part, 'json');
            return new JsonResponse($jsonCoursePart, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la récupération des détails de la partie de cours.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    #[Route('/course_part/{id}', name: 'deleteCoursePart', methods: ['DELETE'])]
    public function deleteMessage(CoursePart $course_part, EntityManagerInterface $em): JsonResponse 
    {
        if (!$course_part) {
            return new JsonResponse(['message' => ' Groupe non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $em->remove($course_part);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression du groupe.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
