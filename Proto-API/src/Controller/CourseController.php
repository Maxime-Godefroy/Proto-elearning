<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{
    #[Route('/course', name: 'app_course')]
    public function getAllCourse(CourseRepository $courseRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $courseList = $courseRepository->findAll();
            $jsonCourseList = $serializer->serialize($courseList, 'json');
            
            return new JsonResponse($jsonCourseList, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Une erreur est survenue lors de la récupération des cours. Veuillez réessayer plus tard.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/course/{id}', name: 'detailCourse', methods: ['GET'])]
    public function getDetailCourse(Course $course = null, SerializerInterface $serializer)
    {
        if (!$course) {
            return $this->json(['message' => 'Le cours demandé n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $jsonCourse = $serializer->serialize($course, 'json');
            return new JsonResponse($jsonCourse, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Une erreur est survenue lors de la récupération des détails du cours. Veuillez réessayer plus tard.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/course/{id}', name: 'deleteCourse', methods: ['DELETE'])]
    public function deleteMessage(Course $course, EntityManagerInterface $em): JsonResponse 
    {
        if (!$course) {
            return new JsonResponse(['message' => ' Cours non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $em->remove($course);
            $em->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression du cours.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
