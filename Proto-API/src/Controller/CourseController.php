<?php

namespace App\Controller;

use App\Entity\Course;
use App\Repository\CourseRepository;
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
        $courseList = $courseRepository->findAll();
        $jsonCourseList = $serializer->serialize($courseList, 'json');
        
        return new JsonResponse($jsonCourseList, Response::HTTP_OK, [], true);
    }

    #[Route('/course/{id}', name: 'detail_course', methods: ['GET'])]
    public function getDetailCourse(Course $course, SerializerInterface $serializer)
    {
        $jsonCourse = $serializer->serialize($course, 'json');
        return new JsonResponse($jsonCourse, Response::HTTP_OK, [], true);
    }
}
