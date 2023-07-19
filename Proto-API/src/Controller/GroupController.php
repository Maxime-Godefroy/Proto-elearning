<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    #[Route('/group', name: 'app_group')]
    public function getAllGroup(GroupRepository $groupRepository, SerializerInterface $serializer): JsonResponse
    {
        $groupList = $groupRepository->findAll();
        $jsonGroupList = $serializer->serialize($groupList, 'json');
        
        return new JsonResponse($jsonGroupList, Response::HTTP_OK, [], true);
    }

    #[Route('/group/{id}', name: 'detail_group', methods: ['GET'])]
    public function getDetailGroup(Group $group, SerializerInterface $serializer)
    {
        $jsonGroup = $serializer->serialize($group, 'json');
        return new JsonResponse($jsonGroup, Response::HTTP_OK, [], true);
    }
}
