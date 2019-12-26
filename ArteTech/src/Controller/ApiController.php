<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\PauseLength;
use App\Entity\Period;
use App\Entity\Task;
use App\Entity\User;
use DateTimeZone;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

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
            'json', ['groups' => ['user_safe', 'status_safe', 'task_safe']]
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

    /**
     * @Route("/api/periods/getAll", name="api_periods_getAll")
     * @return Response
     * @method GET
     * @throws AnnotationException
     * @throws ExceptionInterface
     */
    public function getPeriods()
    {
        $periodsRepository = $this->getDoctrine()->getRepository(Period::class);
        $period = $periodsRepository->findAll();

        $classMetaDataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );

        $norm = [ new DateTimeNormalizer(), new ObjectNormalizer($classMetaDataFactory)];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($norm, $encoders);

        $jsonContent = $serializer->normalize(
            $period,
            'json', ['groups' => ['period_safe', 'hourlyRate_safe', 'transportRate_safe', 'company_safe']]
        );

        $jsonContent = json_encode($jsonContent);


        return new Response($jsonContent, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/periods/getByCompany", name="api_periods_getByCompany")
     * @param Request $request
     * @return Response
     * @throws AnnotationException
     * @throws ExceptionInterface
     * @method POST
     */
    public function getPeriodsByCompany(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $companyName = $data['name'];

        $companyRepository = $this->getDoctrine()->getRepository(Company::class);
        $company = $companyRepository->findOneByName($companyName);

        $periodsRepository = $this->getDoctrine()->getRepository(Period::class);
        $periods = $periodsRepository->findByCompany($company);

        $classMetaDataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );

        $norm = [ new DateTimeNormalizer(), new ObjectNormalizer($classMetaDataFactory)];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($norm, $encoders);

        $jsonContent = $serializer->normalize(
            $periods,
            'json', ['groups' => ['period_safe', 'hourlyRate_safe', 'transportRate_safe', 'company_safe']]
        );

        $jsonContent = json_encode($jsonContent);


        return new Response($jsonContent, 200, ['Content-Type' => 'application/json']);
    }
}
