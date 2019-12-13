<?php

namespace App\Controller;

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
        $users = "";
        $statuses = "";
        $isUnauthorized = false;

        if($this->isAdmin()) {

            $repository = $this->getDoctrine()->getRepository(User::class);
            $users = $repository->findAll();

            $repository = $this->getDoctrine()->getRepository(UserStatus::class);
            $statuses = $repository->findAll();

        } else
            $isUnauthorized = true;

        return $this->render('page/users.html.twig', [
            'users' => $users,
            'statuses' => $statuses,
            'title' => 'Users',
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
            'title' => 'Companies',
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
            'title' => 'Periods',
            'isUnauthorized' => false,
        ]);
    }

    /**
     * @Route("/tasks", name="tasks")
     * @return Response
     */
    public function tasks()
    {
        return $this->render('page/tasks.html.twig', [
            'title' => 'Tasks',
            'isUnauthorized' => false,
        ]);
    }
}
