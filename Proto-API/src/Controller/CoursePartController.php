<?php

namespace App\Controller;

use App\Entity\CoursePart;
use App\Entity\Course;
use App\Repository\CoursePartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CoursePartController extends AbstractController
{
    #[Route('/api/course_part', name: 'app_course_part')]
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

    #[Route('/api/course_part/{id}', name: 'detailCoursePart', methods: ['GET'])]
    public function getDetailCoursePart(CoursePart $course_part, SerializerInterface $serializer)
    {
        try {
            $jsonCoursePart = $serializer->serialize($course_part, 'json');
            return new JsonResponse($jsonCoursePart, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la récupération des détails de la partie de cours.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    #[Route('/api/course_part/{id}', name: 'deleteCoursePart', methods: ['DELETE'])]
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

    #[Route('/api/course_part/{id}', name: 'createCoursePart', methods: ['POST'])]
    public function createCoursePart(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['name']) || !isset($data['course_id']) || !isset($data['content'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $course = $entityManager->getReference(Course::class, $data['course_id']);        
        if (!$course) {
            return new JsonResponse(['message' => 'Cours introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $coursePart = new CoursePart();
        $coursePart->setName($data['name']);
        $coursePart->setCourseId($course);
        $coursePart->setContent($data['content']);

        try {
            $entityManager->persist($coursePart);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la création de la partie de cours.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'La partie de cours a été créée avec succès.'], Response::HTTP_CREATED);
    }

    #[Route('/api/course_part/{id}', name: 'updateCoursePart', methods: ['PUT'])]
    public function updateCoursePart(CoursePart $coursePart, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$coursePart) {
            return new JsonResponse(['message' => 'Partie de cours non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['name']) || !isset($data['course_id']) || !isset($data['content'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }

        $course = $entityManager->getReference(Course::class, $data['course_id']);        
        if (!$course) {
            return new JsonResponse(['message' => 'Cours introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $coursePart->setName($data['name']);
        $coursePart->setCourseId($course);
        $coursePart->setContent($data['content']);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la mise à jour de la partie de cours.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'La partie de cours a été mise à jour avec succès.'], Response::HTTP_OK);
    }
}
