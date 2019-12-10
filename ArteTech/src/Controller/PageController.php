<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     * @param Request $request
     * @return Response
     */
    public function users(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(UserStatus::class);
        $statuses = $repository->findAll();

        return $this->render('page/users.html.twig', [
            'users' => $users,
            'statuses' => $statuses,
            'title' => 'Users'
        ]);
    }

    /**
     * @Route("/companies", name="companies")
     * @return Response
     */
    public function companies()
    {
        return $this->render('page/companies.html.twig', [
            'title' => 'Companies'
        ]);
    }

    /**
     * @Route("/periods", name="periods")
     * @return Response
     */
    public function periods()
    {
        return $this->render('page/periods.html.twig', [
            'title' => 'Periods'
        ]);
    }

    /**
     * @Route("/tasks", name="tasks")
     * @return Response
     */
    public function tasks()
    {
        return $this->render('page/tasks.html.twig', [
            'title' => 'Tasks'
        ]);
    }
}
