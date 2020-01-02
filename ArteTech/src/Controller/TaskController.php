<?php

namespace App\Controller;

use App\Entity\HourlyRate;
use App\Entity\PauseLength;
use App\Entity\Period;
use App\Entity\Task;
use App\Entity\TransportRate;
use App\Entity\User;
use App\Entity\UserStatus;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TaskController extends AbstractController
{
    private function isAdmin()
    {
        $user = $this->getUser();
        if($user->getStatus()->getStatus() === 'admin') {
            return true;
        } else
            return false;
    }

    /**
     * @Route("/tasks/{id}", name="task_detail")
     * @param $id
     * @return Response
     */
    public function detail($id)
    {
        $task = "";
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Task::class);
            $task = $repository->find($id);

        } else
            $isUnauthorized = true;
        return $this->render('task/detail.html.twig', [
            'title' => 'Taak Details',
            'task' => $task,
            'isUnauthorized' => $isUnauthorized
        ]);
    }

    /**
     * @Route("/tasks_add", name="add_task")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request)
    {
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $task = new Task();

            $form = $this->createFormBuilder($task)
                ->add('employee', EntityType::class, [
                    'label' => 'Werknemer',
                    'class' => User::class,
                    'choice_label' => function ($user) {
                        return ucfirst($user->getFirstname()) . ' ' . ucfirst($user->getLastname());
                    }])
                ->add('period', EntityType::class, [
                    'label' => 'Opdracht',
                    'class' => Period::class,
                    'choice_label' => function ($period) {
                        return ucfirst($period->getName()) . ' - ' . ucfirst($period->getCompany()->getName());
                    }])
                ->add('date', DateType::class, ['widget' => 'single_text', 'label' => 'Datum'])
                ->add('startTime', TimeType::class, ['widget' => 'single_text', 'label' => 'Begin Tijd'])
                ->add('endTime', TimeType::class, ['widget' => 'single_text', 'label' => 'Eind Tijd'])
                ->add('activitiesDone', TextType::class, ['label' => 'Uitgevoerde Activiteiten'])
                ->add('materialsUsed', TextType::class, ['label' => 'Gebruikte Materialen'])
                ->add('kmTraveled', NumberType::class, ['label' => "Km's Afgelegd"])
                ->add('pauseLength', EntityType::class, [
                    'label' => 'Lengte Pause',
                    'class' => PauseLength::class,
                    'choice_label' => function ($pause) {
                        return date("H:i",date_timestamp_get($pause->getTime()));
                    }])
                ->add('save', SubmitType::class, ['label' => 'Opslaan'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();

                return $this->redirect("/tasks");
            }
        } else
            $isUnauthorized = true;

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'Taak Toevoegen',
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="edit_task")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, $id)
    {
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Task::class);
            $task = $repository->find($id);

            $form = $this->createFormBuilder($task)
                ->add('employee', EntityType::class, [
                    'label' => 'Werknemer',
                    'class' => User::class,
                    'choice_label' => function ($user) {
                        return ucfirst($user->getFirstname()) . ' ' . ucfirst($user->getLastname());
                    }])
                ->add('period', EntityType::class, [
                    'label' => 'Opdracht',
                    'class' => Period::class,
                    'choice_label' => function ($period) {
                        return ucfirst($period->getName()) . ' - ' . ucfirst($period->getCompany()->getName());
                    }])
                ->add('date', DateType::class, ['widget' => 'single_text', 'label' => 'Datum'])
                ->add('startTime', TimeType::class, ['widget' => 'single_text', 'label' => 'Begin Tijd'])
                ->add('endTime', TimeType::class, ['widget' => 'single_text', 'label' => 'Eind Tijd'])
                ->add('activitiesDone', TextType::class, ['label' => 'Uitgevoerde Activiteiten'])
                ->add('materialsUsed', TextType::class, ['label' => 'Gebruikte Materialen'])
                ->add('kmTraveled', NumberType::class, ['label' => "Km's Afgelegd"])
                ->add('pauseLength', EntityType::class, [
                    'label' => 'Lengte Pause',
                    'class' => PauseLength::class,
                    'choice_label' => function ($pause) {
                        return date("H:i",date_timestamp_get($pause->getTime()));
                    }])
                ->add('save', SubmitType::class, ['label' => 'Opslaan'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();

                return $this->redirect("/tasks");
            }
        } else
            $isUnauthorized = true;

        return $this->render('task/add.html.twig', [
            'form' => $form->createView(),
            'title' => 'Taak Bewerken',
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

}
