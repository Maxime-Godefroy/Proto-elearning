<?php

namespace App\Controller;

use App\Entity\CoursePart;
use App\Repository\CoursePartRepository;
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
        $course_partList = $course_partRepository->findAll();
        $jsonCoursePartList = $serializer->serialize($course_partList, 'json');
        
        return new JsonResponse($jsonCoursePartList, Response::HTTP_OK, [], true);
    }

    #[Route('/course_part/{id}', name: 'detail_course_part', methods: ['GET'])]
    public function getDetailCoursePart(CoursePart $course_part, SerializerInterface $serializer)
    {
        $jsonCoursePart = $serializer->serialize($course_part, 'json');
        return new JsonResponse($jsonCoursePart, Response::HTTP_OK, [], true);
    }
}
