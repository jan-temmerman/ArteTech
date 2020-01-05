<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Period;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\UserStatus;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

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

    private function isClient()
{
    $user = $this->getUser();
    if($user) {
        if ($user->getStatus()->getStatus() === 'client') {
            return true;
        } else
            return false;
    } else
        return false;
}

    private function getUserStatus()
    {
        $user = $this->getUser();
        if($user)
            return $user->getStatus()->getStatus();
        else
            return "guest";
    }
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index()
    {
        $isUnauthorized = false;
        $tasks = "";
        $companies = "";
        $periods = "";

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Company::class);
            $companies = $repository->findBy([], ['id' => 'DESC'], 5, null);
        }

        if($this->isAdmin() || $this->isClient()) {
            $repository = $this->getDoctrine()->getRepository(Task::class);
            $tasks = $repository->findBy([], ['date' => 'DESC'], 5, null);

            $repository = $this->getDoctrine()->getRepository(Period::class);
            $periods = $repository->findBy([], ['id' => 'DESC'], 5, null);
        } else
            $isUnauthorized = true;

        return $this->render('page/index.html.twig', [
            'title' => 'Gebruikers',
            'tasks' => $tasks,
            'companies' => $companies,
            'periods' => $periods,
            'user' => $this->getUser(),
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/users", name="users")
     * @return Response
     */
    public function users()
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
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
        ]);
    }

    /**
     * @Route("/periods", name="periods")
     * @return Response
     */
    public function periods()
    {
        $periods = "";
        $isUnauthorized = false;

        if($this->isAdmin() || $this->isClient()) {
            $repository = $this->getDoctrine()->getRepository(Period::class);
            $periods = $repository->findAll();

            $periods = array_reverse($periods);

        } else
            $isUnauthorized = true;

        return $this->render('page/periods.html.twig', [
            'title' => 'Opdrachten',
            'periods' => $periods,
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
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

        if($this->isAdmin() || $this->isClient()) {
            $repository = $this->getDoctrine()->getRepository(Task::class);
            $tasks = $repository->findAll();

            $tasks = array_reverse($tasks);

        } else
            $isUnauthorized = true;

        return $this->render('page/tasks.html.twig', [
            'tasks' => $tasks,
            'title' => 'Voltooide Taken',
            'isUnauthorized' => $isUnauthorized,
            'userStatus' => $this->getUserStatus()
        ]);
    }

    /**
     * @Route("/companies", name="companies")
     * @return Response
     */
    public function companies()
    {
        $companies = "";
        $isUnauthorized = false;

        if($this->isAdmin()) {
            $repository = $this->getDoctrine()->getRepository(Company::class);
            $companies = $repository->findAll();

            $companies = array_reverse($companies);

        } else
            $isUnauthorized = true;

        return $this->render('page/companies.html.twig', [
            'companies' => $companies,
            'title' => 'Klanten',
            'userStatus' => $this->getUserStatus(),
            'isUnauthorized' => $isUnauthorized,
        ]);
    }
}
