<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Group;
use App\Entity\Users;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
    public function deleteCourse(Course $course, EntityManagerInterface $em): JsonResponse 
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

    #[Route('/course/{id}', name: 'createCourse', methods: ['POST'])]
    public function createCourse(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);
    
        if (!isset($data['name']) || !isset($data['disponibility']) || !isset($data['assigned_groups']) || !isset($data['created_by'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }
        
        $professeur = $entityManager->getReference(Users::class, $data['created_by']);        
        if (!$professeur) {
            return new JsonResponse(['message' => 'Professeur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $assigned_groups = $entityManager->getReference(Group::class, $data['assigned_groups']);        
        if (!$assigned_groups) {
            return new JsonResponse(['message' => 'Professeur introuvable.'], Response::HTTP_NOT_FOUND);
        }
    
        $course = new Course();
        $course->setName($data['name']);
        $course->setDisponibility(new \DateTime($data['disponibility']));
        $course->setAssignedGroups($assigned_groups);
        $course->setCreatedBy($professeur);
    
        try {
            $entityManager->persist($course);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la création du cours.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return new JsonResponse(['message' => 'Le cours a été créé avec succès.'], Response::HTTP_CREATED);
    }

    #[Route('/course/{id}', name: 'updateCourse', methods: ['PUT'])]
    public function updateCourse(Course $course, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$course) {
            return new JsonResponse(['message' => 'Cours non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $jsonData = $request->getContent();
        $data = json_decode($jsonData, true);

        if (!isset($data['name']) || !isset($data['disponibility']) || !isset($data['assigned_groups']) || !isset($data['created_by'])) {
            return new JsonResponse(['message' => 'Toutes les informations requises doivent être fournies.'], Response::HTTP_BAD_REQUEST);
        }
    
        $course->setName($data['name']);
        $course->setDisponibility(new \DateTime($data['disponibility']));
        

        $professeur = $entityManager->getReference(Users::class, $data['created_by']);        
        if (!$professeur) {
            return new JsonResponse(['message' => 'Professeur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $assigned_groups = $entityManager->getReference(Group::class, $data['assigned_groups']);        
        if (!$assigned_groups) {
            return new JsonResponse(['message' => 'Professeur introuvable.'], Response::HTTP_NOT_FOUND);
        }
        
        $course->setAssignedGroups($assigned_groups);
        $course->setCreatedBy($professeur);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue lors de la mise à jour du cours.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Le cours a été mis à jour avec succès.'], Response::HTTP_OK);
    }

}
