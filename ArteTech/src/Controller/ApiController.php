<?php

namespace App\Controller;

use App\Entity\PauseLength;
use App\Entity\Period;
use App\Entity\Task;
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
     * @Route("/api/users/getByEmail", name="api_users_getByEmail")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     * @method POST
     */
    public function getUserByEmail(Request $request, SerializerInterface $serializer)
    {
        $data = json_decode($request->getContent(), true);
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneByEmail($data['email']);

        $jsonContent = $serializer->serialize(
            $user,
            'json', ['groups' => 'group1']
        );

        return new Response($jsonContent, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/tasks/setTask", name="api_tasks_setTask")
     * @param Request $request
     * @return Response
     * @method POST
     */
    public function setTask(Request $request)
    {
        $task = new Task();

        $data = json_decode($request->getContent(), true);

        $employeeRepository = $this->getDoctrine()->getRepository(User::class);
        $employee = $employeeRepository->find($data['employee_id']);

        $periodRepository = $this->getDoctrine()->getRepository(Period::class);
        $period = $periodRepository->find($data['period_id']);

        if($data['pause_id']) {
            $pauseRepository = $this->getDoctrine()->getRepository(PauseLength::class);
            $pause = $pauseRepository->find($data['pause_id']);
        }


        $task->setEmployee($employee);
        $task->setPeriod($period);
        $task->setDate(\DateTime::createFromFormat('Y-m-d', $data['date']));
        $task->setStartTime(\DateTime::createFromFormat('Y-m-d H:i:s', $data['date'] . ' ' .$data['time']['start']));
        if($data['time']['end'])
            $task->setEndTime(\DateTime::createFromFormat('Y-m-d H:i:s', $data['date'] . ' ' .$data['time']['end']));
        if($data['pause_id'])
            $task->setPauseLength($pause);
        $task->setMaterialsUsed($data['materials_used']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();

        $response = new JsonResponse(array('status' => '201', 'message' => "Task successfully persisted."));

        return new Response($response->getContent(), 200, ['Content-Type' => 'application/json']);
    }
}
