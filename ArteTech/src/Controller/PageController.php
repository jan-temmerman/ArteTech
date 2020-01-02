<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\UserStatus;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private function isAdmin()
    {
        $user = $this->getUser();
        if($user) {
            if ($user->getStatus()->getStatus() === 'admin') {
                return true;
            } else
                return false;
        } else
            return false;
    }

    /**
     * @Route("/users", name="users")
     * @param Request $request
     * @return Response
     */
    public function users(Request $request)
    {
        $statuses = "";
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(UserStatus::class);
            $statuses = $repository->findAll();

        } else
            $isUnauthorized = true;

        return $this->render('page/users.html.twig', [
            'statuses' => $statuses,
            'title' => 'Gebruikers',
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/companies", name="companies")
     * @return Response
     */
    public function companies()
    {
        return $this->render('page/companies.html.twig', [
            'title' => 'Klanten',
            'isUnauthorized' => false,
        ]);
    }

    /**
     * @Route("/periods", name="periods")
     * @return Response
     */
    public function periods()
    {
        return $this->render('page/periods.html.twig', [
            'title' => 'Periodes',
            'isUnauthorized' => false,
        ]);
    }

    /**
     * @Route("/tasks", name="tasks")
     * @return Response
     */
    public function tasks()
    {
        $tasks = "";
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Task::class);
            $tasks = $repository->findAll();

            $tasks = array_reverse($tasks);

        } else
            $isUnauthorized = true;

        return $this->render('page/tasks.html.twig', [
            'tasks' => $tasks,
            'title' => 'Voltooide Taken',
            'isUnauthorized' => false,
        ]);
    }
}
