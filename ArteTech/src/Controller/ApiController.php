<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index()
    {
        $name = 'Jan Temmerman';
        return new JsonResponse(array('name' => $name));
    }

    /**
     * @Route("/api/users", name="api_users")
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function users(SerializerInterface $serializer)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        $jsonContent = $serializer->serialize(
            $users,
            'json', ['groups' => 'group1']
        );

        return new Response($jsonContent, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/users/add", name="api_users_add")
     * @param Request $request
     * @return Response
     * @method POST
     */
    public function addUser(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        return new Response(json_encode($data), 200, ['Content-Type' => 'application/json']);
    }
}