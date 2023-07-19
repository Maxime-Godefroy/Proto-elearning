<?php

namespace App\Controller;

use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserGroupController extends AbstractController
{
    #[Route('/user_group', name: 'app_user_group')]
    public function getAllUserGroup(UserGroupRepository $user_groupRepository, SerializerInterface $serializer): JsonResponse
    {
        $user_groupList = $user_groupRepository->findAll();
        $jsonUserGroupList = $serializer->serialize($user_groupList, 'json');
        
        return new JsonResponse($jsonUserGroupList, Response::HTTP_OK, [], true);
    }

    #[Route('/user_group/{id}', name: 'detail_user_group', methods: ['GET'])]
    public function getDetailUserGroup(UserGroup $user_group, SerializerInterface $serializer)
    {
        $jsonUserGroup = $serializer->serialize($user_group, 'json');
        return new JsonResponse($jsonUserGroup, Response::HTTP_OK, [], true);
    }
}
